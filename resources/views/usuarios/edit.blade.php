<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Usuário: ') . $usuario->usu_nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('usuarios.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        Voltar para a Lista
                    </a>

                    @if ($errors->any())
                    <div class="mt-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        <strong>Ops!</strong> Houve problemas com os dados:<br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST" class="mt-6 space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="space-y-2">
                            <x-input-label for="usu_nome" :value="__('Nome')" />
                            <x-text-input id="usu_nome" name="usu_nome" type="text" class="mt-1 block w-full" :value="old('usu_nome', $usuario->usu_nome)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('usu_nome')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="usu_login" :value="__('Login')" />
                            <x-text-input id="usu_login" name="usu_login" type="text" class="mt-1 block w-full" :value="old('usu_login', $usuario->usu_login)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('usu_login')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="usu_senha" :value="__('Nova Senha')" />
                            <x-text-input id="usu_senha" name="usu_senha" type="password" class="mt-1 block w-full" placeholder="Deixe em branco para não alterar" />
                            <x-input-error class="mt-2" :messages="$errors->get('usu_senha')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="usu_nivel" :value="__('Nível de Acesso')" />
                            <select id="usu_nivel" name="usu_nivel" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Admin" {{ old('usu_nivel', $usuario->usu_nivel) == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Gestor" {{ old('usu_nivel', $usuario->usu_nivel) == 'Gestor' ? 'selected' : '' }}>Gestor</option>
                                <option value="Operador" {{ old('usu_nivel', $usuario->usu_nivel) == 'Operador' ? 'selected' : '' }}>Operador</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('usu_nivel')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="usu_equipe" :value="__('Equipe/Setor')" />
                            <x-text-input id="usu_equipe" name="usu_equipe" type="text" class="mt-1 block w-full" :value="old('usu_equipe', $usuario->usu_equipe)" />
                            <x-input-error class="mt-2" :messages="$errors->get('usu_equipe')" />
                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>
                                {{ __('Atualizar Usuário') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>