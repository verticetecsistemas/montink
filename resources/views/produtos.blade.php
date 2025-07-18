<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('welcome') }}" class="btn btn-secondary me-2">Voltar ao Menu</a>
            <h1 class="mb-0">Cadastro de Produtos</h1>
            <div>
                
                <a href="{{ route('produtos.novo') }}" class="btn btn-success">Novo</a>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produtos as $produto)
                <tr>
                    <td>{{ $produto->id }}</td>
                    <td>{{ $produto->nome }}</td>
                    <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('produtos.editar', $produto->id) }}" class="btn btn-primary btn-sm">Editar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $produtos->links() }} <!-- Adiciona os links de paginação -->
    </div>
</body>
</html>