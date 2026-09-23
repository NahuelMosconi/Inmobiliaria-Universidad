<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Permite al usuario logueado cambiar su nombre, email y contraseña.
 */
class PerfilController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.perfil', ['usuario' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $usuario = $request->user();

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($usuario->id)],
            // Para cambiar la contraseña hay que escribir la actual.
            'password_actual' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [], [
            'name' => 'nombre',
            'password_actual' => 'contraseña actual',
            'password' => 'nueva contraseña',
        ]);

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];

        if (! empty($datos['password'])) {
            $usuario->password = $datos['password']; // el modelo la hashea solo (cast "hashed")
        }

        $usuario->save();

        return back()->with('exito', 'Tus datos se actualizaron.');
    }
}
