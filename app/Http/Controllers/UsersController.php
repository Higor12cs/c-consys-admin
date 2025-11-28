<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();

        return inertia('Users/Index', ['users' => $users]);
    }

    public function create()
    {
        return inertia('Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'required' => 'O campo é obrigatório.',
            'string' => 'O campo deve ser do tipo texto.',
            'email' => 'O valor informado não é um e-mail válido.',
            'min' => 'O campo deve ter no mínimo :min caracteres.',
        ]);

        User::create($validated);

        return to_route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        return inertia('Users/Edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'required' => 'O campo é obrigatório.',
            'string' => 'O campo deve ser do tipo texto.',
            'email' => 'O valor informado não é um e-mail válido.',
            'min' => 'O campo deve ter no mínimo :min caracteres.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        $user->save();

        return to_route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return to_route('users.index')->with('success', 'Usuário removido com sucesso!');
    }
}
