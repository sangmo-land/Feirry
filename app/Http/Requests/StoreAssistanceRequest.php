<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Coup de Pouce FEIRRY — fiche A (consultation) and fiche B (visite),
 * CDC Coup de Pouce V2 §06–§07. The server is the source of truth (§12):
 * nothing sent by the browser about quotas or states is trusted.
 */
class StoreAssistanceRequest extends FormRequest
{
    public const PROJECT_TYPES = ['maison', 'immeuble', 'commerce', 'renovation', 'autre'];

    public const PROGRESS = ['idee', 'plans', 'chantier', 'termine'];

    public const LEVELS = ['plain-pied', 'r+1', 'r+2', 'r+3', 'r+4+', 'autre', 'inconnu'];

    public const BUILDING_TYPES = ['maison', 'immeuble', 'commerce', 'autre'];

    public const PHASES = ['fondations', 'elevation', 'toiture', 'finitions', 'autre'];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($v) => is_string($v) ? trim($v) : $v;

        $this->merge(collect($this->except(['attachments']))->map($clean)->all());

        // « Je ne sais pas » is a valid answer for areas.
        foreach (['land_area', 'footprint_area'] as $f) {
            if ($this->input($f) === 'inconnu') {
                $this->merge([$f => null, "{$f}_unknown" => true]);
            }
        }
    }

    public function rules(): array
    {
        $visit = $this->input('path') === 'visit';
        $cfg = config('site.cdp');

        $common = [
            'path' => ['required', Rule::in(['consultation', 'visit'])],
            'idempotency_key' => ['required', 'string', 'min:16', 'max:64', 'regex:/^[A-Za-z0-9-]+$/'],
            'city' => ['required', 'string', 'min:2', 'max:120'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^(?=(?:\D*\d){9,15}\D*$)[0-9+().\s-]+$/'],
            'email' => ['nullable', 'email:rfc', 'max:180'],
            // Booked slot, Douala time. Whether one is required is decided against the live grid.
            'slot' => ['nullable', 'date_format:Y-m-d H:i'],
            'privacy' => ['accepted'],
            'marketing' => ['nullable', 'boolean'],
            'website' => ['prohibited'],
        ];

        if (! $visit) {
            return $common + [
                'project_type' => ['required', Rule::in(self::PROJECT_TYPES)],
                'progress' => ['required', Rule::in(self::PROGRESS)],
                'land_area' => ['nullable', 'numeric', 'gt:0', 'max:1000000'],
                'footprint_area' => ['nullable', 'numeric', 'gt:0', 'max:1000000'],
                'levels' => ['nullable', Rule::in(self::LEVELS)],
                'description' => ['required', 'string', 'min:10', 'max:3000'],
                'meeting_mode' => ['nullable', Rule::in(['office', 'video'])],
                'attachments' => ['nullable', 'array', 'max:'.$cfg['pdf_max_files']],
                'attachments.*' => ['file', 'max:'.$cfg['pdf_max_kb'], 'extensions:pdf', 'mimetypes:application/pdf'],
            ];
        }

        return $common + [
            'site_location' => ['required', 'string', 'min:3', 'max:300'],
            'gps' => ['nullable', 'string', 'max:120'],
            'project_type' => ['required', Rule::in(self::BUILDING_TYPES)],
            'progress' => ['required', Rule::in(self::PHASES)],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'availability' => ['nullable', 'string', 'max:200'],
            'access_constraints' => ['nullable', 'string', 'max:1500'],
            'danger' => ['nullable', 'boolean'],
            'attachments' => ['nullable', 'array', 'max:'.$cfg['photo_max_files']],
            'attachments.*' => ['file', 'max:'.$cfg['photo_max_kb'], 'extensions:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp'],
        ];
    }

    public function attributes(): array
    {
        return trans('v7.attributes') + trans('v7.cdp_attributes');
    }

    public function messages(): array
    {
        return [
            'phone.regex' => __('v7.errors.phone'),
            'privacy.accepted' => __('v7.errors.privacy'),
            'attachments.*.extensions' => __('v7.errors.file_type'),
            'attachments.*.mimetypes' => __('v7.errors.file_type'),
            'attachments.*.max' => __('v7.errors.file_size'),
            'website.prohibited' => 'Invalid submission.',
        ];
    }
}
