<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('customer_id');
            $table->string('reference')->unique();
            $table->string('invoice')->unique();
            $table->text('notes')->nullable();
            $table->bigInteger('amount');
            $table->string('payment');
            $table->string('no_resi')->nullable();
            $table->string('delivery_service');
            $table->string('delivery_ongkir');
            $table->string('delivery_estimation');
            $table->enum('status', ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai', 'Dibatalkan', 'Gagal']);
            $table->string('expire');
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
        Schema::dropIfExists('transactions');
    }
}
