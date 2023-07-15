<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secondment extends Model
{
    use HasFactory;
    protected $fillable=[
        'secondment_id',
        'desc',
        'start_date',
        'end_date',
        'type',
        'user_id',
        'attachment',
        'country',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
