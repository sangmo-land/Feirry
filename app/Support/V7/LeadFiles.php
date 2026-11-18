<?php

namespace App\Support\V7;

use Illuminate\Http\UploadedFile;

/**
 * Uploaded documents for leads (CDC §09): private disk, random file names,
 * one folder per request reference. Never served from the document root.
 */
class LeadFiles
{
    public const EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'dwg'];

    public const MAX_KB = 10240;

    public const MAX_FILES = 5;

    /** Validation rules shared by every form that accepts documents. */
    public static function rules(): array
    {
        return [
            'attachments' => ['nullable', 'array', 'max:'.self::MAX_FILES],
            'attachments.*' => [
                'file',
                'max:'.self::MAX_KB,
                'extensions:'.implode(',', self::EXTENSIONS),
                'mimetypes:application/pdf,image/jpeg,image/png,image/vnd.dwg,application/acad,application/x-acad,application/x-dwg,application/dwg,application/octet-stream',
                // The content must match the extension: a renamed executable or script is refused.
                function (string $attribute, mixed $file, \Closure $fail) {
                    if ($file instanceof UploadedFile && ! self::contentMatchesExtension($file)) {
                        $fail(trans('v7.errors.file_type'));
                    }
                },
            ],
        ];
    }

    /** @param  UploadedFile[]  $files */
    /** Checks the file signature ("magic bytes") against the declared extension. */
    public static function contentMatchesExtension(UploadedFile $file): bool
    {
        $head = (string) @file_get_contents($file->getRealPath(), false, null, 0, 8);

        return match (strtolower($file->getClientOriginalExtension())) {
            'pdf' => str_starts_with($head, '%PDF-'),
            'jpg', 'jpeg' => str_starts_with($head, "\xFF\xD8\xFF"),
            'png' => str_starts_with($head, "\x89PNG\r\n\x1A\n"),
            'dwg' => str_starts_with($head, 'AC10') || str_starts_with($head, 'AC1.') || str_starts_with($head, 'AC2'),
            default => false,
        };
    }

    public static function store(array $files, string $folder): array
    {
        return collect($files)
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->isValid())
            ->map(fn (UploadedFile $file) => [
                'path' => $file->store($folder, 'local'),
                // Shown in the admin only; never used to build a path.
                'name' => mb_substr(preg_replace('/[^\pL\pN ._()-]/u', '_', $file->getClientOriginalName()), 0, 120),
                'size' => $file->getSize(),
            ])
            ->values()
            ->all();
    }
}
