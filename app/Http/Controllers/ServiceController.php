<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Service;
use App\Models\Specialty;

class ServiceController extends BaseController
{
    public function index()
    {
        $services = Service::active()->get();
        return response()->json($services);
    }

    public function show($id)
    {
        $service = Service::find($id);
        if( !$service  ) {
            return response()->json([
                'error' => 'Serviço não encontrado'
            ], 404);
        }

        return response()->json($service);
    }

    public function store(Request $request)
    {
        
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'nullable|string|max:1000',
            'specialty_id' => 'nullable|exists:specialties,id',
            'price' => 'required|numeric|min:0|max:999999.99',
            'duration_minutes' => 'required|integer|min:15|max:480', // 15 min a 8 horas
            'is_active' => 'nullable|boolean'
        ]);
        
        if ($request->specialty_id) {

            $specialty = Specialty::find($request->specialty_id);
        
            if (!$specialty->is_active) {
                return response()->json([
                    'error' => 'Não é possível associar o serviço a uma especialidade inativa.'
                ], 422);
            }

        }

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'specialty_id' => $request->specialty_id,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'is_active' => $request->is_active ?? true
        ]);
    
        $service->load('specialty');

        return response()->json([
            'message' => 'Serviço criado com sucesso!',
            'service' => $service
        ], 201);
    
    }

    public function update(Request $request, $id){
        $service = Service::find($id);
        if(!$service){
            return response()->json([
                'error' => 'Serviço não encontrada.'
            ], 404); 
        }
        $this->validate($request, [
            'name' => 'sometimes|required|string|max:255|unique:services,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'specialty_id' => 'nullable|exists:specialties,id',
            'price' => 'sometimes|required|numeric|min:0|max:999999.99',
            'duration_minutes' => 'sometimes|required|integer|min:15|max:480',
            'is_active' => 'nullable|boolean'
        ]);

        if ($request->has('specialty_id') && $request->specialty_id) {
            $specialty = Specialty::find($request->specialty_id);
            if(!$specialty){
                return response()->json([
                    'error' => 'Especialidade não encontrada.'
                ], 404); 
            }
            if (!$specialty->is_active) {
                return response()->json([
                    'error' => 'Não é possível associar o serviço a uma especialidade inativa.'
                ], 422);
            }
        }

        if ($request->has('is_active') && !$request->is_active && $service->is_active) {
            $futureSchedulings = $service->schedulings()
                ->where('scheduling_date', '>=', now()->toDateString())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count();

            if ($futureSchedulings > 0) {
                return response()->json([
                    'error' => 'Não é possível desativar um serviço com agendamentos futuros. Total: ' . $futureSchedulings
                ], 422);
            }
        }

        $service->update($request->only([
            'name', 
            'description', 
            'specialty_id', 
            'price', 
            'duration_minutes', 
            'is_active'
        ]));

        $service->load('specialty');
        return response()->json([
            'message' => 'Serviço atualizado com sucesso!',
            'service' => $service
        ]);

    }

    public function destroy($id)
    {
        $service = Service::find($id);

        if(!$service){
            return response()->json([
                'error' => 'Não foi encontrado o servico escolhido.'
            ], 404);
        }

        $schedulingsCount = $service->scheduling()->count();
        
        if ($schedulingsCount > 0) {
            return response()->json([
                'error' => 'Não é possível excluir um serviço que possui agendamentos associados. Total: ' . $schedulingsCount
            ], 422);
        }

        $serviceName = $service->name;
        $service->delete();

        return response()->json([
            'message' => "Serviço '{$serviceName}' excluído com sucesso!"
        ]);
    }

    public function getServicesBySpecialty($id)
    {

        $specialty = Specialty::find($id);
 
        if( !$specialty ) {
            return response()->json([
                'error' => 'Especialidade não encontrada'
            ], 404);
        }

        $services = Service::with('specialty')
            ->where('specialty_id', $id)
            ->active()
            ->orderBy('name')
            ->get();

        return response()->json([
            'specialty' => $specialty->name,
            'services' => $services
        ]);
    }


}