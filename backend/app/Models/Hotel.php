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
                $searchableFields = ['hotel_name', 'address'];

                foreach ($searchableFields as $field) {
                    $query->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByProvince($query, $province)
    {
        return $query->where('province', $province);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        if (isset($filters['city'])) {
            $query->byCity($filters['city']);
        }

        if (isset($filters['province'])) {
            $query->byProvince($filters['province']);
        }

        return $query;
    }
}
