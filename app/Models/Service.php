<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scheduling;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'price', 'duration_minutes', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function appointments()
    {
        return $this->hasMany(Scheduling::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}