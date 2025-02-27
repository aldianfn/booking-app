<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        try {
            $user = $this->authService->register($request->all());

            return response()->json($user, 201);
        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            return response()->json([
                'error'     => 'Registration failed',
                'message'   => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $user = $this->authService->login($request->all());

            return response()->json($user, 200);
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());

            return response()->json([
                'error'     => 'Login failed',
                'message'   => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $this->authService->logout();

            return response()->json($user, 200);
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());

            return response()->json([
                'error'     => 'Logout failed',
                'message'   => $e->getMessage(),
            ], 500);
        }
    }
}
