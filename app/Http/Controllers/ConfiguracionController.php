<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('configuracion.index');
    }

    public function setTema(Request $request)
    {
        $request->validate(['tema' => 'required|in:light,dark']);
        auth()->user()->update(['tema' => $request->tema]);
        return response()->json(['ok' => true]);
    }

    public function setColor(Request $request)
    {
        $request->validate(['color' => 'required|in:blue,green,slate,indigo']);
        auth()->user()->update(['color' => $request->color]);
        return response()->json(['ok' => true]);
    }
}
