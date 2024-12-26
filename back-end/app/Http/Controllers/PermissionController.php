<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions); // ارسال لیست به فرانت‌اند
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        return response()->json([
            'message' => 'Permission created successfully!',
            'permission' => $permission
        ]);
    }
    public function getUsers(Permission $permission , $search = '')
    {
        $users = User::query()
            ->when($search, function ($query, $searchQuery) {
                $query->where('username', 'like', "%{$searchQuery}%");
            })
            ->get()->map(function ($user) use ($permission) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'hasPermission' => $user->hasPermissionTo($permission->name),
                ];
            });

        return response()->json($users);
    }

    public function updatePermission(Permission $permission, User $user, Request $request)
    {
        if ($request->has('hasPermission') && $request->hasPermission) {
            $user->givePermissionTo($permission->name);
        } else {
            $user->revokePermissionTo($permission->name);
        }

        return response()->json(['message' => 'Permission updated successfully']);
    }

}
