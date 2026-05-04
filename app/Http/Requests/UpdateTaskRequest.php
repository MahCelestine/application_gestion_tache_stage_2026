<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
        $task = $this->route('task');
        $hasSubtasks = $task->subtasks()->exists();

        $rules = [
            'label' => 'required|string',
            'due_date' => 'required|date',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'equipe_ids' => 'nullable|array',
        ];

        if(!$hasSubtasks) {
            $rules += [
                'status' => 'required|in:en cours,attente BAT,validé,bloqué',
                'estimated_h' => 'required|numeric|min:0',
                'estimated_m' => 'required|numeric|min:0|max:59',
                'add_actual_h' => 'nullable|numeric|min:0',
                'add_actual_m' => 'nullable|numeric|min:0|max:59',
                'reason_description' => 'required_if:status,bloqué|nullable|string',
            ];
        }

        return $rules;
    }
}
