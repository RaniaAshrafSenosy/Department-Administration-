<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostgraduateStudy extends Model
{
    use HasFactory;
    protected $fillable=[
        'dept_code',
        'academic_year',
        'student_name',
        'gender',
        'nationality',
        'registration_date',
        'credit_hours',
        'preliminary_date',
        'telephone_number',
        'phone_number',
        'employer',
        'employer_address',
        'bachelor_certificate',
        'grade',
        'faculty_name',
        'graduation_date',
        'university_name',
        'research_topic_AR',
        'research_topic_EN',
        'research_interest',
        'target',
        'specialization',
        'field_of_research',
        'internal_supervisor_names',
        'external_supervisor_names',
        'external_supervisor_titles',
        'attachment',
        'user_id'
    ];

    protected $casts = [
        'internal_supervisor_names' => 'array',
        'external_supervisor_names' => 'array',
        'external_supervisor_titles' => 'array',
    ];
}
