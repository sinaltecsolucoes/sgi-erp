<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Configuração do Laravel para usar sua PK personalizada
    protected $primaryKey = 'id_usuario';

    // Lista de campos que podem ser preenchidos em massa
    protected $fillable = [
        'usu_nome',
        'usu_login',
        'usu_senha',
        'usu_equipe',
        'usu_nivel',
    ];

    // Campos que devem ser ocultados ao serializar (SEGURANÇA!)
    protected $hidden = [
        'usu_senha',
        'remember_token',
    ];

    // O Laravel precisa saber qual campo é a senha
    public function getAuthPassword()
    {
        return $this->usu_senha;
    }

    /**
     * Hash da senha antes de salvar/atualizar.
     */
    public function setUsuSenhaAttribute($value)
    {
        $this->attributes['usu_senha'] = bcrypt($value);
    }
}
