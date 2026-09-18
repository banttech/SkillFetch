<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('supervisor_test_fees', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('test_id')->nullable();

            // Razorpay & transaction fields
            $table->string('razorpay_order_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_status')->default('pending'); // pending, success, failed

            /**
             * TestSetting Snapshot Columns
             * (Stored to preserve test details even if TestSetting updates later)
             */
            $table->string('name')->nullable();
            $table->decimal('fees', 10, 2)->nullable();
            $table->integer('timing')->nullable();
            $table->integer('marks')->nullable();
            $table->integer('question_per_department')->nullable();
            $table->integer('no_of_departments')->nullable();
            $table->integer('total_question')->nullable();
            $table->integer('total_marks')->nullable();
            $table->integer('passing_marks')->nullable();

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('supervisor_id')
                ->references('id')->on('supervisors')
                ->onDelete('cascade');

            $table->foreign('test_id')
                ->references('id')->on('test_settings')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supervisor_test_fees');
    }
};
