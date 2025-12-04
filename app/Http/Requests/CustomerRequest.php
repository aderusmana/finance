<?php

namespace App\Http\Requests;

use App\Models\Master\ItemDetail;
use App\Models\Master\ItemMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $isSpecialOrder = $this->input('sub_category') === 'Special Order';

        $user = Auth::user();
        $userAccount = $user->department->code ?? null;
        $allowedSubCategories = [];

        if ($user->roles()->where('name', 'super-admin')->exists()) {
            $allowedSubCategories = ['Packaging', 'Finished Goods', 'Special Order'];
        } else {
            if (in_array($userAccount, ['5300', '5302'])) {
                $allowedSubCategories[] = 'Packaging';
                $allowedSubCategories[] = 'Finished Goods';
            }
            if ($userAccount === '5300') {
                $allowedSubCategories[] = 'Special Order';
            }
        }
        $allowedSubCategories = array_unique($allowedSubCategories);

        return [
            // Aturan Umum
            'sub_category'          => ['required', Rule::in($allowedSubCategories)],
            'customer_id'           => 'required|exists:customers,id',
            'account'               => 'required|string|max:255',
            'cost_center'           => 'nullable|string|max:255',
            'request_date'          => 'required|date',
            'objectives'            => 'required|string',
            'estimated_potential'   => 'required|string',
            'print_batch'           => 'required_if:sub_category,Packaging|boolean',

            'items'                         => 'required|array|min:1',
            'items.*.quantity_required'     => 'required|integer|min:1',
            'items.*.quantity_issued'       => 'required|integer|min:0',

            'end_date'              => 'required_if:sub_category,Special Order|nullable|date',
            'weight_selection'      => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'packaging_selection'   => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'sample_count'          => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'purpose'               => 'required_if:sub_category,Special Order|nullable|string',
            'shipment_method'       => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'coa_required'          => 'required_if:sub_category,Special Order|nullable|boolean',

            'sample_origin'             => 'nullable|string|max:255',
            'sample_description_batch'  => 'nullable|string|max:255',
            'sample_description_wb'     => 'nullable|string|max:255',
            'sample_description_tank'   => 'nullable|string|max:255',
            'production_date'           => 'nullable|date',
            'sample_preparation'        => 'nullable|string|max:255',
            'description'               => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        $attributes = [];
        $items = $this->input('items', []);
        $subCategory = $this->input('sub_category');

        foreach ($items as $id => $itemData) {
            $itemName = "Item with ID {$id}"; // Nama default jika item tidak ditemukan

            if ($subCategory === 'Packaging') {
                // Cari di ItemDetail jika sub-kategori adalah Packaging
                $itemDetail = ItemDetail::find($id);
                if ($itemDetail) {
                    $itemName = "[{$itemDetail->item_detail_code}] {$itemDetail->item_detail_name}";
                }
            } else {
                // Cari di ItemMaster untuk Finished Goods & Special Order
                $itemMaster = ItemMaster::find($id);
                if ($itemMaster) {
                    $itemName = "[{$itemMaster->item_master_code}] {$itemMaster->item_master_name}";
                }
            }

            // Definisikan "nama panggilan" untuk setiap atribut item
            $attributes["items.{$id}.quantity_required"] = "Qty Required untuk item {$itemName}";
            $attributes["items.{$id}.quantity_issued"] = "Qty Issued untuk item {$itemName}";
        }

        return $attributes;
    }

    /**
     * Get the custom error messages for validator failures.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'sub_category.required' => 'The Sub Category field is required.',
            'sub_category.in' => 'The selected Sub Category is not valid for your department.',
            'customer_id.required' => 'The Customer field is required.',
            'account.required' => 'The Account field is required.',
            'request_date.required' => 'The Request Date field is required.',
            'objectives.required' => 'The Objectives field is required.',
            'estimated_potential.required' => 'The Estimated Potential field is required.',
            'items.required' => 'At least one item must be added.',
            'items.min' => 'At least one item must be added.',
            'items.*.quantity_required.required' => 'Qty Required must be filled for each item.',
            'items.*.quantity_required.min' => 'Qty Required must be at least 1.',

            // Pesan untuk Special Order
            'end_date.required_if' => 'Sample Completion Date is required for Special Orders.',
            'weight_selection.required_if' => 'Sample Weight is required for Special Orders.',
            'packaging_selection.required_if' => 'Sample Packaging is required for Special Orders.',
            'sample_count.required_if' => 'Samples Count is required for Special Orders.',
            'purpose.required_if' => 'Sample Purpose is required for Special Orders.',
            'shipment_method.required_if' => 'Shipment Method is required for Special Orders.',
            'coa_required.required_if' => 'Certificate of Analysis option is required for Special Orders.',
        ];
    }
}
