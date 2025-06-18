<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialty;
use Laravel\Lumen\Routing\Controller as BaseController;

class SpecialtyController extends BaseController
{
    public function index()
    {
        $specialties = Specialty::active()
            ->withCount('barbers')
            ->with(['services' => function($query) {
                $query->active();
            }])
            ->get();

        return response()->json($specialties->map(function($specialty) {
            return [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'description' => $specialty->description,
                'is_active' => $specialty->is_active,
                'barbers_count' => $specialty->barbers_count,
                'services' => $specialty->services->map(function($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'duration_minutes' => $service->duration_minutes
                    ];
                })
            ];
        }));
    }

    public function show($id)
    {
        $specialty = Specialty::with([
            'barbers' => function($query) {
                $query->where('is_active', true)->orderByPivot('experience_years', 'desc');
            },
            'services' => function($query) {
                $query->active();
            }
        ])->findOrFail($id);

        return response()->json([
            'id' => $specialty->id,
            'name' => $specialty->name,
            'description' => $specialty->description,
            'is_active' => $specialty->is_active,
            'barbers' => $specialty->barbers->map(function($barber) {
                return [
                    'id' => $barber->id,
                    'name' => $barber->name,
                    'experience_years' => $barber->pivot->experience_years,
                    'is_primary' => $barber->pivot->is_primary
                ];
            }),
            'services' => $specialty->services
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $this->validate($request, [
            'name' => 'required|string|max:255|unique:specialties,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $specialty = Specialty::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json([
            'message' => 'Especialidade criada com sucesso',
            'specialty' => $specialty
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $specialty = Specialty::findOrFail($id);

        $this->validate($request, [
            'name' => 'string|max:255|unique:specialties,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $specialty->update($request->only(['name', 'description', 'is_active']));

        return response()->json([
            'message' => 'Especialidade atualizada com sucesso',
            'specialty' => $specialty
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $specialty = Specialty::findOrFail($id);
        
        // Verificar se há barbeiros ou serviços vinculados
        if ($specialty->barbers()->count() > 0 || $specialty->services()->count() > 0) {
            return response()->json([
                'error' => 'Não é possível excluir especialidade com barbeiros ou serviços vinculados'
            ], 422);
        }

        $specialty->delete();

        return response()->json([
            'message' => 'Especialidade excluída com sucesso'
        ]);
    }
}