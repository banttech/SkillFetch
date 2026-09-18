<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('supervisor_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('experience_id');
            $table->timestamps();

            $table->foreign('supervisor_id')
                ->references('id')->on('supervisors')
                ->onDelete('cascade');

            $table->foreign('experience_id')
                ->references('id')->on('experiences')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supervisor_experiences');
    }
};
