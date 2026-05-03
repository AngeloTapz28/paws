<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Pet;

class PetObserver
{
    public function created(Pet $pet): void
    {
        // Already logged in controller, but observer can be used for additional hooks
    }

    public function updated(Pet $pet): void
    {
        if ($pet->wasChanged('status') && $pet->status === 'adopted') {
            // Could trigger additional events here
        }
    }

    public function deleting(Pet $pet): void
    {
        // Clean up related records if needed
    }
}