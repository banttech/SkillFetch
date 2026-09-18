<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supervisor_applied_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('job_id');

            $table->enum('status', ['applied', 'accepted', 'rejected'])
                  ->default('applied');

            $table->timestamps();

            $table->foreign('supervisor_id')
                ->references('id')->on('supervisors')
                ->onDelete('cascade');

            $table->foreign('job_id')
                ->references('id')->on('post_job')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supervisor_applied_jobs');
    }
};
