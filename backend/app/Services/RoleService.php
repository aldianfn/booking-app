<?php

namespace App\Services;

use App\Models\Role;
use Exception;
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
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $validatedData = $this->validateRoleData($data);


            $role = Role::create([
                'role_name' => $validatedData['role_name']
            ]);

            DB::commit();

            return [
                'success'   => true,
                'role'      => $role
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();

        try {
            $validatedData = $this->validateRoleData($data);

            $role = Role::where('id', $id)->lockForUpdate()->first();

            if (!$role) {
                throw new Exception('Role not found');
            }

            $role->role_name = $validatedData['role_name'];
            $role->save();

            DB::commit();

            return [
                'success'   => true,
                'role'      => $role
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $role = Role::where('id', $id)->lockForUpdate()->first();

            if (!$role) {
                throw new Exception('Role not found');
            }

            if ($this->isRoleInUse($role)) {
                throw new Exception('Cannot delete role becasue it assign to users');
            }

            $role->delete();

            DB::commit();

            return [
                'success'   => true,
                'message'   => 'Role deleted successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw new $e;
        }
    }

    public function validateRoleData(array $data)
    {
        return Validator::make($data, [
            'role_name' => 'required|max:255|unique:roles'
        ])->validate();
    }

    private function isRoleInUse(Role $role)
    {
        return DB::table('users')->where('role_id', $role->id)->exists();
    }
}
