<?php 
namespace App\Http\Controllers;

use App\Models\Service;
use Laravel\Lumen\Routing\Controller as BaseController;

class ServiceController extends BaseController
{
    public function index()
    {
        $services = Service::active()->get();
        return response()->json($services);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }
}