<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccinationRecord extends Model
{
    protected $fillable = [
        'pet_id', 'administered_by', 'vaccine_name', 'manufacturer',
        'batch_number', 'date_administered', 'next_due_date',
        'is_booster', 'notes',
    ];

    protected $casts = [
        'date_administered' => 'date',
        'next_due_date'     => 'date',
        'is_booster'        => 'boolean',
    ];

    public function pet(): BelongsTo          { return $this->belongsTo(Pet::class); }
    public function administeredBy(): BelongsTo {
        return $this->belongsTo(User::class, 'administered_by');
    }
}