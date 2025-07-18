@extends('layouts.app')

@section('content')
<div class="container mt-5" style="max-width: 700px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ url('/produtos') }}" class="btn btn-secondary me-2" title="Voltar para produtos">
            <i class="fas fa-arrow-left"></i> <!-- FontAwesome -->
        </a>
        <h2 class="mb-0">Carrinho</h2>
    </div>

    
    @php
        $itens = session('carrinho.pedido') ? [session('carrinho.pedido')] : [];
        
    @endphp
    @if(count($itens) > 0 && $itens[0])
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Variação</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                    <th>Ação</th> 
                </tr>
            </thead>
            <tbody>
                @foreach($itens[0] as $index => $item)
                    <tr>
                        <td>{{ $item['nome'] ?? '' }}</td>
                        <td>{{ $item['variacao'] ?? '-' }}</td>
                        <td>{{ $item['quantidade'] ?? 1 }}</td>
                        <td>
                            {{ isset($item['preco']) ? 'R$ ' . number_format(floatval(str_replace(',', '.', str_replace('.', '', $item['preco']))), 2, ',', '.') : '' }}
                        </td>
                        <td>
                            {{ isset($item['subtotal']) ? 'R$ ' . number_format($item['subtotal'], 2, ',', '.') : '' }}
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm remover-item" data-index="{{ $index }}">Remover</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($produto)
        <div class="row">
            <div class="col-md-7">
                <div class="mb-3">
                    <label class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" value="{{ $produto['nome'] ?? '' }}" readonly>
                </div>
                @if(
                    (!isset($produto['variacoes']) || !is_array($produto['variacoes']) || count($produto['variacoes']) === 0)
                    && isset($produto['estoque_geral'])
                )
                    <div class="mb-3">
                        <label class="form-label">Estoque</label>
                        <input type="text" class="form-control" value="{{ $produto['estoque_geral'] }}" readonly>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label">Preço Unitário</label>
                    <input type="text" id="preco" class="form-control" value="{{ $produto['preco'] ?? '' }}" readonly>
                </div>
                @if(isset($produto['variacoes']) && is_array($produto['variacoes']) && count($produto['variacoes']) > 0)
                    <div class="mb-3">
                        <label class="form-label">Variação</label>
                        <select id="variacao" class="form-select">
                            @foreach($produto['variacoes'] as $variacao)
                                @if(isset($variacao['estoque']) && $variacao['estoque'] > 0)
                                    <option value="{{ $variacao['nome'] }}">
                                        {{ $variacao['nome'] }} 
                                        (Estoque: {{ $variacao['estoque'] }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="mb-3 d-flex align-items-end">
                    <div>
                        <label class="form-label">Quantidade</label>
                        <input type="number" id="quantidade" class="form-control" value="1" min="1" style="width: 120px;">
                    </div>
                    <button type="button" id="incluir-carrinho" class="btn btn-primary ms-3 mb-1">Incluir</button>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resumo do Pedido</h5>
                        @php
                            // Soma total dos itens já no carrinho
                            $totalProdutos = 0;
                            $totalQuantidade = 0;
                            foreach($itens as $item) {
                                $totalProdutos += isset($item['subtotal']) ? floatval($item['subtotal']) : 0;
                                $totalQuantidade += isset($item['quantidade']) ? intval($item['quantidade']) : 0;
                            }
                            // Calcula frete acumulado
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
                            $desconto = session('carrinho.desconto', 0);
                            $totalGeral = max(0, $totalProdutos + ($freteResumo > 0 ? $freteResumo : 0) - $desconto);
                        @endphp
                        <p class="mb-2">Subtotal: <span id="subtotal">{{ 'R$ ' . number_format($totalProdutos, 2, ',', '.') }}</span></p>
                        <p class="mb-2">Frete: <span id="frete">{{ $freteResumo === 0 ? 'Grátis' : 'R$ ' . number_format($freteResumo, 2, ',', '.') }}</span></p>
                        <p class="mb-2">Desconto: 
    <span id="desconto">
        {{ 'R$ ' . number_format($desconto, 2, ',', '.') }}
    </span>
</p>
                        <hr>
                        <p class="fw-bold">Total: <span id="total">{{ 'R$ ' . number_format($totalGeral, 2, ',', '.') }}</span></p>
                        {{-- Botão Fechar Pedido dentro do card --}}
                        <form id="form-fechar-pedido" action="{{ url('/carrinho/pedido') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="button" id="btn-fechar-pedido" class="btn btn-success w-100"
                                {{ (isset($itens[0]) && count($itens[0]) > 0) ? '' : 'disabled' }}>
                                <i class="fas fa-check"></i> Fechar Pedido
                            </button>

                            {{-- Campos adicionais, inicialmente ocultos --}}
                            <div id="campos-adicionais" style="display: none;">
                                <hr>
                                <div class="mb-2">
                                    <label for="cep" class="form-label">CEP</label>
                                    <input type="text" name="cep" id="cep" class="form-control" maxlength="9" placeholder="Digite seu CEP" required>
                                </div>
                                <div class="mb-2">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Digite seu e-mail" required>
                                </div>
                                <button type="submit" id="btn-finalizar-compra" class="btn btn-primary w-100 mt-2">
                                    <i class="fas fa-shopping-cart"></i> Finalizar Compra
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">Nenhum produto no carrinho.</div>
    @endif
</div>

<!-- Modal ViaCEP -->
<div class="modal fade" id="modalViaCep" tabindex="-1" aria-labelledby="modalViaCepLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalViaCepLabel">Endereço encontrado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="modalViaCepBody">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
    function parseCurrencyToFloat(str) {
       if (!str) return 0;
    return parseFloat(str.replace(/[^\d,.-]/g, '').replace(',', '.'));
    }
    
    function parsePreco(precoStr) {
        
        if (!precoStr) return 0;
        return parseFloat(precoStr.replace(/\./g, '').replace(',', '.'));
    }

    function formatPreco(valor) {
        return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function calcularResumo() {
        let preco = parsePreco(document.getElementById('preco').value);
        let quantidade = parseInt(document.getElementById('quantidade').value) || 1;
        let desconto = parseCurrencyToFloat(document.getElementById('desconto').innerText);
        let subtotal = preco * quantidade;
        let frete = 0;

        if (subtotal < 52) {
            frete = 20;
        } else if (subtotal >= 52 && subtotal <= 166.59) {
            frete = 15;
        } else if (subtotal >= 200) {
            frete = 0;
        } else {
            frete = 20;
        }

        console.log('desconto:', desconto);
        let total = subtotal + (frete > 0 ? frete : 0);
        total=total - desconto;

        document.getElementById('subtotal').innerText = formatPreco(subtotal);
        document.getElementById('frete').innerText = frete === 0 ? 'Grátis' : formatPreco(frete);
        document.getElementById('total').innerText = formatPreco(total);
    }

    function atualizarResumoCard() {
        fetch("{{ url('/carrinho/resumo') }}")
            .then(response => response.json())
            .then(resumo => {
                document.getElementById('subtotal').innerText = resumo.subtotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                document.getElementById('frete').innerText = resumo.frete === 0 ? 'Grátis' : resumo.frete.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                //document.getElementById('total').innerText = resumo.total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        calcularResumo();
        atualizarResumoCard();
        document.getElementById('quantidade').addEventListener('input', function() {
            calcularResumo(); // Atualiza subtotal do produto atual
            atualizarResumoCard(); // Atualiza resumo do carrinho (com desconto)
        });

        // Botão Incluir
        document.getElementById('incluir-carrinho').addEventListener('click', function() {
            let nome = "{{ $produto['nome'] ?? '' }}";
            let id = "{{ $produto['id'] ?? '' }}"; // <-- Adiciona o ID do produto
            let preco = document.getElementById('preco').value;
            let quantidade = parseInt(document.getElementById('quantidade').value) || 1;
            let subtotal = parsePreco(preco) * quantidade;
            let frete = 0;
            if (subtotal < 52) {
                frete = 20;
            } else if (subtotal >= 52 && subtotal <= 166.59) {
                frete = 15;
            } else if (subtotal >= 200) {
                frete = 0;
            } else {
                frete = 20;
            }
            let total = subtotal + (frete > 0 ? frete : 0);

            let variacao = null;
            let variacaoSelect = document.getElementById('variacao');
            if (variacaoSelect) {
                variacao = variacaoSelect.value;
            }

            // Envia via AJAX para salvar na sessão
            fetch("{{ url('/carrinho/incluir') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id: id, // <-- Inclui o ID do produto
                    nome: nome,
                    preco: preco,
                    quantidade: quantidade,
                    variacao: variacao,
                    subtotal: subtotal,
                    frete: frete,
                    total: total
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    atualizarResumoCard();
                    alert('Produto incluído no carrinho!');
                    location.reload(); 
                } else {
                    alert('Erro ao incluir no carrinho.');
                }
            })
            .catch(() => alert('Erro ao incluir no carrinho.'));
        });

        // Botão Remover
        document.querySelectorAll('.remover-item').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if(confirm('Deseja remover este item do carrinho?')) {
                    let index = this.getAttribute('data-index');
                    fetch("{{ url('/carrinho/remover') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ index: index })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success){
                            atualizarResumoCard();
                            location.reload(); // Atualiza a tabela e o resumo
                        } else {
                            alert('Erro ao remover item.');
                        }
                    })
                    .catch(() => alert('Erro ao remover item.'));
                }
            });
        });

        // Fechar Pedido: mostra campos adicionais e troca botão
        document.getElementById('btn-fechar-pedido').addEventListener('click', function() {
            document.getElementById('campos-adicionais').style.display = 'block';
            this.style.display = 'none';
        });

        // Busca endereço ao preencher o CEP
        document.getElementById('cep').addEventListener('blur', function() {
            let cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        
                        if (data && !data.erro) {
                            let endereco = `
                                <strong>Logradouro:</strong> ${data.logradouro || '-'}<br>
                                <strong>Bairro:</strong> ${data.bairro || '-'}<br>
                                <strong>Cidade:</strong> ${data.localidade || '-'}<br>
                                <strong>UF:</strong> ${data.uf || '-'}
                            `;
                            document.getElementById('modalViaCepBody').innerHTML = endereco;
                            let modal = new bootstrap.Modal(document.getElementById('modalViaCep'));
                            modal.show();
                        } else {
                            alert('CEP não encontrado.');
                        }
                    })
                   
            }
        });

        document.getElementById('form-fechar-pedido').addEventListener('submit', function(e) {
    // Validação do e-mail
    var email = document.getElementById('email').value;
    var emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    if (!emailValido) {
        e.preventDefault();
        alert('Por favor, informe um e-mail válido.');
        document.getElementById('email').focus();
        return false;
    }

    // Captura o HTML do resumo do pedido
    var resumoHtml = document.querySelector('.card-body').innerHTML;
    var inputResumo = document.createElement('input');
    inputResumo.type = 'hidden';
    inputResumo.name = 'resumo_html';
    inputResumo.value = resumoHtml;
    this.appendChild(inputResumo);

    // Envia os itens do carrinho (em JSON)
    var itens = @json(session('carrinho.pedido', []));
    var inputItens = document.createElement('input');
    inputItens.type = 'hidden';
    inputItens.name = 'itens';
    inputItens.value = JSON.stringify(itens);
    this.appendChild(inputItens);

    // Envia o frete como decimal
    var freteText = document.getElementById('frete').innerText;
    var inputFrete = document.createElement('input');
    inputFrete.type = 'hidden';
    inputFrete.name = 'frete';
    inputFrete.value = (freteText.trim().toLowerCase() === 'grátis') ? 0 : parseCurrencyToFloat(freteText);
    this.appendChild(inputFrete);

    // Envia o total como decimal
    var totalText = document.getElementById('total').innerText;
    var inputTotal = document.createElement('input');
    inputTotal.type = 'hidden';
    inputTotal.name = 'total';
    inputTotal.value = parseCurrencyToFloat(totalText);
    this.appendChild(inputTotal);

    // Captura o valor do desconto
    var descontoText = document.getElementById('desconto').innerText;
    var inputDesconto = document.createElement('input');
    inputDesconto.type = 'hidden';
    inputDesconto.name = 'desconto';
    inputDesconto.value = parseCurrencyToFloat(descontoText);
    this.appendChild(inputDesconto);

    // Substitua o envio padrão pelo AJAX para redirecionar após sucesso
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            window.location.href = '/pedidos';
        } else {
            alert('Erro ao finalizar pedido.');
        }
    })
    .catch(() => alert('Erro ao finalizar pedido.'));
});

    });
</script>
@endsection


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>