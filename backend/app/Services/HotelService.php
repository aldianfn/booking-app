<?php

namespace App\Services;

use App\Models\Hotel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $validatedData = $this->validateHotelData($data);

            $hotel = Hotel::create([
                'hotel_name'    => $validatedData['hotel_name'],
                'address'       => $validatedData['address'],
                'city'          => $validatedData['city'],
                'province'      => $validatedData['province'],
                'phone'         => $validatedData['phone'],
                'email'         => $validatedData['email'],
                'user_id'       => Auth::user()->id
            ]);

            DB::commit();

            return [
                'success'   => true,
                'hotel'     => $hotel
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function validateHotelData(array $data)
    {
        return Validator::make($data, [
            'hotel_name'    => 'required|string|max:255',
            'address'       => 'required|string|max:255|unique:hotels',
            'city'          => 'required|string|max:255',
            'province'      => 'required|string|max:255',
            'phone'         => 'required|string|max:255',
            'email'         => 'required|string|email|max:255'
        ])->validate();
    }
}
