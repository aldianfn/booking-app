<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'action',
        'details',
        'ip_address',
        'status',
        'user_id'
    ];
}
