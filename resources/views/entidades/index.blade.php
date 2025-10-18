<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de ') . $titulo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <p class="mb-4 text-gray-600">{{ $subtitulo }}</p>

                    <div class="flex justify-between items-center mb-6">
                        <h6 class="text-lg font-bold text-gray-800">Gerenciar Registros</h6>
                        <a href="{{ route('entidades.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                            {{ __('Novo ') . $singular }}
                        </a>
                    </div>

                    <div class="mb-4 flex space-x-4">
                        <select id="filtro_situacao" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="Ativos">Situação: Ativos</option>
                            <option value="Inativos">Situação: Inativos</option>
                            <option value="Todos">Situação: Todos</option>
                        </select>
                        <select id="filtro_tipo_entidade" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="Todos">Papel: Todos</option>
                            <option value="Cliente">Papel: Cliente</option>
                            <option value="Fornecedor">Papel: Fornecedor</option>
                            <option value="Transportadora">Papel: Transportadora</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="tabela-entidades" class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Tipo') }}</th>
                                    <th>{{ __('CNPJ/CPF') }}</th>
                                    <th>{{ __('Nome Fantasia / Razão Social') }}</th>
                                    <th>{{ __('Papel Principal') }}</th>
                                    <th>{{ __('Ações') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            const pageType = "{{ $pageType }}";
            let tableEntidades;

            // Mapeia os índices de coluna para o DataTables
            const columnsMap = [{
                    data: 'status',
                    name: 'ativo',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'tipo_entidade_papel',
                    name: 'tipo_entidade_papel',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'cnpj_cpf',
                    name: 'cnpj_cpf',
                    orderable: true,
                    searchable: true
                },
                // Usamos a coluna raw COALESCE para ordenação no backend
                {
                    data: 'nome_display',
                    name: 'nome_display',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'tipo_entidade_papel',
                    name: 'tipo_entidade_papel',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                },
            ];

            // Inicializa o DataTables
            tableEntidades = $('#tabela-entidades').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('entidades.data', ['pageType' => $pageType]) }}",
                    type: 'GET',
                    // Adiciona filtros customizados na requisição AJAX
                    data: function(d) {
                        d.filtro_situacao = $('#filtro_situacao').val();
                        d.filtro_tipo_entidade = $('#filtro_tipo_entidade').val();
                    }
                },
                columns: columnsMap,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json' // Tradução em PT-BR
                },
                order: [
                    [3, 'asc']
                ] // Ordena por Nome Fantasia / Razão Social
            });

            // Recarrega a tabela quando os filtros mudam (Lógica do seu lista_entidades.php)
            $('#filtro_situacao, #filtro_tipo_entidade').on('change', function() {
                tableEntidades.ajax.reload();
            });

            // Lógica para Inativação/Ativação (Será implementada no próximo passo)
            $('#tabela-entidades').on('click', '.status-btn', function() {
                const id = $(this).data('id');
                const action = $(this).data('action');
                // Aqui você chamaria um modal de confirmação e, em seguida, uma rota AJAX:
                // $.ajax({ url: '/entidades/status/' + id, method: 'POST', data: { action: action, _token: '{{ csrf_token() }}' }, ... });
                alert(`Ação de ${action} na Entidade ${id} será implementada.`);
            });
        });
    </script>
    @endpush
</x-app-layout>