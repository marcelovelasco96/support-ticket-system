<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Area;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Notifications\SetPasswordNotification;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = ['admin', 'tecnico', 'usuario'];
        $areas = Area::orderBy('name')->get();

        $query = User::with('area')->orderByDesc('id');

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users', compact('roles', 'users', 'areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,tecnico,usuario'],
            'area_id' => ['required', 'integer', 'exists:areas,id'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make('Brena#2026!'),
            'area_id' => $data['area_id'],
        ]);

        $user->assignRole($data['role']);

        if (!empty($user->email)) {
            $token = Password::broker()->createToken($user);
            $user->notify(new SetPasswordNotification($token));
        }

        return back()->with('ok', 'Usuario creado y rol asignado.');
    }
}
