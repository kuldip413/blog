<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('title');
            $table->text('description');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completred_at')->nullable();
            $table->enum('status',array('assigned','inProgress','completed'));
            // $table->unsignedBigInteger('id');
            $table->timestamps();
            $table->unsignedInteger('assigned_to');
            $table->bigInteger('assigned_by');

            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
