<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AjustesController extends Controller
{
    private const SUPERUSER_EMAIL = 'supervisor@gmail.com';
    private array $roles = ['asesor', 'supervisor', 'admin'];

    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        $current = auth()->user();
        $users = null;

        if (in_array($current->role ?? '', ['admin'], true)) {
            $users = User::orderBy('name')->get();
        }

        $superuserEmail = self::SUPERUSER_EMAIL;

        return view('ajustes.index', compact('users', 'superuserEmail'));
    }

    public function storeUser(Request $request)
    {
        $this->authorizeAction();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:asesor,supervisor,admin',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return redirect()->route('ajustes.index')->with('success', 'Usuario creado correctamente.');
    }

    public function updateRole(Request $request, $id)
    {
        $this->authorizeAction();

        $data = $request->validate([
            'role' => 'required|in:asesor,supervisor,admin',
        ]);

        $user = User::findOrFail($id);

        if ($user->email === self::SUPERUSER_EMAIL) {
            return redirect()->route('ajustes.index')->with('error', 'No se puede modificar el superusuario.');
        }

        $user->role = $data['role'];
        $user->save();

        return redirect()->route('ajustes.index')->with('success', 'Rol actualizado.');
    }

    public function resetPassword(Request $request, $id)
    {
        $this->authorizeAction();

        $data = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($id);

        if ($user->email === self::SUPERUSER_EMAIL) {
            return redirect()->route('ajustes.index')->with('error', 'No se puede modificar la contraseña del superusuario.');
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('ajustes.index')->with('success', 'Contraseña actualizada.');
    }

    public function sendResetLink($id)
    {
        $this->authorizeAction();

        $user = User::findOrFail($id);

        if ($user->email === self::SUPERUSER_EMAIL) {
            return redirect()->route('ajustes.index')->with('error', 'No se puede enviar link de recuperación al superusuario.');
        }

        Password::sendResetLink(['email' => $user->email]);

        return redirect()->route('ajustes.index')->with('success', 'Enviado link de recuperación por correo.');
    }

    public function destroy($id)
    {
        $this->authorizeAction();
        $user = User::findOrFail($id);

        if ($user->email === self::SUPERUSER_EMAIL) {
            return redirect()->route('ajustes.index')->with('error', 'No se puede eliminar el superusuario.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->route('ajustes.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('ajustes.index')->with('success', 'Usuario eliminado.');
    }

    private function authorizeAction(): void
    {
        $current = auth()->user();

        if (! in_array($current->role ?? '', ['admin'], true)) {
            abort(403);
        }
    }
}
