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

     public function show($id)
    {
        $user = auth()->user();
        
        $scheduling = Scheduling::with(['barber', 'service'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json($scheduling);
    }


    public function store(Request $request) 
    {   
        $this->validate($request, [
            'barber_id' => 'required|exists:barbers,id',
            'service_id' => 'required|exists:services,id',
            'scheduling_date' => 'required|date|after_or_equal:today',
            'scheduling_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = auth()->user();
        
        $existingScheduling = Scheduling::where('barber_id', $request->barber_id)
            ->where('scheduling_date', $request->scheduling_date)
            ->where('scheduling_time', $request->scheduling_time)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->first();
        
        if($existingScheduling) {
            return response()->json([
                'error' => 'Este horário já está ocupado. Escolha outro horário.'
            ], 422);
        }

        $schedulingDateTime = Carbon::parse($request->scheduling_date . ' ' . $request->scheduling_time);
        if ($schedulingDateTime->isPast()) {
            return response()->json([
                'error' => 'Não é possível agendar para uma data/hora no passado.'
            ], 422);
        }

        $time = Carbon::parse($request->scheduling_time);
        if($time->hour < 8 || $time->hour > 18){
            return response()->json([

            ], 422);
        }

        if($time->minute != 0 && $time->minute != 30){
            return response()->json(
                ['error' => 'Os horários devem ser agendados de 30 em 30 minutos.'],
             422);
        }

        $service = Service::with('specialty')->findOrFail($request->service_id);
        $barber = Barber::with('specialties')->findOrFail($request->barber_id);
           
        if ($service->specialty_id) {
            $hasSpecialty = $barber->specialties->contains('id', $service->specialty_id);
            if (!$hasSpecialty) {
                return response()->json([
                    'error' => 'O barbeiro selecionado não possui a especialidade necessária para este serviço.'
                ], 422);
            }
        }

        if (!$barber->is_active) {
            return response()->json([
                'error' => 'O barbeiro selecionado não está disponível.'
            ], 422);
        }

        $schedulingCreated = Scheduling::create([
            'user_id' => $user->id,
            'barber_id' => $request->barber_id,
            'service_id' => $request->service_id,
            'scheduling_date' => $request->scheduling_date,
            'scheduling_time' => $request->scheduling_time,
            'notes' => $request->notes,
            'status' => 'scheduled'
        ]); 

        $schedulingCreated->load(['barber', 'service']);

        return response()->json([
            'message'=>'Agendamento Cadastrado',
            'Agendamento' => $schedulingCreated
        ]);
    }

    public function update(Request $request,  string $id)
    {
        $user = auth()->user();
        
        $scheduling = Scheduling::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if(!$scheduling){
            return response()->json([
                'error' => 'Agendamento não encontrado.'
            ], 404);
        }

        if ($scheduling->status === 'completed') {
            return response()->json([
                'error' => 'Não é possível alterar agendamentos já concluídos.'
            ], 422);
        }

        $this->validate($request, [
            'scheduling_date' => 'required|date|after_or_equal:today',
            'scheduling_time' => 'required|date_format:H:i',
            'status' => 'in:cancelled'
        ]);

        $datetimeString = Carbon::parse($scheduling->scheduling_date)->format('Y-m-d') . ' ' .
                  Carbon::parse($scheduling->scheduling_time)->format('H:i:s');

        $schedulingDate = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $datetimeString
        );
        $now = Carbon::now();
        $hoursUntilScheduling = $now->diffInHours($schedulingDate, false);
        if ($hoursUntilScheduling < 2){
            return response()->json([
                'error' => 'O cancelamento deve ser feito com pelo menos 2 horas de antecedência.'
            ], 422);
        }

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

        if(!$barber->is_active){
            return response()->json([
                'error' => 'O barbeiro selecionado não está disponível.'
            ], 422);
        }

        $date = $request->date;

        $availableSlots = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            if($hour != 12){
                $time = sprintf('%02d:00', $hour);
                $availableSlots[] = $time;
            }
        }

        if($date === Carbon::today()->toDateString() ){
            
            $cutoffTime = Carbon::now()->addHours(2);
            $availableSlots = array_filter($availableSlots, function($time) use ($cutoffTime){
                $slotTime = Carbon::parse($time);
                return $slotTime->greaterThan($cutoffTime);
            });
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

    public function getBarbersBySpecialty(Request $request)
    {
        $this->validate($request, [
            'specialty_id' => 'required|exists:specialties,id'
        ]);

        $barbers = Barber::with('specialties')
            ->whereHas('specialties', function($query) use ($request) {
                $query->where('specialty_id', $request->specialty_id);
            })
            ->where('is_active', true)
            ->get();

        return response()->json($barbers);
    }
}

