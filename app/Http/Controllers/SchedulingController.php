<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Scheduling;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class SchedulingController extends BaseController{

     public function index()
    {
        $user = auth()->user();
        
        $scheduling = Scheduling::with(['barber', 'service'])
            ->where('user_id', $user->id)
            ->orderBy('scheduling_date', 'desc')
            ->orderBy('scheduling_time', 'desc')
            ->get();

        return response()->json($scheduling);
    }

    public function store(Request $request) 
    {   
         $this->validate($request, [
            'barber_id' => 'required|exists:barbers,id',
            'service_id' => 'required|exists:services,id',
            'scheduling_date' => 'required|date|after_or_equal:today',
            'scheduling_time' => 'required|date_format:H:i'
        ]);

        $user = auth()->user();
        
        $existingScheduling = Scheduling::where('barber_id', $request->barber_id)
            ->where('scheduling_date', $request->scheduling_date)
            ->where('scheduling_time', $request->scheduling_time)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->first();
        
        if(!$existingScheduling) {
            return response()->json([
                'error' => 'Este horário já está ocupado. Escolha outro horário.'
            ], 422);
        }

        $schedulingDateTime = Carbon::parse($request->$scheduling_date . ' ' . $request->$scheduling_time);
        if ($schedulingDateTime->isPast()) {
            return response()->json([
                'error' => 'Não é possível agendar para uma data/hora no passado.'
            ], 422);
        }

        $schedulingCreated = Scheduling::create([
            'user_id' => $user->id,
            'barber_id' => $request->barber_id,
            'service_id' => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
            'status' => 'scheduled'
        ]); 

        $schedulingCreated->load(['barber', 'service']);

        return response()->json([
            'message'=>'Agendamento Cadastrado',
            'Agendamento' => $scheduling
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        
        $scheduling = Scheduling::with(['barber', 'service'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json($scheduling);
    }

    public function update(Request $request,  string $id)
    {
        $user = auth()->user();
        
        $scheduling = Scheduling::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($scheduling->status === 'completed') {
            return response()->json([
                'error' => 'Não é possível alterar agendamentos já concluídos.'
            ], 422);
        }

        $this->validate($request, [
            'status' => 'in:cancelled'
        ]);

        $scheduling->update(['status' => $request->status]);
        $scheduling->load(['barber', 'service']);

        return response()->json([
            'message' => 'Agendamento atualizado com sucesso',
            'agendamento' => $scheduling
        ]);
    }

     public function availableSlots(Request $request)
    {
        $this->validate($request, [
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        $barber = Barber::findOrFail($request->barber_id);
        $date = $request->date;

        $availableSlots = [];
        for ($hour = 9; $hour <= 18; $hour++) {
            $time = sprintf('%02d:00', $hour);
            $availableSlots[] = $time;
        }

        $bookedSlots = Scheduling::where('barber_id', $request->barber_id)
            ->where('scheduling_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->pluck('scheduling_time')
            ->map(function($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        $availableSlots = array_diff($availableSlots, $bookedSlots);

        return response()->json([
            'date' => $date,
            'barber' => $barber->name,
            'available_slots' => array_values($availableSlots)
        ]);
    }
}

