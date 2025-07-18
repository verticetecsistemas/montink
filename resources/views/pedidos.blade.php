@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('welcome') }}" class="btn btn-secondary me-2">Voltar ao Menu</a>

    <h2>Pedidos</h2>
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>Data do Pedido</th>
                <th>ID</th>
                <th>Status</th>
                <th>Total</th>
                <th>Frete</th>
                <th>Email</th>
                <th>CEP</th>
                <th>Itens</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
            <tr>
                <td>{{ \Carbon\Carbon::parse($pedido->data_pedido)->format('d/m/Y H:i') }}</td>
                <td>{{ $pedido->id }}</td>
                <td>{{ $pedido->status }}</td>
                <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                <td>{{ $pedido->frete == 0 ? 'Grátis' : 'R$ ' . number_format($pedido->frete, 2, ',', '.') }}</td>
                <td>{{ $pedido->email }}</td>
                <td>{{ $pedido->cep }}</td>
                <td>
                    <button class="btn btn-sm btn-primary" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#itens-{{ $pedido->id }}"
                        aria-expanded="false"
                        aria-controls="itens-{{ $pedido->id }}">
                        +
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="8" class="p-0 border-0">
                    <div class="collapse" id="itens-{{ $pedido->id }}">
                        <div class="card card-body">
                            <strong>Itens do Pedido:</strong>
                            <ul class="mb-0">
                                @foreach(is_string($pedido->itens) ? json_decode($pedido->itens, true) : $pedido->itens as $item)
                                    <li>
                                        {{ $item['nome'] ?? '' }} 
                                        @if(!empty($item['variacao'])) 
                                            ({{ $item['variacao'] }}) 
                                        @endif
                                        - Qtd: {{ $item['quantidade'] ?? 1 }} 
                                        - Subtotal: R$ {{ number_format($item['subtotal'] ?? 0, 2, ',', '.') }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>