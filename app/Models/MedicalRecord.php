<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pet_id', 'vet_id', 'examination_date', 'diagnosis',
        'symptoms', 'treatment', 'medications', 'weight_at_exam',
        'health_status', 'fit_for_adoption', 'notes', 'attachment',
        'next_checkup_date',
    ];

    protected $casts = [
        'examination_date'   => 'date',
        'next_checkup_date'  => 'date',
        'fit_for_adoption'   => 'boolean',
    ];

    public function pet(): BelongsTo   { return $this->belongsTo(Pet::class); }
    public function vet(): BelongsTo   { return $this->belongsTo(User::class, 'vet_id'); }
}