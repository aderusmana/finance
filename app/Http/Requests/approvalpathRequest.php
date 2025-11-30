<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class approvalpathRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|string|max:15',
            'sub_category_id' => 'nullable|string|max:15',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'required|string|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required.',
            'sub_category_id.string' => 'Sub-category must be a string.',
            'approvers.required' => 'At least one approver must be selected.',
            'approvers.*.exists' => 'Selected approver does not exist.',
        ];
    }
}
