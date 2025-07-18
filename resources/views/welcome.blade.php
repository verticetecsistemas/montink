@extends('layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <h2>Menu Principal</h2>
    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="{{ route('produtos.index') }}" class="btn btn-primary btn-lg">Produtos</a>
        <a href="{{ route('cupons.index') }}" class="btn btn-secondary btn-lg">Cupons</a>
        <a href="{{ route('pedidos.index') }}" class="btn btn-success btn-lg">Pedidos</a>
    </div>
</div>
@endsection