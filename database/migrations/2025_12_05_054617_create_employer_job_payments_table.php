<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employer_job_payments', function (Blueprint $table) {
            $table->id();

            // Foreign key to post_job table
            $table->unsignedBigInteger('job_id');

            $table->string('transaction_id')->nullable();
            $table->string('payment_status')->default('pending'); // pending, success, failed
            $table->string('payment_order_id')->nullable(); // Razorpay order ID or other provider ID

            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('job_id')
                ->references('id')->on('post_job')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employer_job_payments');
    }
};
