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
use Yajra\DataTables\Facades\DataTables;

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
     * Define o título e a descrição da página de listagem e retorna a view index.
     * Esta é a lógica de lista_entidades.php (Parte PHP).
     */
    public function index(Request $request)
    {
        $pageType = $request->route()->defaults['pageType'] ?? 'entidade';

        $titulos = [
            'cliente' => 'Clientes',
            'fornecedor' => 'Fornecedores',
            'entidade' => 'Todas as Entidades',
        ];
        $subtitulos = [
            'cliente' => 'Gerencie todos os clientes.',
            'fornecedor' => 'Gerencie todos os fornecedores.',
            'entidade' => 'Gerencie todos os cadastros base.',
        ];

        // Passa as variáveis para a view Blade
        return view('entidades.index', [
            'pageType' => $pageType,
            'titulo' => $titulos[$pageType] ?? 'Entidades',
            'subtitulo' => $subtitulos[$pageType] ?? 'Gerencie todos os registros.',
            'singular' => ucfirst($pageType) // Ex: Cliente
        ]);
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
     * Endpoint AJAX para o DataTables (usa o Yajra DataTables).
     * Mapeia a lógica principal de EntidadeRepository::findAllForDataTable.
     */
    public function dataTable(Request $request, string $pageType)
    {
        // 1. Inicia a Query
        $query = Entidade::select([
            'entidades.id',
            'cnpj_cpf',
            'razao_social',
            'nome_fantasia',
            'tipo_entidade_papel',
            'ativo',
            // Coalesce para ordenar por Nome Fantasia, se existir, senão Razão Social
            DB::raw('COALESCE(NULLIF(nome_fantasia, ""), razao_social) as nome_display'),
        ]);

        // 2. Aplica Filtros de Módulo (Tipo de Entidade)
        if ($pageType === 'cliente') {
            // Filtrar entidades que tenham vínculo com a tabela 'clientes'
            $query->whereHas('cliente');
        } elseif ($pageType === 'fornecedor') {
            // Filtrar entidades que tenham vínculo com a tabela 'fornecedores'
            $query->whereHas('fornecedor');
        }
        // NOTE: Se for 'transportadora', você precisaria de uma tabela 'transportadoras' e um whereHas similar.

        // 3. Aplica Filtro de Situação (Como no seu PHP Puro)
        $filtroSituacao = $request->input('filtro_situacao', 'Ativos'); // Padrão: Ativos
        if ($filtroSituacao === 'Ativos') {
            $query->where('ativo', true);
        } elseif ($filtroSituacao === 'Inativos') {
            $query->where('ativo', false);
        }

        // 4. Constrói a Resposta DataTables
        return DataTables::eloquent($query)
            ->orderColumn('nome_display', function ($query, $order) {
                // Permite ordenação correta na coluna de nome display
                $query->orderBy(DB::raw('COALESCE(NULLIF(nome_fantasia, ""), razao_social)'), $order);
            })
            ->addColumn('status', function (Entidade $entidade) {
                // Coluna visual para a situação (ativo/inativo)
                return $entidade->ativo
                    ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>'
                    : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>';
            })
            ->addColumn('actions', function (Entidade $entidade) use ($pageType) {
                // Botões de Ação (Editar, Inativar, etc.)
                $editUrl = route('entidades.edit', $entidade->id);
                // A URL para o modal de inativação/ativação
                $statusAction = $entidade->ativo ? 'Inativar' : 'Ativar';

                return '<a href="' . $editUrl . '" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>' .
                    '<button data-id="' . $entidade->id . '" data-action="' . $statusAction . '" class="status-btn text-sm text-red-600 hover:text-red-900">' . $statusAction . '</button>';
            })
            ->rawColumns(['status', 'actions']) // Diz ao DataTables para não escapar o HTML
            ->make(true);
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
