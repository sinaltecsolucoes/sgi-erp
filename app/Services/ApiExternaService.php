<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ApiExternaService
{
    // URLs base das APIs
    protected $viacepUrl = 'https://viacep.com.br/ws/';
    protected $brasilapiUrl = 'https://brasilapi.com.br/api/cnpj/v1/';
    protected $openCNPJA = 'https://open.cnpja.com';

    /**
     * Busca dados de endereço completo usando o ViaCEP. (Usado para endereços secundários)
     */
    public function buscarCep(string $cep): array
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        if (strlen($cep) !== 8) {
            return ['error' => 'CEP inválido.', 'code' => 400];
        }

        try {
            $response = Http::get("{$this->viacepUrl}{$cep}/json/");

            if ($response->failed() || isset($response->json()['erro'])) {
                return ['error' => 'CEP não encontrado.', 'code' => $response->status()];
            }

            return $response->json();
        } catch (Exception $e) {
            return ['error' => 'Erro de conexão com a API ViaCEP.', 'code' => 500];
        }
    }

    /**
     * Busca dados de entidade (Razão Social, Endereço Principal) com lógica de Fallback.
     * Tenta Open.CNPJA (prioridade), se falhar, tenta BrasilAPI.
     */
    public function buscarCnpj(string $cnpj): array
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) !== 14) {
            return ['error' => 'CNPJ inválido.', 'code' => 400];
        }

        $result = $this->fetchFromOpenCnpj($cnpj);

        if (isset($result['error'])) {
            // Se falhar na primeira API, tenta a segunda (Fallback)
            $result = $this->fetchFromBrasilApi($cnpj);
        }

        return $result;
    }

    /**
     * Tenta buscar dados do CNPJ na Open.CNPJA.
     */
    protected function fetchFromOpenCnpj(string $cnpj): array
    {
        // Se a Open.CNPJA exigir API Key, configure o token aqui:
        $token = env('OPEN_CNPJA_TOKEN');

        try {
            // endpoint CNPJA
            $response = Http::timeout(5)->get("https://open.cnpja.com/office/{$cnpj}", [
                // 'token' => $token, // Descomentar se precisar de token na query string
            ]);

            if ($response->failed() || $response->json('status') === 'ERROR') {
                return ['error' => $response->json('message') ?? 'CNPJ não encontrado na CNPJá.', 'code' => $response->status()];
            }

            $data = $response->json();

            // Mapeamento dos dados da Open.CNPJA
            return [
                'razao_social'      => strtoupper($data['company']['name'] ?? ''),
                'nome_fantasia'     => strtoupper($data['alias'] ?? ''),
                'cnpj_cpf'          => $cnpj,
                'insc_estadual'     => strtoupper($data['registrations'][0]['number'] ?? ''),
                'cep'               => preg_replace('/[^0-9]/', '', $data['address']['zip'] ?? ''),
                'endereco'          => strtoupper($data['address']['street'] ?? ''),
                'numero'            => strtoupper($data['address']['number'] ?? ''),
                'complemento'       => strtoupper($data['address']['details'] ?? ''),
                'bairro'            => strtoupper($data['address']['district'] ?? ''),
                'cidade'            => strtoupper($data['address']['city'] ?? ''),
                'uf'                => strtoupper($data['address']['state'] ?? ''),
                'telefone'          => $data['company']['phone'] ?? null,
                'email'             => $data['company']['email'] ?? null,
                'api_source'        => 'Open.CNPJA',
            ];
        } catch (Exception $e) {
            // Retorna erro para acionar o fallback
            return ['error' => 'Erro de conexão com Open.CNPJA. Tentando alternativa.', 'code' => 500];
        }
    }

    /**
     * Tenta buscar dados do CNPJ na BrasilAPI (Fallback).
     */
    protected function fetchFromBrasilApi(string $cnpj): array
    {
        try {
            $response = Http::timeout(5)->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");

            if ($response->failed() || $response->status() !== 200) {
                return ['error' => 'CNPJ não encontrado na BrasilAPI.', 'code' => $response->status()];
            }

            $data = $response->json();

            // Mapeamento dos dados da BrasilAPI (sem IE)
            return [
                'razao_social'      => strtoupper($data['razao_social'] ?? ''),
                'nome_fantasia'     => strtoupper($data['nome_fantasia'] ?? ''),
                'cnpj_cpf'          => $cnpj,
                'insc_estadual'     => '', // Não disponível nesta API
                'cep'               => preg_replace('/[^0-9]/', '', $data['cep'] ?? ''),
                'endereco'          => strtoupper($data['logradouro'] ?? ''),
                'numero'            => strtoupper($data['numero'] ?? ''),
                'complemento'       => strtoupper($data['complemento'] ?? ''),
                'bairro'            => strtoupper($data['bairro'] ?? ''),
                'cidade'            => strtoupper($data['municipio'] ?? ''),
                'uf'                => strtoupper($data['uf'] ?? ''),
                'telefone'          => $data['ddd_telefone_1'] ?? null,
                'email'             => $data['email'] ?? null,
                'api_source'        => 'BrasilAPI',
            ];
        } catch (Exception $e) {
            return ['error' => 'Erro fatal de conexão com a API de CNPJ.', 'code' => 500];
        }
    }
}
