<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AssignedCourse extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'semester',
        'course_code',
        'academic_year'
    ];
}

