<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $primaryKey = 'announcement_id';
    protected $fillable=[
        'title',
        'body',
        'target_role',
        'target_dept',
        'status',
        'is_archived',
        'datetime'
    ];
    protected $casts = [
        'target_role' => 'array',
        'target_dept' => 'array'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

}
