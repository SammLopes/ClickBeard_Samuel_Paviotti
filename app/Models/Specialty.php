<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barber;
use App\Models\Service;

class Specialty extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function barbers()
    {
        return $this->belongsToMany(Barber::class, 'barber_specialty')
                    ->withPivot('experience_years', 'is_primary')
                    ->withTimestamps();
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getBarbersByExperience()
    {
        return $this->barbers()
                    ->orderByPivot('experience_years', 'desc')
                    ->get();
    }

    public function getExpertBarbers($minExperience = 3)
    {
        return $this->barbers()
                    ->wherePivot('experience_years', '>=', $minExperience)
                    ->get();
    }
}