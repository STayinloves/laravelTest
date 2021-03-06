<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestRejectUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contest_reject_user', function (Blueprint $table) {
            $table->integer('id', 1)->index()->unsigned();
            $table->integer('contest_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();

            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contest_reject_user');

    }
}
