<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',80);
            $table->string('email',100)->nullable();
            $table->string('phone',25)->nullable();
            $table->tinyInteger('user_role',false,2)->default(\App\Models\User::GENERALUSER)->comment('1=Developer,2=Super Admin,3=Admin,4=Librarian,5=General User');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(\App\Models\User::PENDING)->comment('0=REJECTED,1=APPROVED,2=PENDING');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
