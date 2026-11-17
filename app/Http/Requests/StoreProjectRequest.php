<?php

namespace App\Http\Requests;

use App\Support\V7\LeadFiles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** The 5-step "Mon projet" form (CDC §08). */
class StoreProjectRequest extends FormRequest
{
    public const NEEDS = ['conception', 'construction', 'renovation', 'suivi', 'autre'];

    public const PHASES = ['idee', 'terrain', 'plans', 'permis', 'travaux'];

    public const LANDS = ['titre', 'en_cours', 'non'];

    public const CONTACT = ['telephone', 'whatsapp', 'email'];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->filled('phone') ? trim((string) $this->input('phone')) : null,
            'email' => $this->filled('email') ? trim((string) $this->input('email')) : null,
        ]);
    }

    public function rules(): array
    {
        return array_merge([
            'need' => ['required', Rule::in(self::NEEDS)],
            'offer' => ['nullable', 'string', 'max:60', 'exists:offers,slug'],
            'location' => ['required', 'string', 'min:2', 'max:180'],
            'programme' => ['nullable', 'string', 'max:500'],
            'phase' => ['nullable', Rule::in(self::PHASES)],
            'land_status' => ['nullable', Rule::in(self::LANDS)],
            'size' => ['nullable', 'string', 'max:120'],
            'budget_range' => ['nullable', 'string', 'max:120'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:30', 'regex:/^(?=(?:\D*\d){9,15}\D*$)[0-9+().\s-]+$/'],
            'email' => ['nullable', 'required_without:phone', 'email:rfc', 'max:180'],
            'contact_preference' => ['required', Rule::in(self::CONTACT)],
            'message' => ['nullable', 'string', 'max:3000'],
            'privacy' => ['accepted'],
            'website' => ['prohibited'],
        ], LeadFiles::rules());
    }

    public function attributes(): array
    {
        return trans('v7.attributes');
    }

    public function messages(): array
    {
        return [
            'phone.regex' => __('v7.errors.phone'),
            'phone.required_without' => __('v7.errors.contact'),
            'email.required_without' => __('v7.errors.contact'),
            'privacy.accepted' => __('v7.errors.privacy'),
            'website.prohibited' => 'Invalid submission.',
        ];
    }
}
