<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Breed extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pet_category_id', 'name', 'description',
        'average_size', 'average_lifespan', 'is_active'
    ];
    protected $casts = ['is_active' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PetCategory::class, 'pet_category_id');
    }

    public function petCategory(): BelongsTo
{
    return $this->belongsTo(PetCategory::class, 'pet_category_id');
}

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}