<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'pet_category_id', 'breed_id', 'gender',
        'date_of_birth', 'weight', 'size', 'color', 'description',
        'special_needs', 'is_vaccinated', 'is_neutered', 'is_microchipped',
        'status', 'adoption_fee_type', 'adoption_fee', 'primary_image',
        'images', 'added_by', 'vet_approved_by', 'is_vet_approved',
        'is_admin_approved', 'listed_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'    => 'date',
            'listed_at'        => 'datetime',
            'images'           => 'array',
            'is_vaccinated'    => 'boolean',
            'is_neutered'      => 'boolean',
            'is_microchipped'  => 'boolean',
            'is_vet_approved'  => 'boolean',
            'is_admin_approved'=> 'boolean',
            'adoption_fee'     => 'decimal:2',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(PetCategory::class, 'pet_category_id');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function vetApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_approved_by');
    }

    public function adoptionApplications(): HasMany
    {
        return $this->hasMany(AdoptionApplication::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function vaccinationRecords(): HasMany
    {
        return $this->hasMany(VaccinationRecord::class);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getPrimaryImageUrlAttribute(): string
    {
        if ($this->primary_image) {
            return asset('storage/' . $this->primary_image);
        }
        return asset('images/pet-placeholder.png');
    }

    public function getAgeAttribute(): string
    {
        if (!$this->date_of_birth) return 'Unknown';

        $diff = now()->diff($this->date_of_birth);
        if ($diff->y > 0) return $diff->y . ' yr' . ($diff->y > 1 ? 's' : '');
        if ($diff->m > 0) return $diff->m . ' mo' . ($diff->m > 1 ? 's' : '');
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available'       => 'success',
            'pending'         => 'warning',
            'adopted'         => 'primary',
            'under_treatment' => 'info',
            'not_available'   => 'secondary',
            'quarantine'      => 'danger',
            default           => 'secondary',
        };
    }

    public function getAdoptionFeeDisplayAttribute(): string
    {
        if ($this->adoption_fee_type === 'free') return 'Free';
        if ($this->adoption_fee_type === 'donation') return 'Donation';
        return '₱ ' . number_format($this->adoption_fee, 2);
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                     ->where('is_admin_approved', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('color', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}