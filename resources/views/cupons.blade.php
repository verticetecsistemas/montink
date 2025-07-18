@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('welcome') }}" class="btn btn-secondary me-2">Voltar ao Menu</a>
    <h2>Cupons</h2>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Desconto (%)</th>
                <th>Data Início</th>
                <th>Data Fim</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cupons as $cupom)
            <tr>
                <td>{{ $cupom->nome }}</td>
                <td>{{ $cupom->desconto }}%</td>
                <td>{{ \Carbon\Carbon::parse($cupom->data_inicio)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cupom->data_fim)->format('d/m/Y') }}</td>
                <td>
                    @if($cupom->ativo === 1)
                        @if(\Carbon\Carbon::parse($cupom->data_inicio)->lte(\Carbon\Carbon::today()))
                            <button class="btn btn-success btn-sm aplicar-cupom"
                                data-desconto="{{ $cupom->desconto }}"
                                data-nome="{{ $cupom->nome }}">
                                Aplicar
                            </button>
                        @endif
                    @else
                        <button class="btn btn-secondary btn-sm" disabled>Utilizado</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.aplicar-cupom').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var desconto = parseFloat(this.getAttribute('data-desconto'));
        var nomeCupom = this.getAttribute('data-nome');

        fetch("{{ route('aplicar.cupom') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                desconto: desconto,
                nome: nomeCupom
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                // Chama a rota para atualizar o status do cupom
                fetch('/cupons/aplica', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ nome: nomeCupom })
                })
                .then(() => {
                    alert('Cupom aplicado! Desconto: R$ ' + data.valor_desconto.toFixed(2));
                    window.location.reload();
                });
            } else {
                alert(data.message || 'Nenhum pedido em andamento.');
            }
        })
        .catch(() => {
            alert('Nenhum pedido em andamento.');
        });
    });
});
</script>
@endsection