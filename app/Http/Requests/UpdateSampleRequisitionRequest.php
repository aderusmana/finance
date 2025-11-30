<?php

namespace App\Http\Requests;

use App\Models\Master\ItemDetail;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\Requisition; // Import the Requisition model
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSampleRequisitionRequest extends FormRequest
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
    public function rules()
    {
        // Cara paling andal: periksa apakah ada input 'source'.
        // Jika ada, ini adalah submit dari form QA.
        if ($this->has('source')) {
            // Aturan validasi HANYA untuk QA
            return [
                'source'             => 'required|string|max:255',
                'sample_notes'       => 'required|string|max:500',
                'production_date'    => 'required|date',
                'preparation_method' => 'required|string|max:255',
                'description'        => 'required|string',
            ];
        } else {
            // Jika tidak ada 'source', ini adalah edit biasa oleh Marketing/Requester.
            $rules = [
                'customer_id'         => 'required|exists:customers,id',
                'request_date'        => 'required|date',
                'sub_category'        => 'required|string',
                'cost_center'         => 'nullable|string',
                'objectives'          => 'required|string',
                'estimated_potential' => 'required|string',
                'print_batch'         => 'nullable|boolean',
                'items'               => 'required|array|min:1',
                'items.*.quantity_required' => 'required|integer|min:1',
            ];

            // Aturan tambahan jika ini Special Order
            if ($this->input('sub_category') === 'Special Order') {
                $rules = array_merge($rules, [
                    'end_date'            => 'required|date|after_or_equal:request_date',
                    'weight_selection'    => 'required|string',
                    'packaging_selection' => 'required|string',
                    'sample_count'        => 'required|string',
                    'purpose'             => 'required|string',
                    'coa_required'        => 'required|boolean',
                    'shipment_method'     => 'required|string',
                ]);
            }

            return $rules;
        }
    }

    public function attributes(): array
    {
        $attributes = [];
        $items = $this->input('items', []);
        $subCategory = $this->input('sub_category');

        foreach ($items as $id => $itemData) {
            $itemName = "Item with ID {$id}"; // Default name

            if ($subCategory === 'Packaging') {
                $itemDetail = ItemDetail::find($id);
                if ($itemDetail) {
                    $itemName = "[{$itemDetail->item_detail_code}] {$itemDetail->item_detail_name}";
                }
            } else {
                $itemMaster = ItemMaster::find($id);
                if ($itemMaster) {
                    $itemName = "[{$itemMaster->item_master_code}] {$itemMaster->item_master_name}";
                }
            }

            $attributes["items.{$id}.quantity_required"] = "Qty Required for item {$itemName}";
            $attributes["items.{$id}.quantity_issued"] = "Qty Issued for item {$itemName}";
        }

        return $attributes;
    }

     /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'sub_category.required' => 'Sub Category must be selected.',
            'customer_id.required' => 'Customer must be selected.',
            'no_srs.required' => 'SRS Number is required.',
            'no_srs.unique' => 'SRS Number is already registered.',
            'account.required' => 'Account is required.',
            'request_date.required' => 'Request Date is required.',
            'objectives.required' => 'Objectives are required.',
            'estimated_potential.required' => 'Estimated Potential is required.',
            'items.required' => 'At least 1 item must be requested.',
            'items.min' => 'At least 1 item must be requested.',
            'items.*.quantity_required.required' => 'Qty Required is required for each item.',
            'items.*.quantity_required.min' => 'Qty Required must be at least 1.',
            'end_date.required' => 'Sample Completion Date is required for Special Order.',
            'weight_selection.required' => 'Sample Weight is required for Special Order.',
            'packaging_selection.required' => 'Sample Packaging is required for Special Order.',
            'sample_count.required' => 'Sample Count details are required for Special Order.',
            'shipment_method.required' => 'Shipment Method must be selected for Special Order.',
            'coa_required.required' => 'COA Required is required for Special Order.',
            'source.required' => 'Sample source is required for the QA form.',
            'sample_notes.required' => 'Sample notes are required for the QA form.',
            'production_date.required' => 'Production date is required for the QA form.',
            'preparation_method.required' => 'Sample preparation method is required for the QA form.',
            'description.required' => 'Description is required for the QA form.',
        ];
    }
}
