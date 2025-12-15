<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of permissions.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::all();

        return $this->successResponse([
            'permissions' => $permissions->map(function ($permission) {
                return $this->formatPermission($permission);
            }),
        ], 'Permissions retrieved successfully');
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:permissions',
            'description' => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
        ]);

        return $this->createdResponse([
            'permission' => $this->formatPermission($permission),
        ], 'Permission created successfully');
    }

    /**
     * Display the specified permission.
     */
    public function show(int $id): JsonResponse
    {
        $permission = Permission::with('roles')->find($id);

        if (!$permission) {
            return $this->notFoundResponse('Permission not found');
        }

        return $this->successResponse([
            'permission' => $this->formatPermission($permission),
        ], 'Permission retrieved successfully');
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->notFoundResponse('Permission not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($permission->id),
            ],
            'description' => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        if ($request->has('name')) {
            $permission->name = $request->name;
        }

        if ($request->has('slug')) {
            $permission->slug = $request->slug;
        }

        if ($request->has('description')) {
            $permission->description = $request->description;
        }

        $permission->save();

        return $this->successResponse([
            'permission' => $this->formatPermission($permission),
        ], 'Permission updated successfully');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(int $id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->notFoundResponse('Permission not found');
        }

        $permission->delete();

        return $this->successResponse([], 'Permission deleted successfully');
    }

    /**
     * Format permission data for response.
     */
    private function formatPermission(Permission $permission): array
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'slug' => $permission->slug,
            'description' => $permission->description,
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
        ];
    }
}
