<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employer_job_skills', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('skill_id');
            $table->unsignedBigInteger('employer_id');

            $table->timestamps();

            $table->foreign('job_id')
                  ->references('id')->on('post_job')
                  ->onDelete('cascade');

            $table->foreign('skill_id')
                  ->references('id')->on('skills')
                  ->onDelete('cascade');

            $table->foreign('employer_id')
                  ->references('id')->on('employer_details')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employer_job_skills');
    }
};
