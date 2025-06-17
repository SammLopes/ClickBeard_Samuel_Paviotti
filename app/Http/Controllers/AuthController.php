<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController {

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => app('hash')->make($request->input('password')),
            'role' => 'client'
        ]);
    
        $token = auth()->login($user);
    
        return response()->json([
            'message' => 'Usuário cadastrado com sucesso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $token
        ], 201);

    }

    public function login()
    {
        
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $token
        ]);
    }

    public function me()
    {
        $user = auth()->user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    } 

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
}

