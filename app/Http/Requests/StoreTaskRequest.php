<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'label' => 'required|string',
            'estimated_h' => 'required|numeric|min:0',
            'estimated_m' => 'required|numeric|min:0|max:59',
            'due_date' => 'required|date',
            'quote_number' => 'nullable|string|max:100',
            'billing_info' => 'nullable|string|max:100',
            'equipe_ids' => 'nullable|array',
            'client_id' => 'required_without:new_client_name|nullable|exists:clients,id|prohibits:new_client_name',
            'new_client_name' => 'required_without:client_id|nullable|string|max:255|prohibits:client_id',
            'prospect_id' => 'nullable|exists:prospects,id',
            'subtasks' => 'nullable|array',
            'subtasks.*.label' => 'required|string|max:255',
            'subtasks.*.estimated_h' => 'required|numeric|min:0',
            'subtasks.*.estimated_m' => 'required|numeric|min:0|max:59',
            'subtasks.*.due_date' => 'required|date',
            'subtasks.*.equipe_ids' => 'nullable|array',
        ];
    }
}
