<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Barber;
use App\Models\Service;

class Scheduling extends Model
{
    protected $fillable = [
        'user_id', 
        'barber_id', 
        'service_id', 
        'scheduling_date', 
        'scheduling_time', 
        'status', 
        'notes'
    ];

    protected $casts = [
        'scheduling_date' => 'date',
        'scheduling_time' => 'datetime:H:i'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduling_date', Carbon::today());
    }

    public function scopeFuture($query)
    {
        return $query->where('scheduling_date', '>=', Carbon::today());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getFullDateTimeAttribute()
    {
        return Carbon::parse($this->scheduling_date->format('Y-m-d') . ' ' . $this->scheduling_time);
    }
}