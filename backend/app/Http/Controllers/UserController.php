<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user) {
                return response()->json($user, 200);
            } else {

                throw new \Exception('User not authenticated');
            }
        } catch (\Exception $e) {
            Log::error('User profile access failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $e->getMessage(),
            ], 401);
        }
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
    public function show(User $user)
    {
        try {
            $user = Auth::user();

            if ($user) {
                Activity::create([
                    'action'        => 'Accessed profile',
                    'details'       => 'User successfully accessing /user to check user profile',
                    'ip_address'    => request()->ip(),
                    'status'        => 'success',
                    'user_id'       => $user ? $user->id : null
                ]);

                return response()->json($user, 200);
            } else {
                Activity::create([
                    'action'        => 'Accessed profile',
                    'details'       => 'User failed to accessing /user',
                    'ip_address'    => request()->ip(),
                    'status'        => 'failed',
                    'user_id'       => $user ? $user->id : null
                ]);

                throw new \Exception('User not authenticated');
            }
        } catch (\Exception $e) {
            Log::error('User profile access failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
