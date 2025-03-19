<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'hotel_name',
        'address',
        'city',
        'province',
        'phone',
        'email',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearchHotel($query, $search)
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $searchableFields = ['hotel_name', 'address', 'city', 'province'];

                foreach ($searchableFields as $field) {
                    $query->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
