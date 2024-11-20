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
        $customers = $query->paginate($pageSize, ['*'], 'page', $page);

        // بازگرداندن پاسخ
        return response()->json([
            'status' => true,
            'message' => 'Customers retrieved successfully',
            'data' => $customers->items(), // داده‌های صفحه فعلی
            'total' => $customers->total(), // تعداد کل کاربران
            'current_page' => $customers->currentPage(), // صفحه فعلی
            'last_page' => $customers->lastPage(), // آخرین صفحه
        ], 200);
    }


    public function show($id)
    {
        $customer = User::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Customer found successfully',
            'data' => $customer
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'mobile'   => 'required|digits_between:10,15|unique:users,mobile',
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
            'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = User::findOrFail($id);
        $customer->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer
        ], 200);
    }

    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();

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
