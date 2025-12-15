<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of roles.
     */
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();

        return $this->successResponse([
            'roles' => $roles->map(function ($role) {
                return $this->formatRole($role);
            }),
        ], 'Roles retrieved successfully');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:roles',
            'description' => 'sometimes|string|max:500',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
        ]);

        // Attach permissions if provided
        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        $role->load('permissions');

        return $this->createdResponse([
            'role' => $this->formatRole($role),
        ], 'Role created successfully');
    }

    /**
     * Display the specified role.
     */
    public function show(int $id): JsonResponse
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return $this->notFoundResponse('Role not found');
        }

        return $this->successResponse([
            'role' => $this->formatRole($role),
        ], 'Role retrieved successfully');
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->notFoundResponse('Role not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id),
            ],
            'description' => 'sometimes|string|max:500',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        if ($request->has('name')) {
            $role->name = $request->name;
        }

        if ($request->has('slug')) {
            $role->slug = $request->slug;
        }

        if ($request->has('description')) {
            $role->description = $request->description;
        }

        $role->save();

        // Sync permissions if provided
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        $role->load('permissions');

        return $this->successResponse([
            'role' => $this->formatRole($role),
        ], 'Role updated successfully');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->notFoundResponse('Role not found');
        }

        // Prevent deleting protected roles
        if (in_array($role->slug, ['super-admin', 'admin'])) {
            return $this->errorResponse('Cannot delete protected roles', 400);
        }

        $role->delete();

        return $this->successResponse([], 'Role deleted successfully');
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->notFoundResponse('Role not found');
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $role->permissions()->sync($request->permissions);
        $role->load('permissions');

        return $this->successResponse([
            'role' => $this->formatRole($role),
        ], 'Permissions assigned successfully');
    }

    /**
     * Format role data for response.
     */
    private function formatRole(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                ];
            }),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }
}
