<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_history';

    protected $fillable = [
        'user_id',
        'ip_address',
        'city',
        'country',
        'user_agent',
        'logged_in_at',
    ];

    //to get name from the user table to show in the history
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
