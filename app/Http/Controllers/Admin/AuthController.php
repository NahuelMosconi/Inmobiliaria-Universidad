<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Login y logout del panel. Los usuarios no se pueden registrar solos:
 * el primero lo crea el seeder y después se manejan desde la base.
 */
class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credenciales, $request->boolean('recordarme'))) {
            // No digo si falló el mail o la contraseña para no dar pistas.
            return back()
                ->withErrors(['email' => 'El email o la contraseña no son correctos.'])
                ->onlyInput('email');
        }

        // Nuevo id de sesión para evitar ataques de fijación de sesión.
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}
