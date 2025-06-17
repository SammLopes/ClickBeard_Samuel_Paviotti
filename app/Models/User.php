<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject, AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];

    // Retorna o identificador do usuário para o token
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Define claims adicionais (vazio neste caso)
    public function getJWTCustomClaims()
    {
        return [];
    }
}
