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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('dept_code')->unique();
            // $table->index(['dept_code'], 'id');
            $table->string('dept_name')->unique();
            $table->unsignedBigInteger('head_id')->nullable();
            $table->text('desc');
            $table->string('head');
            $table->string('booklet');
            $table->string('bylaw');
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
        Schema::dropIfExists('departments');
    }
};
