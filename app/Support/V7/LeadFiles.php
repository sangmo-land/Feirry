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
            ],
        ];
    }

    /** @param  UploadedFile[]  $files */
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
