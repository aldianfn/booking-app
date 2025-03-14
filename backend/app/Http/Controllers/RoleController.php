<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = $this->roleService->getAllRole();

            return response()->json($roles, 200);
        } catch (Exception $e) {
            return response()->json([
                'error'     => $e->getMessage(),
                'message'   => 'Unauthorized'
            ], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $role = $this->roleService->create($request->all());

            return response()->json(['role' => $role], 201);
        } catch (Exception $e) {
            return response()->json([
                'error'     => $e->getMessage(),
                'message'   => 'Failed creating role'
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $role = $this->roleService->update($request->all(), $id);

            return response()->json(['role' => $role], 200);
        } catch (Exception $e) {
            return response()->json([
                'error'     => $e->getMessage(),
                'message'   => 'Failed updating role'
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $role = $this->roleService->delete($id);

            return response()->json(['role' => $role], 200);
        } catch (Exception $e) {
            return response()->json([
                'error'     => $e->getMessage(),
                'message'   => 'Failed deleting role'
            ], 401);
        }
    }
}
