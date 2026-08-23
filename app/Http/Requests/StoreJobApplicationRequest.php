<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['required', 'string', 'min:8', 'max:30'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'website' => ['prohibited'],
        ];
    }
}
