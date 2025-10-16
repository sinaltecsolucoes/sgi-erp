<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Usuários - SGI-ERP') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('usuarios.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Criar Novo Usuário
                    </a>

                    @if ($message = Session::get('success'))
                    <div class="mt-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nível</th>
                                    <th class="px-6 py-3 bg-gray-50">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($usuarios as $usuario)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $usuario->id_usuario }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $usuario->usu_nome }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $usuario->usu_login }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $usuario->usu_nivel }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                        <form action="{{ route('usuarios.destroy', $usuario->id_usuario) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>