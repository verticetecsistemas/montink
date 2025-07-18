<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estoque; 
use Illuminate\Support\Facades\Validator;

class EstoqueController extends Controller
{
    /**
     * Salva uma nova variação no estoque.
     */
    public function store(Request $request)
    {
        // Validação dos campos
        $validator = Validator::make($request->all(), [
            'produto_id' => 'required|exists:produtos,id', 
            'nome' => 'required|string|max:255',
            'estoque' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Salva a variação no banco de dados
        $variacao = new Estoque();
        $variacao->produto_id = $request->input('produto_id'); 
        $variacao->variacao = $request->input('nome');
        $variacao->estoque = $request->input('estoque');
        // $variacao->produto_id = $request->input('produto_id');
        $variacao->save();

        return response()->json([
            'success' => true,
            'message' => 'Variação salva com sucesso!',
            'data' => $variacao
        ]);
    }

    public function update(Request $request)
    {
        // Validação dos campos
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'estoque' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Atualiza a variação no banco de dados
        $variacao = Estoque::findOrFail($id);
        $variacao->variacao = $request->input('nome');
        $variacao->estoque = $request->input('estoque');
        $variacao->save();

        return response()->json([
            'success' => true,
            'message' => 'Variação atualizada com sucesso!',
            'data' => $variacao
        ]);
    }
}