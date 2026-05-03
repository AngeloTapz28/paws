<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'amount',
        'type',
        'status',
        'notes',
        'gateway_response',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}