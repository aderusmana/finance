<?php

namespace App\Services;

use App\Models\Customer\Customer;
use App\Models\Customer\CustomerItem;
use App\Models\Customer\CustomerFile;
use App\Models\User;
use App\Models\Master\ApprovalLog;
use App\Models\Master\ApprovalPath;
use App\Notifications\SystemNotification;
use App\Traits\ApprovalTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CustomerService
{
    use ApprovalTrait;

    public function createCustomer(array $data, $request)
    {
        return DB::transaction(function () use ($data, $request) {
            $user = Auth::user();

            $isBgActive = $data['bank_garansi'] === 'YA';

            if ($isBgActive) {
                $year = date('Y');
                $monthRoman = $this->getRomanMonth(date('n'));
                $initials = $this->generateInitials($data['name']);

                $maxSequence = 0;
                $existingNumbers = Customer::where('no_pkd', 'LIKE', "%/{$year}")
                                    ->pluck('no_pkd')->toArray();

                foreach ($existingNumbers as $no) {
                    $parts = explode('/', $no);
                    if (isset($parts[0]) && is_numeric($parts[0])) {
                        $seq = intval($parts[0]);
                        if ($seq > $maxSequence) $maxSequence = $seq;
                    }
                }

                $nextSequence = $maxSequence + 1;
                $pkdNumber = '';
                do {
                    $sequenceStr = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
                    $pkdNumber = sprintf("%s/PKD-%s/%s/%s", $sequenceStr, $initials, $monthRoman, $year);
                    $exists = Customer::where('no_pkd', $pkdNumber)->exists();
                    if ($exists) $nextSequence++;
                } while ($exists);

                $data['no_pkd'] = $pkdNumber;
                $data['credit_limit'] = 0;
            } else {
                $data['no_pkd'] = null;
                $rawLimit = $data['credit_limit'];
                $cleanLimit = str_replace(['.', ','], '', $rawLimit);
                $data['credit_limit'] = (float) $cleanLimit;
            }

            if (isset($data['top_calc'])) {
                $data['top_calc'] = $data['top_calc'];
            } else {
                $termVal = $data['term_of_payment'];
                $data['top_calc'] = ($termVal === 'CBD') ? 0 : (int) $termVal;
            }

            if(empty($data['lead_time'])) {
                $data['lead_time'] = 0;
            }

            $grandTotal = 0;
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $qty = (float) ($item['quantity'] ?? 0);
                    $price = (float) ($item['price'] ?? 0);
                    $grandTotal += ($qty * $price);
                }
            }
            $data['customer_total'] = $grandTotal;
            $data['created_by'] = $user->id;
            $data['status'] = 'Active';

            $customer = Customer::create($data);

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['item_name']) && !empty($item['quantity'])) {
                        CustomerItem::create([
                            'customer_id' => $customer->id,
                            'item_name'   => $item['item_name'],
                            'quantity'    => $item['quantity'],
                            'price'       => $item['price'] ?? 0,
                        ]);
                    }
                }
            }

            $storageFolder = 'customer_files/' . $customer->id;
            $fileData = [
                'customer_id' => $customer->id,
                'npwp_file' => $request->hasFile('file_npwp') ? $request->file('file_npwp')->store($storageFolder, 'public') : null,
                'nib_siup_file' => $request->hasFile('file_nib') ? $request->file('file_nib')->store($storageFolder, 'public') : null,
                'ktp_file' => $request->hasFile('file_ktp') ? $request->file('file_ktp')->store($storageFolder, 'public') : null,
                'akte_file' => $request->hasFile('file_akte') ? $request->file('file_akte')->store($storageFolder, 'public') : null,
            ];
            CustomerFile::create($fileData);

            $subCategory = ($data['term_of_payment'] === 'CBD') ? 'CBD' : null;

            $this->generateApprovalLogs($user, $customer->id, 'Customer', $subCategory);

            $firstLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $customer->id)
                ->orderBy('level', 'asc')
                ->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    $customer->update(['route_to' => $firstApprover->name, 'status_approval' => 'Pending']);

                    try {
                        Notification::send($firstApprover, new SystemNotification(
                            'Butuh Persetujuan',
                            "Customer Baru <b>{$customer->name}</b> menunggu persetujuan Anda.",
                            route('customers.approval'),
                            'ph-signature',
                            'warning'
                        ));
                    } catch (\Exception $e) { Log::error("Notif Error: " . $e->getMessage()); }
                }
            } else {
                $customer->update(['status_approval' => 'Completed', 'route_to' => 'Finished']);
            }

            activity()
                ->causedBy($user)
                ->performedOn($customer)
                ->useLog('customer')
                ->event('create')
                ->log("Created new customer: {$customer->name}");

            return $customer;
        });
    }

    private function getRomanMonth($month) {
        $map = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        return $map[$month] ?? 'I';
    }

    private function generateInitials($string) {
        $string = strtoupper(preg_replace('/[^A-Z0-9\s]/', '', $string));
        $words = explode(' ', $string);
        $initials = '';
        foreach ($words as $w) {
            $initials .= $w[0] ?? '';
        }
        return substr($initials, 0, 5);
    }
}
