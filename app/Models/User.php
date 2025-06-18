<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject, AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    // Retorna o identificador do usuário para o token
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';  
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
