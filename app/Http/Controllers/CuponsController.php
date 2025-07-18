<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CuponsController extends Controller
{
    public function index()
    {        
        $cupons = \App\Models\Cupons::get();
        return view('cupons', compact('cupons'));
    }

    
}
