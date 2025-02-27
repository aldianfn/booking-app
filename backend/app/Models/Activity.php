<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'path',
        'method',
        'ip_address',
        'user_agent',
        'request_data',
        'status',
        'user_id'
    ];
}
