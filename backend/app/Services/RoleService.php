<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAllRole()
    {
        try {
            $role = Role::get();

            return $role;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $validatedData = $this->validateRoleData($data);


            $role = Role::create([
                'role_name' => $validatedData['role_name']
            ]);

            DB::commit();

            return $role;
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function validateRoleData(array $data)
    {
        return Validator::make($data, [
            'role_name' => 'required|max:255|unique:roles'
        ])->validate();
    }
}
