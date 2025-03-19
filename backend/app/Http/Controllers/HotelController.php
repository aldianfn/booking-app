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
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);

            $search = $request->query('search');

            $filters = [
                'sort_by'           => $request->query('sort_by'),
                'sort_direction'    => in_array($request->query('sort_direction'), ['asc', 'desc']) ? $request->query('sort_direction') : 'desc'
            ];

            $filters = array_filter($filters, function ($value) {
                return $value !== null;
            });

            $hotels = $this->hotelService->getAllHotels($filters, $search, $perPage);

            return response()->json([
                'success'   => true,
                'hotels'    => $hotels->items(),
                'meta'      => [
                    'current_page'      => $hotels->currentPage(),
                    'last_page'         => $hotels->lastPage(),
                    'per_page'          => $hotels->perPage(),
                    'total'             => $hotels->total(),
                    'search_query'      => $search,
                    'applied_filters'   => $filters
                ]
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
        try {
            $hotel = $this->hotelService->create($request->all());

            return response()->json(['hotel' => $hotel], 201);
        } catch (Exception $e) {
            return response()->json([
                'error'     => $e->getMessage(),
                'message'   => 'Failed creating hotel'
            ], 401);
        }
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
