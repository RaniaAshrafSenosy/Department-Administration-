<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable=[
        'course_id',
        'course_code',
        'course_specs',
        'prerequisites',
        'credit_hours',
        'course_name',
        'course_desc',
        'dept_code',
        'program_name',
        'is_archived'
    ];
    protected $casts = [
        'prerequisites' => 'array'
    ];
    public function courseDepartment()
    {
        return $this->balongsTo(Department::class);
    }
    public function courseProgram()
    {
        return $this->balongsTo(Program::class);
    }

}

