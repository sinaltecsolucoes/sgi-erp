<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $fillable = [
        'entidade_id',
        'limite_credito',
        'is_transportadora'
    ];

    // Relacionamento N:1 - Um cliente pertence a uma entidade
    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class);
    }
}
