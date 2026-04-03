<?php

use App\Http\Controllers\clienteController;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors(['email' => 'Credenciales incorrectas.'])
            ->onlyInfput('email');
    })->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/registros', [clienteController::class, 'create'])->name('registros');
    Route::post('/registros', [clienteController::class, 'store'])->name('registros.store');

    Route::get('/gestion-filtros', [clienteController::class, 'filtrosIndex'])->name('filtros.index');
    Route::get('/gestion-filtros/{id}', [clienteController::class, 'filtrosShow'])->name('filtros.show');

    Route::get('/gestion-radicados', function () {
        return view('gestion_radicados');
    })->name('radicados.index');

    Route::get('/gestion-aprobados', function () {
        return view('gestion_aprobados');
    })->name('aprobados.index');

    Route::get('/gestion-desembolso', function () {
        return view('gestion_desembolso');
    })->name('desembolso.index');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout'); 
});


// routes/web.php
Route::get('/municipios/{departamentoId}', [clienteController::class, 'getMunicipios'])->name('municipios.get');