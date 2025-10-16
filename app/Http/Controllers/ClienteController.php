<?php

namespace App\Http\Controllers;

use App\Services\ApiExternaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $apiService;

    // Construtor para injetar o serviço de API
    public function __construct(ApiExternaService $apiService)
    {
        $this->apiService = $apiService;
    }

    // Método que será chamado via ROTA /api/cnpj/{cnpj}
    public function buscarCnpjApi(string $cnpj)
    {
        // 1. Busca os dados via Service
        $data = $this->apiService->buscarCnpj($cnpj);

        // 2. Verifica se houve erro
        if (isset($data['error'])) {
            return response()->json([
                'success' => false,
                'message' => $data['error']
            ], $data['code'] ?? 500);
        }

        // 3. Retorna os dados com sucesso
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}