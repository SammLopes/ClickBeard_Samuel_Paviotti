<?php
namespace App\Http\Controllers;

use App\Models\Barber;
use Laravel\Lumen\Routing\Controller as BaseController;

class BarberController extends BaseController
{
    public function index()
    {
        $barbers = Barber::active()->get();
        return response()->json($barbers);
    }

    public function show($id)
    {
        $barber = Barber::findOrFail($id);
        return response()->json($barber);
    }
}