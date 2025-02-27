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
        //
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
}
