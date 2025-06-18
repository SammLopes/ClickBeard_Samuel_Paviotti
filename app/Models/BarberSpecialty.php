<?php

use Illuminate\Database\Eloquent\Model;

class BarberSpecialty extends Model {
    protected $table = 'barber_specialty';
    
    protected $fillable = [
        'barber_id', 
        'specialty_id', 
        'experience_years', 
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }
}