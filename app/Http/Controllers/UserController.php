<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search');
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('dni', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }

    public function create()
    {
        return view('users.create', ['roles' => UserRole::cases()]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['active'] = $request->boolean('active');
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propio usuario.');
        }

        $user->update(['active' => false]);

        return redirect()->route('users.index')->with('success', 'Usuario desactivado. Su historial se conserva.');
    }
}
