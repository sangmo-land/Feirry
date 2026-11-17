<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Support\V7\LeadFiles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Coup de Pouce FEIRRY: free file consultation or technical site visit (CDC §08). */
class StoreAssistanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $campaign = Campaign::current();
        $visit = $this->input('path') === 'visit';

        return array_merge([
            'path' => ['required', Rule::in(array_values(array_filter([
                $campaign?->consultation_enabled ? 'consultation' : null,
                $campaign?->visit_enabled ? 'visit' : null,
            ])) ?: ['consultation', 'visit'])],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:30', 'regex:/^(?=(?:\D*\d){9,15}\D*$)[0-9+().\s-]+$/'],
            'email' => ['nullable', 'required_without:phone', 'email:rfc', 'max:180'],
            'city' => [$visit ? 'nullable' : 'required', 'string', 'max:120'],
            'project_type' => ['nullable', Rule::in(StoreProjectRequest::NEEDS)],
            'description' => [$visit ? 'nullable' : 'required', 'string', 'max:3000'],
            'meeting_mode' => [$visit ? 'nullable' : 'required', Rule::in(['office', 'video'])],
            'site_location' => [$visit ? 'required' : 'nullable', 'string', 'max:300'],
            'objective' => [$visit ? 'required' : 'nullable', 'string', 'max:1500'],
            'access_constraints' => ['nullable', 'string', 'max:1500'],
            'availability' => [$visit ? 'required' : 'nullable', 'string', 'max:200'],
            'privacy' => ['accepted'],
            'website' => ['prohibited'],
        ], LeadFiles::rules(), $campaign?->documents_required && ! $visit
            ? ['attachments' => ['required', 'array', 'min:1', 'max:'.LeadFiles::MAX_FILES]]
            : []);
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
