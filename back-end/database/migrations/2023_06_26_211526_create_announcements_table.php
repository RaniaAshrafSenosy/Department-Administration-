<?php

use App\Models\Announcement;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('body');
            $table->string('file')->nullable();
            $table->json('target_role')->nullable();
            $table->json('target_dept')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->enum('status', ['accepted', 'rejected', 'pending'])->default('pending');
            $table->dateTime('datetime')->default(Announcement::raw('NOW()'));
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
        Schema::dropIfExists('announcements');

}
};
