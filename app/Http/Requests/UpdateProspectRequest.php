<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProspectRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:RDV à prendre,Date de RDV,OK',
            'rdv_date' => 'required_if:status,Date de RDV,OK|nullable|date',
            'response_type' => 'nullable|in:OUI,NON,DEVIS',
            'quote_number' => 'nullable|string|max:100',
            'is_followup' => 'nullable|in:OUI,NON',
            'note' => 'nullable|string'
        ];
    }
}
