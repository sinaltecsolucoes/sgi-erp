<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Exibe a lista de usuarios
     */
    public function index()
    {
        // Pega todos os usuários do banco de dados.
        $usuarios = Usuario::all();

        // Retorna a view 'usuarios.index' e passa a variável $usuarios
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Mostra o formulario de criação de novo usuario.
     */
    public function create()
    {
        // Retorna a view que contém o formulário
        return view('usuarios.create');
    }

    /**
     * Armazena um novo usuário no banco de dados.
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados
        $request->validate([
            'usu_nome' => 'required|string|max:150',
            'usu_login' => 'required|string|unique:usuarios,usu_login|max:100',
            'usu_senha' => 'required|string|min:6',
            'usu_nivel' => 'required|string|max:30',
        ]);

        // 2. Criação do usuário (o hash da senha é feito no Model - Mutator)
        Usuario::create($request->all());

        // 3. Redirecionamento com mensagem de sucesso
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        return redirect()->route('usuarios.edit', $usuario);
    }

    /**
     * Mostra o formulário para editar um usuário específico.
     */
    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Atualiza o usuário específico no banco de dados.
     */
    public function update(Request $request, Usuario $usuario)
    {
        // 1. Validação dos dados
        $request->validate([
            'usu_nome' => 'required|string|max:150',
            // O login deve ser único, exceto para o usuário atual
            'usu_login' => 'required|string|max:100|unique:usuarios,usu_login,' . $usuario->id_usuario . ',id_usuario',
            'usu_nivel' => 'required|string|max:30',
            'usu_senha' => 'nullable|string|min:6', // Senha é opcional no update
        ]);

        // 2. Prepara os dados
        $data = $request->except(['_token', '_method']);

        // Se uma nova senha for fornecida, faz o hash; caso contrário, mantenha a antiga.
        if (isset($data['usu_senha']) && !empty($data['usu_senha'])) {
            // O Mutator (setUsuSenhaAttribute) no Model já cuida do Hash,
            // mas podemos garantir aqui, ou confiar no Model. Vamos confiar no Model
            // e apenas garantir que o campo esteja preenchido no request.

        } else {
            // Remove a senha do array de dados para que a antiga seja mantida
            unset($data['usu_senha']);
        }

        // 3. Atualização no banco
        $usuario->update($data);

        // 4. Redirecionamento
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
