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

    // YYYY-MM-DD -> formato de data
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

    public function confirmScheduling(Request $request, $id)
    {
        $scheduling = Scheduling::with(
            [
                'user', 
                'barber', 
                'service'
            ])
            ->findOrFail($id);

        if ($scheduling->status !== 'scheduled') {
            return response()->json([
                'error' => 'Apenas agendamentos com status "scheduled" podem ser confirmados.'
            ], 422);
        }

        $scheduling->update(['status' => 'confirmed']);

        return response()->json([
            'message' => 'Agendamento confirmado com sucesso.',
            'agendamento' => $scheduling
        ]);
    }

    public function completeScheduling(Request $request, $id)
    {
        $scheduling = Scheduling::with(
            [
                'user', 
                'barber', 
                'service'
            ])
            ->findOrFail($id);

        if (!in_array($scheduling->status, ['scheduled', 'confirmed'])) {
            return response()->json([
                'error' => 'Apenas agendamentos agendados ou confirmados podem ser marcados como concluídos.'
            ], 422);
        }

        $scheduling->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Agendamento marcado como concluído.',
            'agendamento' => $scheduling
        ]);
    }

    public function cancelScheduling(Request $request, $id)
    {
        $this->validate($request, [
            'reason' => 'nullable|string|max:500'
        ]);

        $scheduling = Scheduling::with(['user', 'barber', 'service'])
            ->findOrFail($id);

        if ($scheduling->status === 'completed') {
            return response()->json([
                'error' => 'Não é possível cancelar agendamentos já concluídos.'
            ], 422);
        }

        if ($scheduling->status === 'cancelled') {
            return response()->json([
                'error' => 'Este agendamento já foi cancelado.'
            ], 422);
        }

        $updateData = ['status' => 'cancelled'];
        if ($request->reason) {
            $updateData['notes'] = ($scheduling->notes ? $scheduling->notes . "\n\n" : '') . "CANCELADO PELA ADMINISTRAÇÃO: " . $request->reason;
        }

        $scheduling->update($updateData);

        return response()->json([
            'message' => 'Agendamento cancelado com sucesso.',
            'agendamento' => $scheduling
        ]);
    }

     public function schedulingReport(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:scheduled,confirmed,completed,cancelled',
            'barber_id' => 'nullable|exists:barbers,id'
        ]);

        $query = Scheduling::with(
            [
                'user', 
                'barber', 
                'service'
            ])
            ->whereBetween('scheduling_date', [$request->start_date, $request->end_date]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->barber_id) {
            $query->where('barber_id', $request->barber_id);
        }

        $schedulings = $query->orderBy('scheduling_date', 'asc')
                            ->orderBy('scheduling_time', 'asc')
                            ->get();

        $stats = [
            'total' => $schedulings->count(),
            'by_status' => $schedulings->groupBy('status')->map->count(),
            'by_barber' => $schedulings->groupBy('barber.name')->map->count(),
        ];

        return response()->json([
            'period' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ],
            'statistics' => $stats,
            'schedulings' => $schedulings
        ]);
    }

}