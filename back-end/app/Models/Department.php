<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable=[
        'dept_code',
        'dept_name',
        'desc',
        'head',
        'booklet',
        'bylaw',
        'is_archived'
    ];
    public function departmentUser()
    {
        return $this->hasMany(User::class);

    }
    public function departmentCourse()
    {
        return $this->hasMany(Course::class);

    }
    public function announcements()
    {
        return $this->belongsToMany(Announcement::class);
    }
}
