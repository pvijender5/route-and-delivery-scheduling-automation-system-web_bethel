<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $rules = [
            'item_id' => ['required', 'string', 'max:255'],
            'item_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:product_category,id'],
            'category' => ['required', 'string', 'max:255'],
            'unit_type' => ['required', 'string', 'max:255'],
            'per_unit_weight' => ['required', 'string', 'max:255'],
            'expected_quantity' => ['required', 'string', 'max:255'],
        ];

        // Add unique validation for create
        if ($this->isMethod('post')) {
            $rules['item_id'][] = 'unique:products,item_id';
        }

        // Modify unique validation for update
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['item_id'][] = 'unique:products,item_id,' . $this->route('id');
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'The item ID is required.',
            'item_id.unique' => 'This item ID is already in use.',
            'item_name.required' => 'The item name is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'category.required' => 'The category name is required.',
            'unit_type.required' => 'The unit type is required.',
            'per_unit_weight.required' => 'The per unit weight is required.',
            'expected_quantity.required' => 'The expected quantity is required.',
        ];
    }
} 