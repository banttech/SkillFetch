<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('otp_validations', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('otp');
            $table->string('type')->nullable(); // login, register, password_reset, etc.
            $table->boolean('is_verified')->default(0);
            $table->integer('attempts')->default(0);
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_validations');
    }
};
