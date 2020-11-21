<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id')->nullable();
            $table->string('name', 255);
            $table->string('slug', 255)->nullable();
            $table->string('email', 255);
            $table->string('document', 50)->nullable();
            $table->string('phone', 100)->nullable();
            $table->enum('status', ['published','draft'])->nullable();
            $table->string('cover', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id','email']);
            $table->unique(['tenant_id','document']);
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
}
