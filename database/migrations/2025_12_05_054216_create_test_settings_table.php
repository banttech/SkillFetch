<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('test_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('fees', 10, 2)->nullable();
            $table->integer('marks')->nullable();
            $table->integer('timing')->nullable(); // in minutes
            $table->integer('question_per_department')->nullable();
            $table->integer('no_of_departments')->nullable();
            $table->integer('total_question')->nullable();
            $table->integer('total_marks')->nullable();
            $table->integer('passing_marks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_settings');
    }
};
