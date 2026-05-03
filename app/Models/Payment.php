<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_number', 'adoption_application_id', 'payer_id',
        'recorded_by', 'type', 'amount', 'currency', 'method',
        'status', 'proof_of_payment', 'notes', 'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function application(): BelongsTo {
        return $this->belongsTo(AdoptionApplication::class, 'adoption_application_id');
    }
    public function payer(): BelongsTo      { return $this->belongsTo(User::class, 'payer_id'); }
    public function recordedBy(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
    public function transactions(): HasMany  { return $this->hasMany(Transaction::class); }

    protected static function booted(): void
    {
        static::creating(function (Payment $pay) {
            if (empty($pay->reference_number)) {
                $year  = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $pay->reference_number = sprintf('PAY-%d-%05d', $year, $count);
            }
        });
    }
}