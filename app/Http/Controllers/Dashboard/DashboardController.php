<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Funcionario;

class DashboardController extends Controller
{

public function index()
{
    $stats               = [];
    $ultimosFuncionarios = collect();
    $ultimosEquipos      = collect();

    if (auth()->user()->can('funcionarios.ver')) {
        $stats['funcionarios'] = \App\Models\Funcionario::where('estado', 'Activo')->count();

        $ultimosFuncionarios = \App\Models\Funcionario::with(['area', 'cargo'])
            ->latest()->take(5)->get();
    }

    $notificaciones = \App\Models\Notificacion::noLeidas()
        ->latest()
        ->take(10)
        ->get();

    return view('dashboard.index', compact(
        'stats',
        'ultimosFuncionarios',
        'ultimosEquipos',
        'notificaciones'
    ));
}
    // public function index()
    // {
    //     $stats = [];

    //     if (auth()->user()->can('funcionarios.ver')) {
    //         $stats['funcionarios'] = Funcionario::where('estado', 'Activo')->count();
    //         $stats['equipos']      = 0;
    //         $stats['licencias']    = 0;
    //         $stats['insumos']      = 0;
    //     }

    //     $ultimosFuncionarios = auth()->user()->can('funcionarios.ver')
    //         ? Funcionario::with(['area', 'cargo'])->latest()->take(5)->get()
    //         : collect();

    //     $ultimosEquipos = collect();

    //     return view('dashboard.index', compact(
    //         'stats',
    //         'ultimosFuncionarios',
    //         'ultimosEquipos'
    //     ));
    // }
}