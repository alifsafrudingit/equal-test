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
    Schema::create('transactions', function (Blueprint $table) {
      $table->id();

      $table->enum('type', ['pembelian', 'penjualan']);
      $table->date('date');
      $table->integer('qty');
      $table->integer('cost');
      $table->integer('price');
      $table->bigInteger('total_cost');
      $table->integer('qty_balance');
      $table->bigInteger('value_balance');
      $table->bigInteger('hpp');

      $table->softDeletes();
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
};
