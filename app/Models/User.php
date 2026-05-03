<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'avatar',
        'status',
        'last_login_at',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'date_of_birth'     => 'date',
            'password'          => 'hashed',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
                    ->withPivot(['assigned_at', 'assigned_by'])
                    ->withTimestamps();
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'added_by');
    }

    public function adoptionApplications(): HasMany
    {
        return $this->hasMany(AdoptionApplication::class, 'adopter_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'vet_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    public function systemNotifications(): HasMany
    {
        return $this->hasMany(SystemNotification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ─────────────────────────────────────────────
    // ROLE HELPERS
    // ─────────────────────────────────────────────

    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return $this->roles->whereIn('name', $role)->isNotEmpty();
        }
        return $this->roles->where('name', $role)->isNotEmpty();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function isAdopter(): bool
    {
        return $this->hasRole('adopter');
    }

    public function isVet(): bool
    {
        return $this->hasRole('vet');
    }

    public function isAdminOrStaff(): bool
    {
        return $this->hasRole(['admin', 'staff']);
    }

    public function getPrimaryRole(): ?Role
    {
        return $this->roles->first();
    }

    public function getPrimaryRoleNameAttribute(): string
    {
        return $this->getPrimaryRole()?->name ?? 'guest';
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $initial = strtoupper(substr($this->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$this->name}&background=4f46e5&color=fff&size=128";
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->systemNotifications()->whereNull('read_at')->count();
    }
}