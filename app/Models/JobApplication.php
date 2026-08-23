<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    public const STATUSES = ['received', 'screening', 'interview', 'offer', 'hired', 'rejected'];

    protected $guarded = [];

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }
}
