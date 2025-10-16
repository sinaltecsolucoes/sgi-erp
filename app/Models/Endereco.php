<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endereco extends Model
{
    // Define a chave primária
    protected $primaryKey = 'end_codigo';

    protected $fillable = [
        'end_entidade_id',
        'end_tipo_endereco',
        'end_cep',
        'end_logradouro',
        'end_numero',
        'end_complemento',
        'end_bairro',
        'end_cidade',
        'end_uf',
        'end_usuario_cadastro_id'
    ];

    /**
     * O endereço pertence a uma Entidade.
     */
    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class, 'end_entidade_id', 'id');
    }
}
