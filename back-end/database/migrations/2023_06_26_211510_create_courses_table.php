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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('course_specs');
            $table->json('prerequisites')->nullable();
            $table->double('credit_hours');
            $table->string('course_name');
            $table->text('course_desc');
            $table->string('dept_code')->nullable(false);
            $table->foreign('dept_code')->references('dept_code')->on('departments')->onDelete('cascade');
            $table->string('program_name')->nullable()->default(null);
            $table->foreign('program_name')->references('program_name')->on('programs')->onDelete('cascade');
            $table->boolean('is_archived')->default(false);
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
        Schema::dropIfExists('courses');
    }
};
