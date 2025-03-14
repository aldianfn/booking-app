<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Services\HotelService;
use Exception;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    protected $hotelService;

    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $hotels = $this->hotelService->getAllHotels();

            // if (!$hotels) {
            //     return response()->json([
            //         'success'   => true,
            //         'hotels'    => 'Hotel not found'
            //     ], 200);
            // }

            return response()->json([
                'success'   => true,
                'hotels'    => $hotels
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'   => false,
                'error'     => $e->getMessage(),
                'message'   => 'Failed getting hotel'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        //
    }
}
