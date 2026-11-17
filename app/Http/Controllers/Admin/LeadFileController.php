<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\QuoteRequest;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves a lead's private document to a signed-in administrator only.
 * Files live on the private disk and have no public URL (CDC §09, UPL-01).
 */
class LeadFileController extends Controller
{
    public function __invoke(string $type, int $id, int $index): StreamedResponse
    {
        // Only administrators have accounts: the public site has no sign-up.
        $user = auth()->user();
        abort_unless($user !== null, 403);

        if (method_exists($user, 'canAccessPanel')) {
            abort_unless($user->canAccessPanel(Filament::getDefaultPanel()), 403);
        }

        $lead = match ($type) {
            'quote' => QuoteRequest::findOrFail($id),
            'assistance' => AssistanceRequest::findOrFail($id),
            default => abort(404),
        };

        $file = ($lead->attachments ?? [])[$index] ?? abort(404);
        abort_unless(Storage::disk('local')->exists($file['path']), 404);

        return Storage::disk('local')->download($file['path'], $file['name'] ?? basename($file['path']));
    }
}
