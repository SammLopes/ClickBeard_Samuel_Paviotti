<?php
namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class BarberController extends BaseController
{
    public function index(Request $request)
    {
        $query = Barber::with(['specialties', 'primarySpecialty'])->active();

        if($request->has('specialty_id')){
            $query->withSpecialty($request->specialty_id);
        }
        
        $barbers = $query->get()->map(
            function($barber){
                $primarySpecialty = $barber->primarySpecialty()->first();
                return [
                    'id' => $barber->id,
                    'name' => $barber->name,
                    'email' => $barber->email,
                    'phone' => $barber->phone,
                    'age' => $barber->age,
                    'hire_date' => $barber->hire_date->format('Y-m-d'),
                    'years_of_service' => $barber->years_of_service,
                    'is_active' => $barber->is_active,
                    'primary_specialty' => $primarySpecialty ? [
                        'id' => $primarySpecialty->id,
                        'name' => $primarySpecialty->name,
                        'experience_years' => $primarySpecialty->pivot->experience_years
                    ] : null,
                    'specialties' => $barber->specialties->map(function($specialty) {
                        return [
                            'id' => $specialty->id,
                            'name' => $specialty->name,
                            'experience_years' => $specialty->pivot->experience_years,
                            'is_primary' => $specialty->pivot->is_primary
                        ];
                    })
                ];
            }
         );

        return response()->json($barbers);
    }

    public function show($id)
    {
        $barber = Barber::with(['specialties', 'scheduling' => function($query) {
            $query->with(['user', 'service'])->orderBy('scheduling_date', 'desc')->limit(10);
        } ] )->findOrFail($id);

        $barberData = [
            'id' => $barber->id,
            'name' => $barber->name,
            'email' => $barber->email,
            'phone' => $barber->phone,
            'age' => $barber->age,
            'hire_date' => $barber->hire_date->format('Y-m-d'),
            'years_of_service' => $barber->years_of_service,
            'is_active' => $barber->is_active,
            'specialties' => $barber->specialties->map(function($specialty) {
                return [
                    'id' => $specialty->id,
                    'name' => $specialty->name,
                    'description' => $specialty->description,
                    'experience_years' => $specialty->pivot->experience_years,
                    'is_primary' => $specialty->pivot->is_primary
                ];
            }),
            'recent_schedulings' => $barber->scheduling
        ];
        return response()->json($barberData);
    }

    public function store(Request $request)
    {   
        $user = auth()->user();
        if( !$user->isAdmin() ){
            return response()->json(['erro'=>'Acesso negado'], 403);
        }

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:barbers,email',
            'phone' => 'nullable|string|max:20',
            'age' => 'required|integer|min:18|max:70',
            'hire_date' => 'required|date',
            'specialties' => 'required|array|min:1',
            'specialties.*.specialty_id' => 'required|exists:specialties,id',
            'specialties.*.experience_years' => 'required|integer|min:0',
            'specialties.*.is_primary' => 'boolean'
        ]);

        $barber = Barber::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => $request->age,
            'hire_date' => $request->hire_date,
            'is_active' => true
        ]);

        foreach ($request->specialties as $specialtyData) {
            $barber->addSpecialty(
                $specialtyData['specialty_id'],
                $specialtyData['experience_years'],
                $specialtyData['is_primary'] ?? false
            );
        }
        $barber->load('specialties');

        return response()->json([
            'message'=>'Barbeiro criado com sucesso!',
            'barber'=>$barber
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $barber = Barber::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'string|max:255',
            'email' => 'email|unique:barbers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'age' => 'integer|min:18|max:70',
            'hire_date' => 'date',
            'is_active' => 'boolean'
        ]);

        $fieldsUpdated = $request->only([
            'name', 'email', 'phone', 'age', 'hire_date', 'is_active'
        ]);
        $barber->update($fieldsUpdated);

         return response()->json([
            'message' => 'Barbeiro atualizado com sucesso',
            'barber' => $barber
        ]);
    }

    public function addSpecialty(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $this->validate($request, [
            'specialty_id' => 'required|exists:specialties,id',
            'experience_years' => 'required|integer|min:0',
            'is_primary' => 'boolean'
        ]);

        $barber = Barber::findOrFail($id);

        if ($barber->hasSpecialty($request->specialty_id)) {
            return response()->json([
                'error' => 'Barbeiro já possui esta especialidade'
            ], 422);
        }

        $barber->addSpecialty(
            $request->specialty_id,
            $request->experience_years,
            $request->is_primary ?? false
        );

        $barber->load('specialties');

        return response()->json([
            'message' => 'Especialidade adicionada com sucesso',
            'barber' => $barber
        ]);
    }

    public function removeSpecialty(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $this->validate($request, [
            'specialty_id' => 'required|exists:specialties,id'
        ]);

        $barber = Barber::findOrFail($id);
        $barber->removeSpecialty($request->specialty_id);

        return response()->json([
            'message' => 'Especialidade removida com sucesso'
        ]);
    }

    public function getBySpecialty($specialtyId)
    {
        $specialty = Specialty::findOrFail($specialtyId);
        
        $barbers = $specialty->barbers()
            ->where('is_active', true)
            ->orderByPivot('experience_years', 'desc')
            ->get();

        return response()->json([
            'specialty' => $specialty->name,
            'barbers' => $barbers->map(function($barber) {
                return [
                    'id' => $barber->id,
                    'name' => $barber->name,
                    'experience_years' => $barber->pivot->experience_years,
                    'is_primary' => $barber->pivot->is_primary
                ];
            })
        ]);
    }

}