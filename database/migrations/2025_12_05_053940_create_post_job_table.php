<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('post_job', function (Blueprint $table) {
            $table->id();

            // employer_details table ID
            $table->unsignedBigInteger('employer_id');

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('years')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            // Correct Foreign Key
            $table->foreign('employer_id')
                ->references('id')->on('employer_details')   
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_job');
    }
};
