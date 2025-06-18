<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scheduling;
use App\Models\Specialty;
use App\Models\Barber;


class Service extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'specialty_id',
        'price', 
        'duration_minutes', 
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function scheduling()
    {
        return $this->hasMany(Scheduling::class);
    }

    public function specialty(){
        return $this->belongsTo(Specialty::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpecialty($query, $specialtyId){
        return $query->where('specialty_id', $specialtyId);
    }

    public function getQualifiedBarbers()
    {
        if (!$this->specialty_id) {
            return Barber::active()->get();
        }

        return $this->specialty->barbers()
                    ->where('is_active', true)
                    ->get();
    }
}