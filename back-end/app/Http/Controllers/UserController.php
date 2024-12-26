<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // دریافت پارامترهای صفحه‌بندی و جستجو
        $page = $request->input('page', 1); // صفحه فعلی (پیش‌فرض 1)
        $pageSize = $request->input('pageSize', 10); // تعداد رکوردها در هر صفحه (پیش‌فرض 10)
        $search = $request->input('search', ''); // جستجو بر اساس نام

        // ساخت کوئری برای گرفتن لیست کاربران با فیلتر جستجو و صفحه‌بندی
        $query = User::query();

        // اگر جستجویی وجود داشته باشد، فیلتر را اعمال می‌کنیم
        if ($search) {
            $query->where('username', 'like', '%' . $search . '%');
        }

        // انجام صفحه‌بندی با تعداد رکوردهای مشخص
        $user = $query->paginate($pageSize, ['*'], 'page', $page);

        // بازگرداندن پاسخ
        return response()->json([
            'status' => true,
            'message' => 'Customers retrieved successfully',
            'data' => $user->items(), // داده‌های صفحه فعلی
            'total' => $user->total(), // تعداد کل کاربران
            'current_page' => $user->currentPage(), // صفحه فعلی
            'last_page' => $user->lastPage(), // آخرین صفحه
        ], 200);
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Customer found successfully',
            'data' => $user
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'mobile' => 'required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = User::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $UserOldData = User::findOrFail($id);
        if ($request->input('password') != $UserOldData->password || $request->password=='') {
            $request->merge(['password' => $UserOldData->password]);
        }
        $UserOldData->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'user updated successfully',
            'data' => $UserOldData
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Customer deleted successfully'
        ], 204);
    }

    public function getUserPermissions(User $user)
    {
        return $user->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'hasPermission' => true,
            ];
        });
    }

    public function updateUserPermission(User $user, Permission $permission, Request $request)
    {
        if ($request->has('hasPermission') && $request->hasPermission) {
            $user->givePermissionTo($permission->name);
        } else {
            $user->revokePermissionTo($permission->name);
        }

        return response()->json(['message' => 'Permission updated successfully']);
    }

}
