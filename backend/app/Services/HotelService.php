<?php

namespace App\Services;

use App\Models\Hotel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HotelService
{
    public function getAllHotels(array $filters = [], ?string $search = null, int $perPage = 10)
    {
        try {
            $query = Hotel::query();

            // Apply search using scope
            if ($search) {
                $query->searchHotel($search);
            }

            // Apply all filter
            $query->applyFilters($filters);

            // Apply sorting
            $sortField = $filters['sort_by'] ?? 'created_at';
            $sortDirection = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($sortField, $sortDirection);

            return $query->paginate($perPage);
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

    public function validatePerPage(int $perPage)
    {
        return Validator::make($perPage, [
            'per_page'  => 'nullable|integer|max:100'
        ]);
    }
}
