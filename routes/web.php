<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\CuponsController;
use Illuminate\Http\Request;
use App\Models\Pedidos;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/produtos', function () {
    return view('produtos');
});

Route::get('/pedidos', function () {
    return view('pedidos');
});

Route::get('/cupons', function () {
    return view('cupons');
});

Route::get('/produtos', [ProdutosController::class, 'index'])->name('produtos.index');
Route::get('/pedidos', [PedidosController::class, 'index'])->name('pedidos.index');
Route::get('/cupons', [CuponsController::class, 'index'])->name('cupons.index');

Route::get('/produtos/novo', function () {
    return view('produtos_dados');
})->name('produtos.novo');

Route::post('/produtos/salvar', [ProdutosController::class, 'store'])->name('produtos.salvar');
Route::post('/produtos/atualizar', [ProdutosController::class, 'update'])->name('produtos.atualizar');
Route::get('/produtos/{id}/editar', [ProdutosController::class, 'edit'])->name('produtos.editar');

Route::post('/variacoes/salvar', [EstoqueController::class, 'store'])->name('variacoes.salvar');
Route::post('/variacoes/atualizar', [EstoqueController::class, 'update'])->name('variacoes.atualizar');
Route::post('/carrinho/adicionar', function (\Illuminate\Http\Request $request) {
    session(['carrinho.produto' => $request->input('produto')]);
    return response()->json(['success' => true]);
});

Route::get('/carrinho', function () {
    // Exibe a página do carrinho (crie a view se necessário)
    $produto = session('carrinho.produto');
    return view('carrinho', compact('produto'));
});

Route::post('/carrinho/incluir', [CarrinhoController::class, 'store'])->name('carrinho.incluir');
Route::post('/carrinho/remover', [CarrinhoController::class, 'delete']);


Route::get('/carrinho/resumo', [CarrinhoController::class, 'resumo'])->name('carrinho.resumo');

Route::post('/carrinho/pedido', [CarrinhoController::class, 'storeOrder'])->name('carrinho.pedido'); 


Route::post('/cupons/aplica', function (\Illuminate\Http\Request $request) {
    \App\Models\Cupons::where('nome', $request->input('nome'))->update(['ativo' => false]);
    return response()->json(['success' => true]);
});

Route::post('/aplicar-cupom', function (\Illuminate\Http\Request $request) {
    $carrinho = session('carrinho.pedido');
    if (!$carrinho || !is_array($carrinho) || count($carrinho) === 0) {
        return response()->json(['success' => false, 'message' => 'Não há pedido em andamento.']);
    }

    $descontoPercentual = floatval($request->input('desconto', 0));
    $total = 0;
    foreach ($carrinho as $item) {
        $total += isset($item['subtotal']) ? $item['subtotal'] : 0;
    }

    $valorDesconto = ($total * $descontoPercentual) / 100;
    session(['carrinho.desconto' => $valorDesconto]);
    session(['carrinho.cupom_nome' => $request->input('nome')]);
    session(['carrinho.total' => $total - $valorDesconto]);

    return response()->json([
        'success' => true,
        'valor_desconto' => $valorDesconto,
        'novo_total' => $total - $valorDesconto
    ]);
})->name('aplicar.cupom');


Route::post('/webhook/pedido-status', function(Request $request) {
    $pedidoId = $request->input('id');
    $status = $request->input('status');

    $pedido = Pedidos::find($pedidoId);

    if (!$pedido) {
        return response()->json(['success' => false, 'message' => 'Pedido não encontrado.'], 404);
    }

    if (strtolower($status) === 'cancelado') {
        $pedido->delete();
        return response()->json(['success' => true, 'message' => 'Pedido removido.']);
    } else {
        $pedido->status = $status;
        $pedido->save();
        return response()->json(['success' => true, 'message' => 'Status atualizado.']);
    }
});

