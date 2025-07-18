<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarrinhoController extends Controller
{
    public function store(Request $request)
    {

        $item = $request->only(['id','nome', 'preco', 'quantidade', 'variacao', 'subtotal', 'frete', 'total']);    

    // Recupera carrinho atual ou cria vazio
    $carrinho = session('carrinho.pedido', []);

    $carrinho[] = $item;

    // Salva de volta na sessão
    session(['carrinho.pedido' => $carrinho]);
    return response()->json(['success' => true]);
    }


    public function resumo()
    {
        
        $itens = session('carrinho.pedido') ? session('carrinho.pedido') : [];
        $totalProdutos = 0;

         

        if (!empty($itens)) {
            foreach ($itens as $item) {
                $totalProdutos += $item['subtotal'];
                //Log::info($item);   
            }
        }
        $freteResumo = 0;
        if ($totalProdutos < 52) {
            $freteResumo = 20;
        } else if ($totalProdutos >= 52 && $totalProdutos <= 166.59) {
            $freteResumo = 15;
        } else if ($totalProdutos >= 200) {
            $freteResumo = 0;
        } else {
            $freteResumo = 20;
        }
        $totalGeral = $totalProdutos + ($freteResumo > 0 ? $freteResumo : 0);
        return response()->json([
            'itens' => $itens,
            'subtotal' => $totalProdutos,
            'frete' => $freteResumo,
            'total' => $totalGeral
        ]);
    }

    public function delete(Request $request)
    {
        $index = $request->input('index');
        $carrinho = session('carrinho.pedido', []);

        if (isset($carrinho[$index])) {
            unset($carrinho[$index]);
            // Reindexa o array para evitar buracos nos índices
            $carrinho = array_values($carrinho);
            session(['carrinho.pedido' => $carrinho]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Item não encontrado.']);
    }

    public function storeOrder(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->id(); // Assumindo que o usuário está autenticado
        $data['status'] = 'pendente'; // Status inicial do pedido
        $data['data_pedido'] = now(); // Data atual do pedido
        $data['frete'] = $request->input('frete', 0); // Frete, se necessário
        $data['total'] = $request->input('total', 0); // Total do pedido
        $data['desconto'] = $request->input('desconto', 0); // Total do pedido
        $data['itens'] = $request->input('itens', []); // Itens do pedido
        
        // Salva o pedido no banco de dados
        $pedido = \App\Models\Pedidos::create($data);

        // Baixa de estoque
        // Garante que $data['itens'] seja um array
        if (is_string($data['itens'])) {
            $data['itens'] = json_decode($data['itens'], true);
        }
        foreach ($data['itens'] as $item) {
            $produto = \App\Models\Produtos::find($item['id']);
            if ($produto) {
                // Baixa estoque geral
                if (isset($produto->estoque) && isset($item['quantidade'])) {
                    $produto->estoque = max(0, $produto->estoque - $item['quantidade']);
                    $produto->save();
                }

                // Baixa estoque da variação, se houver
                if (!empty($item['variacao'])) {
                    // Supondo que Produto tem relação 'variacoes' e cada variação tem campo 'nome' e 'estoque'
                    $variacao = $produto->estoques()->where('variacao', $item['variacao'])->first();
                    if ($variacao && isset($variacao->estoque)) {
                        $variacao->estoque = max(0, $variacao->estoque - $item['quantidade']);
                        $variacao->save();
                    }
                }
            }
        }

        $email = $request->input('email');
        $resumoHtml = $request->input('resumo_html');

    
        // Envio do e-mail (exemplo usando Mailable)
        \Mail::send([], [], function ($message) use ($email, $resumoHtml) {
            $message->to($email)
                ->subject('Resumo do seu pedido')
                ->html($resumoHtml);
        });

        //return redirect()->back()->with('success', 'Pedido finalizado e e-mail enviado!');
        // Limpa o carrinho após salvar o pedido
        session()->forget('carrinho.pedido');
        session()->forget('carrinho.desconto');
        session()->forget('carrinho.cupom_nome');
        session()->forget('carrinho.total');

        return response()->json(['success' => true, 'pedido' => $pedido]);
    }

}