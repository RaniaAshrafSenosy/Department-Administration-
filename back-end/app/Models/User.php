<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Department;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable,HasApiTokens;

    protected $primaryKey = 'user_id';


    protected $fillable=[
        'user_id',
            'full_name',
            'user_name',
            'phone_number',
            'relative_number',
            'relative_name',
            'main_email',
            'additional_email',
            'password',
            'profile_links',
            'role',
            'title',
            'office_location',
            'day_time',
            'time_range',
            'is_active',
            'reason',
            'is_archived',
            'dept_code',
            'image',
            'start_date',
            'end_date',
            'privileged_user'
    ];

    public function user()
    {
        return $this->belongsTo(Department::class);
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
   protected $casts = [
    'profile_links' => 'array',
    'day_time' => 'array',
    'time_range' => 'array'
];
public function getTimeRangeAttribute($value)
{
    $timeRanges = json_decode($value, true);

    foreach ($timeRanges as &$timeRange) {
        if (isset($timeRange['day_time'])) {
            $timeRange['day_time'] = ucfirst(strtolower($timeRange['day_time']));
        }
    }

    return $timeRanges;
}

public function setTimeRangeAttribute($value)
{
    if (is_array($value)) {
        foreach ($value as &$timeRange) {
            if (isset($timeRange['day_time'])) {
                $timeRange['day_time'] = ucfirst(strtolower($timeRange['day_time']));
            }
        }
        $this->attributes['time_range'] = json_encode($value);
    } else {
        $this->attributes['time_range'] = $value;
    }
}
}
