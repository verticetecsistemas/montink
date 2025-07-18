<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produtos;
use App\Models\Estoque;
use Illuminate\Support\Facades\Log;

class ProdutosController extends Controller
{
    public function index()
    {
        $produtos = \App\Models\Produtos::paginate(5); // Paginação de 5 por página
        return view('produtos', compact('produtos'));
    }

    public function edit($id)
    {
        $produto = Produtos::findOrFail($id);
        $variacoes = $produto->estoques()->get(); 
        //$estoque = $produto->estoque;        
        return view('produtos_dados', compact('produto', 'variacoes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|string|max:20',
            'estoque_geral' => 'required|integer|min:0',
        ]);

        $produto = new Produtos();
        $produto->nome = $request->nome;
        
        $produto->preco = str_replace(['.', ','], ['', '.'], $request->preco);
        $produto->estoque = $request->estoque_geral;
        $produto->save();

       return response()->json(['success' => true, 'produto_id' => $produto->id]);
    }

    public function update(Request $request)
    {
        $produto = Produtos::find($request->id);
        $produto->nome = $request->nome;
        $produto->estoque = $request->estoque_geral;
        $produto->preco = str_replace(['.', ','], ['', '.'], $request->preco);
        $produto->save();

        return response()->json(['success' => true, 'produto_id' => $produto->id]);
    }

   
}
