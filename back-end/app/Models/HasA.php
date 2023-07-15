<?php

namespace App\Models;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HasA extends Model
{
    use HasFactory;
    protected $table = 'has_a';
    protected $primaryKey = 'has_a_id';
    protected $fillable=[
        'dept_code',
        'announcement_id'
    ];

    public function announcements()
    {
        return $this->belongsToMany(Announcement::class);
    }
}
