<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $fillable=[
        'program_name',
        'program_desc',
        'program_head',
        'booklet',
        'bylaw',
        'dept_code',
        'is_archived'
    ];

    public function programCourse()
    {
        return $this->hasMany(Course::class);

    }
}
