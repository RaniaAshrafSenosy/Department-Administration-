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
    // 'dept_code',
    //     'announcement_id'
    public function up()
    {
        Schema::create('has_a', function (Blueprint $table) {
            $table->id('has_a_id');
            $table->string('dept_code')->nullable();
            $table->foreign('dept_code')->references('dept_code')->on('departments')->onDelete('cascade');
            $table->unsignedBigInteger('announcement_id')->nullable(false);
            $table->foreign('announcement_id')->references('announcement_id')->on('announcements')->onDelete('cascade');
            //$table->index(['dept_code', 'announcement_id'], 'has_a_id');
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
        Schema::dropIfExists('has_a');
    }
};
