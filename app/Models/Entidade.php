<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Entidade extends Model
{
    protected $fillable = [
        'tipo_pessoa',
        'cnpj_cpf',
        'insc_estadual',
        'nome_fantasia',
        'razao_social',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'telefone',
        'email',
        'ativo'
    ];

    // Relacionamento 1:1 - Uma entidade pode ser um Cliente
    public function cliente(): HasOne
    {
        // O Laravel assume 'entidade_id' na tabela 'clientes'
        return $this->hasOne(Cliente::class);
    }

    // Relacionamento 1:1 - Uma entidade pode ser um Fornecedor
    public function fornecedor(): HasOne
    {
        return $this->hasOne(Fornecedor::class);
    }

    // Mutator para garantir que documentos sejam limpos antes de salvar
    public function setCnpjCpfAttribute($value)
    {
        $this->attributes['cnpj_cpf'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Uma entidade pode ter vários Endereços.
     */
    public function enderecos(): HasMany
    {
        return $this->hasMany(Endereco::class, 'end_entidade_id', 'id');
    }
}
