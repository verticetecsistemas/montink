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
        Schema::table('cupons', function (Blueprint $table) {            
            $table->unsignedBigInteger('user_id')->nullable()->after('id'); // ID do usuário que fez o pedido
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); // Se você tiver uma tabela de usuários            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
