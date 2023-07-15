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
        Schema::create('external_postgraduate_studies', function (Blueprint $table) {
            $table->id();
            $table->string('department')->nullable(false);
            $table->string('academic_year');
            $table->string('student_name');
            $table->string('gender');
            $table->string('nationality');
            $table->date('registration_date');
            $table->double('credit_hours');
            $table->date('preliminary_date');
            $table->string('telephone_number');
            $table->string('phone_number');
            $table->string('employer')->nullable();
            $table->text('employer_address')->nullable();
            $table->string('bachelor_certificate');
            $table->string('grade');
            $table->string('faculty_name');
            $table->date('graduation_date');
            $table->string('university_name');
            $table->text('research_topic_AR');
            $table->text('research_topic_EN');
            $table->text('research_interest');
            $table->text('target');
            $table->text('specialization');
            $table->text('field_of_research');
            $table->json('internal_supervisor_names');
            $table->json('external_supervisor_names')->nullable();
            $table->json('external_supervisor_titles')->nullable();
            $table->string('attachment');
            $table->string('email');
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
        Schema::dropIfExists('external_postgraduate_studies');
    }
};
