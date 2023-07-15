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
            $table->id('user_id');
            $table->string('full_name');
            $table->string('user_name')->nullable();
            $table->string('phone_number');
            $table->string('relative_number');
            $table->string('relative_name');
            $table->string('main_email')->unique();
            $table->string('additional_email')->nullable()->unique();
            $table->string('password');
            $table->text('profile_links')->nullable();
            $table->string('role');
            $table->string('title')->nullable();
            $table->text('office_location');
            $table->string('day_time')->nullable();
            $table->json('time_range')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('reason');
            $table->boolean('is_archived')->default(false);
            $table->boolean('privileged_user')->default(false);
            $table->string('dept_code')->nullable(false);
            $table->foreign('dept_code')->references('dept_code')->on('departments')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('image')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Schema::table('users', function($table) {
        //     $table->foreign('dept_code')->references('dept_code')->on('departments');
        // });
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
