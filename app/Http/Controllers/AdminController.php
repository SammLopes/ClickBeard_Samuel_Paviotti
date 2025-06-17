<?php
namespace App\Http\Controllers;

use App\Models\Scheduling;
use Laravel\Lumen\Routing\Controller as BaseController;
use Carbon\Carbon;

class AdminController extends BaseController
{
     public function __construct()
    {
        $this->middleware('auth');
    }

    public function todayScheduling()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $scheduling = Scheduling::with(['user', 'barber', 'service'])
            ->today()
            ->orderBy('appointment_time')
            ->get();

        return response()->json([
            'date' => Carbon::today()->format('Y-m-d'),
            'total' => $scheduling->count(),
            'agendamentos' => $scheduling
        ]);
    }

    public function futuresScheduling() 
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $scheduling = Scheduling::with(['user', 'barber', 'service'])
            ->future()
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return response()->json([
            'total' => $scheduling->count(),
            'agendamentos' => $scheduling
        ]);
    }

    public function schedulingByDate($date) 
    {
         $user = auth()->user();
        
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $scheduling = Scheduling::with(['user', 'barber', 'service'])
            ->whereDate('scheduling_date', $date)
            ->orderBy('scheduling_time')
            ->get();

        return response()->json([
            'date' => $date,
            'total' => $schedulings->count(),
            'Schedulings' => $schedulings
        ]);
    }
}