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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name')->unique();
            $table->text('program_desc');
            $table->string('program_head');
            $table->string('booklet');
            $table->string('bylaw');
            $table->string('dept_code')->nullable(false);
            $table->foreign('dept_code')->references('dept_code')->on('departments')->onDelete('cascade');
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
        Schema::dropIfExists('programs');
    }
};
