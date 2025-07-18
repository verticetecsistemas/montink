<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedidosController extends Controller
{
    //
    public function index()
    {
        $pedidos = \App\Models\Pedidos::where('user_id', auth()->id())->get();
        return view('pedidos', compact('pedidos'));
    }
}
