<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('answer_type'); // e.g. text, MCQ, checkbox, etc.
            $table->timestamps();

            // Foreign Key
            $table->foreign('department_id')
                ->references('id')->on('departments')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
