<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employer_job_experience', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('experience_id');
            $table->unsignedBigInteger('employer_id'); // References employer_details table

            $table->timestamps();

            // Foreign Key: Job
            $table->foreign('job_id')
                ->references('id')
                ->on('post_job')
                ->onDelete('cascade');

            // Foreign Key: Experience
            $table->foreign('experience_id')
                ->references('id')
                ->on('experiences')
                ->onDelete('cascade');

            // Foreign Key: Employer (employer_details table)
            $table->foreign('employer_id')
                ->references('id')
                ->on('employer_details')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employer_job_experience');
    }
};
