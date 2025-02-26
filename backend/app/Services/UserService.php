<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

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

            // Log::info('UserService: Creating new user', ['data' => $data]);

            $profile_picture = 'user-default.png';

            if (isset($data['profile_picture'])) {
                $profile_picture = $data['profile_picture']->store('photos', 'public');
            }

            $validator = Validator::make($data, [
                'name'              => 'required|string|max:255',
                'email'             => 'required|string|email|max:255|unique:users',
                'password'          => 'required|string|min:8|confirmed',
                'phone'             => 'required|string',
                'profile_picture'   => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'phone'             => $data['phone'],
                'profile_picture'   => $profile_picture,
                'role'              => 'customer'
            ]);

            $this->logActivity('User Registration', 'User registered successfully', request()->ip(), 'success', $user->id);

            DB::commit();

            return $user;
        } catch (Throwable $e) {
            DB::rollBack();

            // Log::info('UserService: Failed creating new user', $e->getMessage());

            $this->logActivity('User Registration', $e->getMessage(), request()->ip(), 'failed');
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

    private function logActivity($action, $details, $ipAddress, $status, $userId = null)
    {
        Activity::create([
            'action'        => $action,
            'details'       => $details,
            'ip_address'    => $ipAddress,
            'status'        => $status,
            'user_id'       => $userId
        ]);
    }
}
