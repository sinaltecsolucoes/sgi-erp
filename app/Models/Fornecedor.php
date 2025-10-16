<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;   

class Fornecedor extends Model
{
    protected $fillable = [
        'entidade_id',
        'ultima_compra_info',
        'is_materia_prima'
    ];

    // Relacionamento N:1 - Um fornecedor pertence a uma entidade
    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class);
    }
}
