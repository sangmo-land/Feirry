<?php

use App\Support\V7\ContentInstaller;
use Illuminate\Database\Migrations\Migration;

/*
| Puts the V7 content in place on deploy (`composer deploy` runs migrate).
| Unpublishes the demonstration records rather than deleting them: they stay
| in the admin and can be restored one by one.
*/
return new class extends Migration
{
    public function up(): void
    {
        ContentInstaller::install();
    }

    public function down(): void
    {
        // Content changes are not reversed automatically; restore from the
        // pre-deploy database backup (see docs/PUBLICATION.md).
    }
};
