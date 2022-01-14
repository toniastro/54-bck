<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 40, 2)->default(0);
            $table->string('reason')->nullable();
            $table->bigInteger('borrower_id')->unsigned()->index();
            $table->bigInteger('lender_id')->unsigned()->index()->nullable();//only filled when an offer has been accepted by borrower
            $table->boolean('granted')->default(false);
            $table->boolean('paid_back')->default(false);
            $table->timestamps();
            $table->foreign('lender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('borrower_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
