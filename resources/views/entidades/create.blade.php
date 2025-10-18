<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastro de Nova Entidade (Pessoa/Empresa)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('entidades.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        Voltar para a Lista de Entidades
                    </a>

                    @if ($errors->any())
                    <div class="mt-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        <strong>Ops!</strong> Houve problemas na validação:<br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form id="form-entidade" action="{{ route('entidades.store') }}" method="POST" class="mt-6 space-y-8">
                        @csrf

                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium text-gray-900">Identificação e Papel</h3>
                            <p class="mt-1 text-sm text-gray-600">Dados base da pessoa/empresa. Use o CNPJ para preenchimento automático.</p>

                            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div class="col-span-1">
                                    <x-input-label for="ent_tipo_pessoa" :value="__('Tipo de Pessoa')" />
                                    <select id="ent_tipo_pessoa" name="ent_tipo_pessoa" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="J" {{ old('ent_tipo_pessoa') == 'J' ? 'selected' : '' }}>Jurídica (CNPJ)</option>
                                        <option value="F" {{ old('ent_tipo_pessoa') == 'F' ? 'selected' : '' }}>Física (CPF)</option>
                                    </select>
                                </div>

                                <div class="col-span-1">
                                    <x-input-label for="ent_cpf_cnpj" id="label-cpf-cnpj" :value="__('CNPJ')" />
                                    <x-text-input id="ent_cpf_cnpj" name="ent_cpf_cnpj" type="text" class="mt-1 block w-full" :value="old('ent_cpf_cnpj')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('ent_cpf_cnpj')" />
                                </div>

                                <div class="col-span-1 flex items-end">
                                    <x-primary-button type="button" id="btn-buscar-cnpj">
                                        {{ __('Buscar Dados (API)') }}
                                    </x-primary-button>
                                </div>
                                <div class="col-span-3">
                                    <span id="cnpj-feedback" class="text-sm"></span>
                                </div>
                            </div>

                            <div class="mt-6">
                                <x-input-label for="ent_tipo_entidade" :value="__('Esta Entidade será:')" />
                                <select id="ent_tipo_entidade" name="ent_tipo_entidade" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="Cliente" {{ old('ent_tipo_entidade') == 'Cliente' ? 'selected' : '' }}>Cliente</option>
                                    <option value="Fornecedor" {{ old('ent_tipo_entidade') == 'Fornecedor' ? 'selected' : '' }}>Fornecedor</option>
                                    <option value="Cliente e Fornecedor" {{ old('ent_tipo_entidade') == 'Cliente e Fornecedor' ? 'selected' : '' }}>Cliente e Fornecedor</option>
                                    <option value="Transportadora" {{ old('ent_tipo_entidade') == 'Transportadora' ? 'selected' : '' }}>Transportadora</option>
                                    <option value="Outros" {{ old('ent_tipo_entidade') == 'Outros' ? 'selected' : '' }}>Outros (Apenas Entidade Base)</option>
                                </select>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium text-gray-900">Dados Cadastrais</h3>
                            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="ent_razao_social" :value="__('Razão Social / Nome')" />
                                    <x-text-input id="ent_razao_social" name="ent_razao_social" type="text" class="mt-1 block w-full" :value="old('ent_razao_social')" required />
                                </div>
                                <div>
                                    <x-input-label for="ent_nome_fantasia" :value="__('Nome Fantasia')" />
                                    <x-text-input id="ent_nome_fantasia" name="ent_nome_fantasia" type="text" class="mt-1 block w-full" :value="old('ent_nome_fantasia')" />
                                </div>
                                <div id="div-inscricao-estadual">
                                    <x-input-label for="ent_inscricao_estadual" :value="__('Inscrição Estadual (IE)')" />
                                    <x-text-input id="ent_inscricao_estadual" name="ent_inscricao_estadual" type="text" class="mt-1 block w-full" :value="old('ent_inscricao_estadual')" />
                                </div>
                                <div>
                                    <x-input-label for="ent_telefone" :value="__('Telefone Principal')" />
                                    <x-text-input id="ent_telefone" name="ent_telefone" type="text" class="mt-1 block w-full" :value="old('ent_telefone')" />
                                </div>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium text-gray-900">Endereço Principal</h3>
                            <p class="mt-1 text-sm text-gray-600">Este é o endereço principal (preenchido pela API).</p>

                            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-4">
                                <div>
                                    <x-input-label for="end_cep" :value="__('CEP')" />
                                    <x-text-input id="end_cep" name="end_cep" type="text" class="mt-1 block w-full" :value="old('end_cep')" />
                                </div>
                                <div class="col-span-2">
                                    <x-input-label for="end_logradouro" :value="__('Logradouro')" />
                                    <x-text-input id="end_logradouro" name="end_logradouro" type="text" class="mt-1 block w-full" :value="old('end_logradouro')" />
                                </div>
                                <div>
                                    <x-input-label for="end_numero" :value="__('Número')" />
                                    <x-text-input id="end_numero" name="end_numero" type="text" class="mt-1 block w-full" :value="old('end_numero')" />
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-4">
                                <div>
                                    <x-input-label for="end_complemento" :value="__('Complemento')" />
                                    <x-text-input id="end_complemento" name="end_complemento" type="text" class="mt-1 block w-full" :value="old('end_complemento')" />
                                </div>
                                <div>
                                    <x-input-label for="end_bairro" :value="__('Bairro')" />
                                    <x-text-input id="end_bairro" name="end_bairro" type="text" class="mt-1 block w-full" :value="old('end_bairro')" />
                                </div>
                                <div>
                                    <x-input-label for="end_cidade" :value="__('Cidade')" />
                                    <x-text-input id="end_cidade" name="end_cidade" type="text" class="mt-1 block w-full" :value="old('end_cidade')" />
                                </div>
                                <div>
                                    <x-input-label for="end_uf" :value="__('UF')" />
                                    <x-text-input id="end_uf" name="end_uf" type="text" class="mt-1 block w-full" maxlength="2" :value="old('end_uf')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <input id="ent_situacao" name="ent_situacao" type="checkbox" checked value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <x-input-label for="ent_situacao" class="ml-2" :value="__('Entidade Ativa (Disponível para uso)')" />
                        </div>

                        <div class="flex items-center gap-4 pt-6">
                            <x-primary-button>
                                {{ __('Salvar Entidade') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        $(document).ready(function() {
            // Mapeamento de campos do formulário
            const $tipoPessoaSelect = $('#ent_tipo_pessoa');
            const $cpfCnpjInput = $('#ent_cpf_cnpj');
            const $labelCpfCnpj = $('#label-cpf-cnpj');
            const $btnBuscarCnpj = $('#btn-buscar-cnpj');
            const $feedback = $('#cnpj-feedback');
            const $divInscricaoEstadual = $('#div-inscricao-estadual');

            // Campos que serão preenchidos pela API
            const mapFields = {
                razao_social: $('#ent_razao_social'),
                nome_fantasia: $('#ent_nome_fantasia'),
                insc_estadual: $('#ent_inscricao_estadual'),
                telefone: $('#ent_telefone'),
                // Endereço Principal (mapeando para os nomes da view)
                cep: $('#end_cep'),
                endereco: $('#end_logradouro'),
                numero: $('#end_numero'),
                complemento: $('#end_complemento'),
                bairro: $('#end_bairro'),
                cidade: $('#end_cidade'),
                uf: $('#end_uf'),
            };

            // =================================================================
            // 1. MÁSCARAS E LÓGICA DE TIPO DE PESSOA (Baseado no seu entidades.js)
            // =================================================================
            const aplicarMascara = (tipo) => {
                $cpfCnpjInput.unmask();
                if (tipo === 'J') {
                    $labelCpfCnpj.text('CNPJ');
                    $cpfCnpjInput.mask('00.000.000/0000-00', {
                        reverse: true
                    });
                    $divInscricaoEstadual.show();
                } else { // Física
                    $labelCpfCnpj.text('CPF');
                    $cpfCnpjInput.mask('000.000.000-00', {
                        reverse: true
                    });
                    $divInscricaoEstadual.hide();
                }
            };

            // Inicializa a máscara
            aplicarMascara($tipoPessoaSelect.val());

            // Altera a máscara ao mudar o tipo de pessoa
            $tipoPessoaSelect.on('change', function() {
                aplicarMascara($(this).val());
            });

            // Máscara de CEP
            $('#end_cep').mask('00000-000');


            // =================================================================
            // 2. FUNÇÃO DE BUSCA CNPJ (Chamando a Rota de Backend)
            // =================================================================

            const preencherCampos = (data) => {
                $feedback.text(`Dados carregados via ${data.api_source}.`).removeClass('text-red-500 text-blue-500').addClass('text-green-500');

                $.each(mapFields, function(key, $field) {
                    // Usa o nome da coluna da API (data[key]) e não o nome da chave do mapFields
                    if (data[key]) {
                        // Mapeamento específico devido à diferença de nome:
                        let valorApi = data[key];
                        if (key === 'endereco') key = 'end_logradouro'; // Corrige o mapeamento

                        // Verifica se a chave 'data' contém o valor
                        if (valorApi) {
                            $field.val(valorApi.toUpperCase()).prop('readonly', true);
                        }
                    }
                });

                // Tratamento de máscaras após preenchimento
                $('#end_cep').mask('00000-000').trigger('input');
            };

            $btnBuscarCnpj.on('click', function() {
                let documento = $cpfCnpjInput.val().replace(/\D/g, '');
                const tipoPessoa = $tipoPessoaSelect.val();

                if (tipoPessoa === 'F') {
                    $feedback.text('Busca automática para CPF não implementada. Digite manualmente.').addClass('text-orange-500').removeClass('text-red-500 text-green-500');
                    return;
                }
                if (documento.length !== 14) {
                    $feedback.text('CNPJ Inválido.').addClass('text-red-500').removeClass('text-green-500 text-blue-500');
                    return;
                }

                $feedback.text('Buscando dados na API...').removeClass('text-green-500 text-red-500').addClass('text-blue-500');
                $btnBuscarCnpj.prop('disabled', true);

                // Limpa os campos antes de buscar, mas mantém o documento
                $.each(mapFields, function(key, $field) {
                    $field.val('').prop('readonly', false);
                });

                // Chama a rota do Laravel que acessa a ApiExternaService (com Fallback)
                $.ajax({
                    // A rota 'api.cnpj' está definida no routes/web.php
                    url: "{{ route('api.cnpj', ['cnpj' => 'TEMP']) }}".replace('TEMP', documento),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            preencherCampos(response.data);
                        } else {
                            // Se a busca falhar, o Controller retorna a mensagem de erro da API
                            $feedback.text(response.message || 'Erro desconhecido na busca.').addClass('text-red-500').removeClass('text-blue-500');
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Erro de conexão com o servidor. Tente novamente.';
                        $feedback.text(msg).addClass('text-red-500').removeClass('text-blue-500');
                    },
                    complete: function() {
                        $btnBuscarCnpj.prop('disabled', false);
                    }
                });
            });

            // =================================================================
            // 3. FUNÇÃO DE BUSCA CEP (ViaCEP)
            // =================================================================
            // Esta função pode ser feita diretamente no frontend, pois a ViaCEP é pública e não precisa de backend.
            $('#end_cep').on('blur', function() {
                let cep = $(this).val().replace(/\D/g, '');
                if (cep.length !== 8) return;

                // Implementação ViaCEP (como no seu JS original)
                $.ajax({
                    url: `https://viacep.com.br/ws/${cep}/json/`,
                    dataType: 'json',
                    success: function(data) {
                        if (!("erro" in data)) {
                            $('#end_logradouro').val(data.logradouro.toUpperCase()).prop('readonly', true);
                            $('#end_bairro').val(data.bairro.toUpperCase()).prop('readonly', true);
                            $('#end_cidade').val(data.localidade.toUpperCase()).prop('readonly', true);
                            $('#end_uf').val(data.uf.toUpperCase()).prop('readonly', true);
                            $('#end_numero').focus(); // Move o foco para o número
                        } else {
                            // CEP pesquisado não foi encontrado
                            alert("CEP não encontrado.");
                        }
                    },
                    error: function() {
                        alert("Erro ao consultar a API ViaCEP.");
                    }
                });
            });

        });
    </script>
    @endpush
</x-app-layout>