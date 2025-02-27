<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthService
{
    public function register(array $data)
    {
        try {
            DB::beginTransaction();

            Log::info('AuthService: Creating new user', ['data' => $data]);

            // Set profile picture default value
            $profile_picture = 'user-default.png';

            // Replace default profile picture to user uploaded picture
            if (isset($data['profile_picture'])) {
                $profile_picture = $data['profile_picture']->store('photos', 'public');
            }

            // Validate user input
            $validatedData = $this->validateRegisterData($data);

            // Create new user
            $user = User::create([
                'name'              => $validatedData['name'],
                'email'             => $validatedData['email'],
                'password'          => Hash::make($validatedData['password']),
                'phone'             => $validatedData['phone'],
                'profile_picture'   => $profile_picture,
                'role'              => 'customer'
            ]);

            // Generate token
            $token = $this->createToken($user);

            DB::commit();

            return [
                'user'  => $user,
                'token' => $token
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('UserService: Failed creating new user: ' . $e->getMessage());

            throw $e;
        }
    }

    public function login(array $data)
    {
        try {
            DB::beginTransaction();

            // Validate user input
            $validatedData = $this->validateLoginData($data);

            if (Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
                // Get user data
                $user = Auth::user();

                // Generate token
                $token = $this->createToken($user);

                DB::commit();

                return [
                    'user'  => $user,
                    'token' => $token
                ];
            } else {
                throw new \Exception('Invalid credentials');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('UserService: Login failed: ' . $e->getMessage());

            throw $e;
        }
    }

    public function logout()
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            if ($user) {
                // Revoke token
                $user->tokens()->delete();

                DB::commit();

                return [
                    'message' => 'Logout successfull'
                ];
            } else {
                throw new \Exception('User not authenticated');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('UserService: Logout failed: ' . $e->getMessage());

            throw $e;
        }
    }

    private function validateRegisterData(array $data)
    {
        return Validator::make($data, [
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => 'required|string|min:8|confirmed',
            'phone'             => 'required|string',
            'profile_picture'   => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ])->validate();
    }

    private function validateLoginData(array $data)
    {
        return Validator::make($data, [
            'email'             => 'required|string|email|max:255',
            'password'          => 'required|string|min:8'
        ])->validate();
    }

    private function createToken($user)
    {
        return $user->createToken('authToken')->plainTextToken;
    }
}
