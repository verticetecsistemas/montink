<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pendente'); // Exemplo: pendente, pago, enviado, cancelado
            $table->decimal('total', 10, 2); // Total do pedido
            $table->decimal('frete', 10, 2)->default(0); // Frete do pedido
            $table->json('itens'); // Itens do pedido
            $table->timestamp('data_pedido')->useCurrent(); // Data do pedido
            $table->unsignedBigInteger('user_id')->nullable(); // ID do usuário que fez o pedido
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); // Se você tiver uma tabela de usuários
            $table->string('email')->nullable(); // Email do usuário, se necessário
            $table->string('cep')->nullable(); // Telefone do usuário, se necessário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};
