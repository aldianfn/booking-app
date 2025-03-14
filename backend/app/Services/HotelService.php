<?php

namespace App\Services;

use App\Models\Hotel;
use Exception;

class HotelService
{
    public function getAllHotels()
    {
        try {
            $hotels = Hotel::all();

            if (!$hotels) {
                throw new Exception('Hotel not found');
            }

            return $hotels;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
