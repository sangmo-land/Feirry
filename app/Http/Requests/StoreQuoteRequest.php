<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'company' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['required', 'string', 'min:8', 'max:30'],
            'service_id' => ['nullable', 'exists:services,id'],
            'project_type' => ['required', 'string', 'max:60'],
            'budget_range' => ['nullable', 'string', 'max:60'],
            'timeline' => ['nullable', 'string', 'max:60'],
            'location' => ['required', 'string', 'max:180'],
            'surface_m2' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,dwg,zip'],
            // Honeypot: bots fill it, humans never see it.
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'website.prohibited' => 'Invalid submission.',
        ];
    }
}
