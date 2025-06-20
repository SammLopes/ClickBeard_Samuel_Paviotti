<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scheduling;
use App\Models\Specialty;
use Carbon\Carbon;

class Barber extends Model
{
    protected $fillable = [
        'name', 
        'email', 
        'age',
        'phone',
        'hire_date', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hire_date' => 'date'
    ];

    public function scheduling()
    {
        return $this->hasMany(Scheduling::class);
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'barber_specialty')
                    ->wherePivot('is_primary', true)
                    ->withPivot('experience_years', 'is_primary')
                    ->withTimestamps();
    } 
    
    public function primarySpecialty()
    {
        return $this->belongsToMany(Specialty::class, 'barber_specialty')
                    ->wherePivot('is_primary', true)
                    ->withPivot('experience_years', 'is_primary')
                    ->limit(1);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithSpecialty($query, $specialtyId)
    {
        return $query->whereHas('specialties', function($q) use ($specialtyId) {
            $q->where('specialty_id', $specialtyId);
        });
    }

    public function getYearsOfServiceAttribute(){
        return Carbon::parse($this->hire_date)->diffInYears(Carbon::now());
    }
    
    public function getAgeAttribute($value)
    {
        return $value;
    }

      public function hasSpecialty($specialtyId)
    {
        return $this->specialties->contains('id', $specialtyId);
    }

    public function getSpecialtyExperience($specialtyId)
    {
        $specialty = $this->specialties->find($specialtyId);
        return $specialty ? $specialty->pivot->experience_years : 0;
    }

    public function addSpecialty($specialtyId, $experienceYears = 0, $isPrimary = false)
    {
        $this->specialties()->attach($specialtyId, [
            'experience_years' => $experienceYears,
            'is_primary' => $isPrimary
        ]);
    }

    public function removeSpecialty($specialtyId)
    {
        $this->specialties()->detach($specialtyId);
    }
}
