<?php

namespace App\Services;

use App\Models\Role;

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
}
