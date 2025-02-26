<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Exception;
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
            $validatedData = $this->validateData($data);

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

            // Log successfull register activity into table
            $this->logActivity('User Registration', 'User registered successfully', 'success', $user);

            DB::commit();

            return [
                'user'  => $user,
                'token' => $token
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('UserService: Failed creating new user: ' . $e->getMessage());

            // Log failed register activity into table
            if (isset($user)) {
                $this->logActivity('User Registration', 'User registration failed: ' . $e->getMessage(), 'failed', $user);
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

    public function login($data)
    {
        //
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => 'required|string|min:8|confirmed',
            'phone'             => 'required|string',
            'profile_picture'   => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ])->validate();
    }

    private function createToken($user)
    {
        return $user->createToken('authToken')->plainTextToken;
    }

    private function logActivity($action, $details, $status, $user)
    {
        Activity::create([
            'action'        => $action,
            'details'       => $details,
            'ip_address'    => request()->ip(),
            'status'        => $status,
            'user_id'       => $user->id
        ]);
    }
}
