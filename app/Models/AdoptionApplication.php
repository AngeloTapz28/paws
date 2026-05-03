<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdoptionApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_number', 'pet_id', 'adopter_id', 'reviewed_by',
        'applicant_full_name', 'applicant_email', 'applicant_phone',
        'applicant_address', 'housing_type', 'has_yard', 'has_other_pets',
        'other_pets_details', 'has_children', 'children_ages',
        'reason_for_adopting', 'experience_with_pets', 'occupation',
        'working_hours', 'emergency_contact', 'additional_notes',
        'status', 'submitted_at', 'reviewed_at',
        'interview_scheduled_at', 'completed_at',
        'review_notes', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at'            => 'datetime',
            'reviewed_at'             => 'datetime',
            'interview_scheduled_at'  => 'datetime',
            'completed_at'            => 'datetime',
            'has_yard'                => 'boolean',
            'has_other_pets'          => 'boolean',
            'has_children'            => 'boolean',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function adopter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adopter_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'submitted'            => 'primary',
            'under_review'         => 'info',
            'interview_scheduled'  => 'warning',
            'approved'             => 'success',
            'rejected'             => 'danger',
            'withdrawn'            => 'secondary',
            'completed'            => 'dark',
            default                => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted'            => 'Submitted',
            'under_review'         => 'Under Review',
            'interview_scheduled'  => 'Interview Scheduled',
            'approved'             => 'Approved',
            'rejected'             => 'Rejected',
            'withdrawn'            => 'Withdrawn',
            'completed'            => 'Completed',
            default                => ucfirst($this->status),
        };
    }

    // ─────────────────────────────────────────────
    // BOOT — Generate application number
    // ─────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (AdoptionApplication $app) {
            if (empty($app->application_number)) {
                $year  = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $app->application_number = sprintf('PAWS-%d-%05d', $year, $count);
            }
        });
    }
}