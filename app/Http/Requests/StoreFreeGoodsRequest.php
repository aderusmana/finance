<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFreeGoodsRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'account' => 'required|string|max:50',
            'cost_center' => 'nullable|string|max:255',
            'request_date' => 'required|date',
            'objectives' => 'required|string|max:500',
            'estimated_potential' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.quantity_required' => 'required|integer|min:1',
            'items.*.quantity_issued' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'Customer Name wajib diisi.',
            'objectives.required' => 'Objectives wajib diisi.',
            'estimated_potential.required' => 'Estimated Potential wajib diisi.',
            'items.required' => 'List produk yang diminta wajib diisi.',
            'items.*.quantity_required.required' => 'Qty Required wajib diisi untuk setiap item.',
            'items.*.quantity_required.min' => 'Qty Required minimal 1.',
        ];
    }
}