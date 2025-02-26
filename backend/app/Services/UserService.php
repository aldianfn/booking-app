<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserService
{
    public function all()
    {
        return User::all();
    }

    public function find(int $id)
    {
        return User::find($id);
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            Log::info('UserService: Creating new user', ['data' => $data]);

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

            // Log successfull register activity
            $this->logActivity($user, 'User Registration', 'User registered successfully', 'success');

            DB::commit();

            return [
                'user'  => $user,
                'token' => $token
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('UserService: Failed creating new user: ' . $e->getMessage());

            // Log failed register activity
            if (isset($user)) {
                $this->logActivity($user, 'User Registration', 'User registration failed: ' . $e->getMessage(), 'failed');
            }

            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        $user = User::findOrFail($id);

        return $user->update($data);
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);

        return $user->delete($id);
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

                // Log successfull login activity
                $this->logActivity($user, 'User Login', 'User logged in successfully', 'success');

                DB::commit();

                return [
                    'user'  => $user,
                    'token' => $token
                ];
            } else {
                // Log failed login activity
                $this->logActivity(null, 'User Login', 'User login failed: Invalid credentials', 'success');

                throw new Exception('Invalid credentials');
            }
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('UserService: Login failed: ' . $e->getMessage());

            // Log failed login activity
            if (isset($user)) {
                $this->logActivity($user, 'User Login', 'User login failed: ' . $e->getMessage(), 'failed');
            }

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

                // Log logout activity
                $this->logActivity($user, 'User Logout', 'User logged out successfully', 'success');

                DB::commit();

                return [
                    'message' => 'Logout successfull'
                ];
            } else {
                throw new Exception('User not authenticated');
            }
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('UserService: Logout failed: ' . $e->getMessage());

            // Log failed logout activity
            if (isset($user)) {
                $this->logActivity($user, 'User Logout', 'User logout failed: ' . $e->getMessage(), 'failed');
            }

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

    private function logActivity($user, $action, $details, $status)
    {
        Activity::create([
            'action'        => $action,
            'details'       => $details,
            'ip_address'    => request()->ip(),
            'status'        => $status,
            'user_id'       => $user ? $user->id : null
        ]);
    }
}
