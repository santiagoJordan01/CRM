<?php

use App\Http\Controllers\GestionClienteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\RegistroClienteController;
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
            ->onlyInput('email');
    })->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/notificaciones/{id}/abrir', [NotificacionController::class, 'abrir'])->name('notifications.open');

    Route::middleware('role:asesor,supervisor')->group(function () {
        Route::get('/registros', [RegistroClienteController::class, 'create'])->name('registros');
        Route::post('/registros', [RegistroClienteController::class, 'store'])->name('registros.store');
    });

    Route::get('/gestion-filtros', [GestionClienteController::class, 'filtrosIndex'])->name('filtros.index');
    Route::get('/gestion-filtros/{id}/proceso', [GestionClienteController::class, 'filtrosProceso'])->name('filtros.proceso');
    Route::get('/gestion-filtros/{id}', [GestionClienteController::class, 'filtrosShow'])->name('filtros.show');
    Route::get('/proceso/{id}', [GestionClienteController::class, 'procesoIndex'])->name('proceso.index');
    Route::post('/gestion-filtros/{id}/actualizacion-asesor', [GestionClienteController::class, 'filtrosActualizarAsesor'])
        ->middleware('role:asesor')
        ->name('filtros.asesor.update');
    Route::post('/gestion-filtros/{id}/respuesta-mesa-control', [GestionClienteController::class, 'filtrosResponderMesaControl'])
        ->middleware('role:supervisor')
        ->name('filtros.responder');

    Route::get('/gestion-radicados', [GestionClienteController::class, 'radicadosIndex'])->name('radicados.index');
    Route::get('/gestion-radicados/{id}/proceso', [GestionClienteController::class, 'radicadosProceso'])->name('radicados.proceso');
    Route::get('/gestion-radicados/{id}', [GestionClienteController::class, 'radicadosShow'])->name('radicados.show');
    Route::post('/gestion-radicados/{id}/respuesta-mesa-control', [GestionClienteController::class, 'radicadosResponderMesaControl'])
        ->middleware('role:supervisor')
        ->name('radicados.responder');

    Route::get('/gestion-aprobados', [GestionClienteController::class, 'aprobadosIndex'])->name('aprobados.index');
    Route::get('/gestion-aprobados/{id}/proceso', [GestionClienteController::class, 'aprobadosProceso'])->name('aprobados.proceso');
    Route::get('/gestion-aprobados/{id}', [GestionClienteController::class, 'aprobadosShow'])->name('aprobados.show');
    Route::post('/gestion-aprobados/{id}/respuesta-mesa-control', [GestionClienteController::class, 'aprobadosResponderMesaControl'])
        ->middleware('role:supervisor')
        ->name('aprobados.responder');

    Route::get('/gestion-desembolso', [GestionClienteController::class, 'desembolsoIndex'])->name('desembolso.index');
    Route::get('/gestion-desembolso/{id}/proceso', [GestionClienteController::class, 'desembolsoProceso'])->name('desembolso.proceso');
    Route::get('/gestion-desembolso/{id}', [GestionClienteController::class, 'desembolsoShow'])->name('desembolso.show');
    Route::post('/gestion-desembolso/{id}/respuesta-mesa-control', [GestionClienteController::class, 'desembolsoResponderMesaControl'])
        ->middleware('role:supervisor')
        ->name('desembolso.responder');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});


// routes/web.php
Route::get('/municipios/{departamentoId}', [RegistroClienteController::class, 'getMunicipios'])->name('municipios.get');


// Boton para que el asesor pueda visualizar los clientes o nuevos filtros creados por el supervisor, y pueda dar respuesta a los mismos.   
Route::post('/filtros/{id}/asignar-asesor', [GestionClienteController::class, 'asignarAsesor'])
    ->name('filtros.asignarAsesor')
    ->middleware('auth');
Route::post('/filtros/{id}/desasignar-asesor', [GestionClienteController::class, 'desasignarAsesor'])
    ->name('filtros.desasignarAsesor')
    ->middleware('auth');
    