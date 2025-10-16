<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\ApiExternaService;

class EntidadeController extends Controller
{
    // Injeta o serviço de API no construtor
    protected $apiService;

    public function __construct(ApiExternaService $apiService)
    {
        $this->apiService = $apiService;
    }

    // Método de API de busca de CNPJ
    public function buscarCnpjApi(string $cnpj)
    {
        $data = $this->apiService->buscarCnpj($cnpj);

        if (isset($data['error'])) {
            return response()->json(['success' => false, 'message' => $data['error']], $data['code'] ?? 500);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Mostra o formulário de criação de nova Entidade.
     */
    public function create()
    {
        // Retorna a view para o formulário
        return view('entidades.create');
    }

    /**
     * Armazena uma nova Entidade e seu Endereço Principal, e anexa os Módulos (Cliente/Fornecedor).
     * Mapeia a lógica do EntidadeRepository::create (Passos 1-5).
     */
    public function store(Request $request)
    {
        // 1. Validação 
        $request->validate([
            'ent_cpf_cnpj' => 'required|string|max:18|unique:entidades,cnpj_cpf',
            'ent_tipo_pessoa' => 'required|in:J,F',
            'ent_razao_social' => 'required|string|max:200',
            'ent_tipo_entidade' => 'required|string', // Cliente, Fornecedor, Ambos, Transportadora

            // Validação do Endereço Principal
            'end_cep' => 'required|string|max:8',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'required|string|max:20',
            'end_bairro' => 'required|string|max:80',
            'end_cidade' => 'required|string|max:80',
            'end_uf' => 'required|string|max:2',
        ]);

        try {
            // Inicia a Transação 
            DB::beginTransaction();

            // 2. Criação da Entidade Principal
            $entidade = Entidade::create([
                'tipo_pessoa' => $request->input('ent_tipo_pessoa'),
                // O Mutator no Model Entidade já limpa o CNPJ/CPF
                'cnpj_cpf' => $request->input('ent_cpf_cnpj'),
                'insc_estadual' => $request->input('ent_inscricao_estadual'),
                'razao_social' => $request->input('ent_razao_social'),
                'nome_fantasia' => $request->input('ent_nome_fantasia'),
                'ativo' => $request->has('ent_situacao') ? true : false, // Checkbox ativo/inativo
                // Campos do tipo_entidade (Para filtrar na listagem)
                'tipo_entidade_papel' => $request->input('ent_tipo_entidade'),

                // Mapear outros campos comuns
            ]);

            // 3. Criação do Endereço Principal
            if ($request->filled('end_cep')) {
                $entidade->enderecos()->create([
                    'end_tipo_endereco' => 'Principal',
                    'end_cep' => $request->input('end_cep'),
                    'end_logradouro' => $request->input('end_logradouro'),
                    'end_numero' => $request->input('end_numero'),
                    'end_complemento' => $request->input('end_complemento'),
                    'end_bairro' => $request->input('end_bairro'),
                    'end_cidade' => $request->input('end_cidade'),
                    'end_uf' => $request->input('end_uf'),
                    'end_usuario_cadastro_id' => Auth::id() // ID do usuário logado
                ]);
            }

            // 4. Anexar os Módulos (Clientes / Fornecedores) (tbl_clientes / tbl_fornecedores)
            $tipoEntidade = $request->input('ent_tipo_entidade');

            if ($tipoEntidade === 'Cliente' || $tipoEntidade === 'Cliente e Fornecedor') {
                Cliente::create(['entidade_id' => $entidade->id]);
            }
            if ($tipoEntidade === 'Fornecedor' || $tipoEntidade === 'Cliente e Fornecedor') {
                Fornecedor::create(['entidade_id' => $entidade->id]);
            }

            // Commit da Transação
            DB::commit();

            // Lógica de Auditoria (CREATE)

            return redirect()->route('entidades.index')
                ->with('success', 'Entidade e Vínculos criados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Lança o erro para o handler de erros do Laravel.
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entidade $entidade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entidade $entidade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entidade $entidade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entidade $entidade)
    {
        //
    }
}
