<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">    
    <title>Novo Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<body>
    <div class="container mt-5" style="max-width: 70%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ url('/produtos') }}" class="btn btn-secondary me-2" title="Voltar para produtos">
            <i class="fas fa-arrow-left"></i> <!-- FontAwesome -->
        </a>

            <h2 class="mb-0">
                {{ isset($produto) ? 'Edição de Produto' : 'Novo Produto' }}
            </h2>
            <button type="button" class="btn btn-success" id="comprar-produto" {{ isset($produto) ? '' : 'disabled' }}>Comprar</button>
        </div>
        <form method="POST" action="{{ route('produtos.salvar') }}">
            @csrf
            <input type="hidden" id="produto_id" name="produto_id" value="{{ isset($produto) ? $produto->id : '' }}">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required
                       value="{{ isset($produto) ? $produto->nome : '' }}" {{ isset($produto) ? 'disabled' : '' }}>
            </div>
            <div class="mb-3 d-flex align-items-end">
                <div class="flex-grow-1 me-3">
                    <label for="preco" class="form-label">Preço</label>
                    <input type="text" class="form-control" id="preco" name="preco" required style="width: 100%;"
                           value="{{ isset($produto) ? number_format($produto->preco, 2, ',', '.') : '' }}" {{ isset($produto) ? 'disabled' : '' }}>
                </div>
                <div style="width: 20%;">
                    <label for="estoque_geral" class="form-label">Estoque Geral</label>
                    <input type="number" class="form-control" id="estoque_geral" name="estoque_geral"
    value="{{ isset($produto) ? ($produto->estoque ?? $produto->estoque_geral ?? 0) : 0 }}"
    data-original="{{ isset($produto) ? ($produto->estoque ?? $produto->estoque_geral ?? 0) : 0 }}"
    min="0" {{ isset($produto) ? 'disabled' : '' }}>
                </div>
            </div>
            <button type="submit" class="btn btn-success" {{ isset($produto) ? 'disabled' : '' }}>Salvar</button>
            <button type="button" class="btn btn-warning ms-2" id="editar-produto" {{ isset($produto) ? '' : 'disabled' }}>Editar</button>
        </form>

        <!-- Tabela de Variações -->
        <div class="mt-5">
            <div class="d-flex align-items-center mb-2">
                <h4 class="mb-0 me-2">Variações</h4>
                <button type="button" class="btn btn-primary btn-sm" id="add-variacao" {{ isset($produto) ? '' : 'disabled' }}>+</button>
            </div>
            <table class="table table-bordered" id="variacoes-table">
                <thead>
                    <tr>
                        <th>Variação</th>
                        <th>Estoque</th>
                        <th>Salvar</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($variacoes))
                        @foreach($variacoes as $variacao)
                            <tr data-variacao-id="{{ $variacao->id }}">
                                <td>
                                    <input type="text" class="form-control" name="variacoes[][nome]" value="{{ $variacao->variacao }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="variacoes[][estoque]" value="{{ $variacao->estoque }}" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm salvar-variacao" disabled>Salvo</button>
                                    <button type="button" class="btn btn-warning btn-sm editar-variacao">Editar</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $('#preco').mask('000.000.000,00', {reverse: true});

        let produtoId = {{ isset($produto) ? $produto->id : 'null' }};

        // Salva o produto via AJAX e habilita o botão de variação, o botão Editar e o botão Comprar
        $('form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);

            // Se produtoId já existe, faz update, senão faz create
            let url = produtoId
                ? '{{ url("/produtos") }}/atualizar'
                : form.attr('action');
            let method = 'POST';

            $.ajax({
                url: url,
                method: method,
                data: form.serialize() + (produtoId ? '&id=' + produtoId : ''),
                success: function(response) {
                    if (response.success && response.produto_id) {
                        produtoId = response.produto_id;
                        $('#add-variacao').prop('disabled', false);
                        $('#editar-produto').prop('disabled', false);
                        $('#comprar-produto').prop('disabled', false); // Habilita o botão Comprar
                        form.find('input, button[type="submit"]').prop('disabled', true);
                    } else {
                        alert('Erro ao salvar produto.');
                    }
                },
                error: function(xhr) {
                    alert('Erro ao salvar produto.');
                }
            });
        });

        // Ao clicar em Editar, habilita os campos Nome e Preço para edição
        $('#editar-produto').on('click', function() {
            $('#nome, #preco, #estoque_geral').prop('disabled', false); // habilita também o estoque_geral
            $(this).prop('disabled', true);
            $('button[type="submit"]').prop('disabled', false);
        });

        // Adiciona nova linha de variação com botão Editar desabilitado
        $('#add-variacao').on('click', function() {
            $('#variacoes-table tbody').append(`
                <tr>
                    <td><input type="text" class="form-control" name="variacoes[][nome]" placeholder="Nome da variação"></td>
                    <td><input type="number" class="form-control" name="variacoes[][estoque]" min="0" placeholder="Estoque"></td>
                    <td>
                        <button type="button" class="btn btn-success btn-sm salvar-variacao">Salvar</button>
                        <button type="button" class="btn btn-warning btn-sm editar-variacao" disabled>Editar</button>
                    </td>
                </tr>
            `);
        });

        // Função para atualizar o Estoque Geral
        function atualizarEstoqueGeral() {
            let total = 0;
            let linhas = $('#variacoes-table tbody tr').length;
            if (linhas > 0) {
                $('#variacoes-table tbody tr').each(function() {
                    let val = parseInt($(this).find('input[name="variacoes[][estoque]"]').val());
                    if (!isNaN(val)) total += val;
                });
                $('#estoque_geral').val(total);
            } else {
                // Mantém o valor original vindo do backend
                let original = $('#estoque_geral').data('original');
                if (typeof original !== 'undefined') {
                    $('#estoque_geral').val(original);
                }
            }
        }

        $(document).ready(function() {
            atualizarEstoqueGeral();
        });

        // Salva a variação via AJAX, incluindo produto_id
        $(document).on('click', '.salvar-variacao', function() {
            let row = $(this).closest('tr');
            let variacao = row.find('input[name="variacoes[][nome]"]').val().trim();
            let estoque = row.find('input[name="variacoes[][estoque]"]').val().trim();
            let variacaoId = row.data('variacao-id') || null;

            // Validação dos campos
            if (variacao === '') {
                alert('O campo Variação é obrigatório.');
                row.find('input[name="variacoes[][nome]"]').focus();
                return;
            }
            if (estoque === '' || isNaN(estoque) || parseInt(estoque) < 0) {
                alert('O campo Estoque deve ser um número igual ou maior que zero.');
                row.find('input[name="variacoes[][estoque]"]').focus();
                return;
            }
            if (!produtoId) {
                alert('Produto não identificado.');
                return;
            }

            // Define se é update ou create
            let url = variacaoId
                ? '{{ route("variacoes.atualizar") }}'
                : '{{ route("variacoes.salvar") }}';

            let data = {
                _token: '{{ csrf_token() }}',
                nome: variacao,
                estoque: estoque,
                produto_id: produtoId
            };
            if (variacaoId) data.id = variacaoId;

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    row.find('input').prop('readonly', true);
                    row.find('.salvar-variacao')
                        .removeClass('btn-success')
                        .addClass('btn-secondary')
                        .prop('disabled', true)
                        .text('Salvo');
                    row.find('.editar-variacao').prop('disabled', false);

                    // Se for novo, armazena o id retornado
                    if (response.variacao_id) {
                        row.attr('data-variacao-id', response.variacao_id);
                    }

                    // Atualiza o estoque geral
                    atualizarEstoqueGeral();
                },
                error: function(xhr) {
                    alert('Erro ao salvar a variação. Tente novamente.');
                }
            });
        });

        // Ao clicar em Editar, habilita os campos para edição e reabilita o botão Salvar
        $(document).on('click', '.editar-variacao', function() {
            let row = $(this).closest('tr');
            row.find('input').prop('readonly', false);
            row.find('.salvar-variacao')
                .removeClass('btn-secondary')
                .addClass('btn-success')
                .prop('disabled', false)
                .text('Salvar');
            $(this).prop('disabled', true);
        });

        // Atualiza ao editar e salvar novamente
        $(document).on('input', 'input[name="variacoes[][estoque]"]', function() {
            atualizarEstoqueGeral();
        });

        // Atualiza ao adicionar nova variação (campo em branco não soma)
        $('#add-variacao').on('click', function() {
            atualizarEstoqueGeral();
        });

        
        $(document).ready(function() {
            atualizarEstoqueGeral();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // Botão Comprar
        $('#comprar-produto').on('click', function() {
            // Coleta dados do produto
            let produto = {
                id: $('#produto_id').val(),
                nome: $('#nome').val(),
                preco: $('#preco').val(),
                estoque_geral: $('#estoque_geral').val(),
                variacoes: []
            };

            // Coleta variações
            $('#variacoes-table tbody tr').each(function() {
                let nome = $(this).find('input[name="variacoes[][nome]"]').val();
                let estoque = $(this).find('input[name="variacoes[][estoque]"]').val();
                if (nome && estoque) {
                    produto.variacoes.push({
                        nome: nome,
                        estoque: estoque
                    });
                }
            });

            // Envia para o carrinho (POST)
            $.ajax({
                url: '{{ url("/carrinho/adicionar") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    produto: produto
                },
                success: function(response) {
                    // Redireciona para a página do carrinho
                    window.location.href = '{{ url("/carrinho") }}';
                },
                error: function() {
                    alert('Erro ao adicionar ao carrinho.');
                }
            });
        });
    </script>
</body>
</html>