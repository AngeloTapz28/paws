<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'icon', 'description', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function breeds(): HasMany
    {
        return $this->hasMany(Breed::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}