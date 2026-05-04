<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubtaskRequest extends FormRequest
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
            'task_id' => 'required|exists:tasks,id',
            'label' => 'required|string',
            'due_date' => 'required|date',
            'estimated_h' => 'required|numeric|min:0',
            'estimated_m' => 'required|numeric|min:0|max:59',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'equipe_ids' => 'nullable|array',
        ];
    }
}
