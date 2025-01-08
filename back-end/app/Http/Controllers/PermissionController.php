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
        // پیام‌های اعتبارسنجی به زبان فارسی
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ], [
            'name.required' => 'نام دسترسی الزامی است.',
            'name.string' => 'نام دسترسی باید به صورت متن باشد.',
            'name.unique' => 'این نام دسترسی قبلاً وجود دارد.',
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        return response()->json([
            'message' => 'دسترسی با موفقیت ایجاد شد!',
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

        return response()->json([
            'message' => 'دسترسی با موفقیت بروزرسانی شد.'
        ]);
    }
}
