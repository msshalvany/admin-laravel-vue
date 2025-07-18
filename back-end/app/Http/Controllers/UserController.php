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
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $search = $request->input('search', '');

        $query = User::with('location');

        if ($search) {
            $query->where('username', 'like', '%' . $search . '%');
        }

        $user = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
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
            'message' => 'کاربر با موفقیت پیدا شد.',
            'data' => $user
        ], 200);
    }

    public function store(Request $request)
    {
        $messages = [
            'username.required' => 'نام کاربری الزامی است.',
            'username.string' => 'نام کاربری باید به صورت متن باشد.',
            'username.max' => 'نام کاربری نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'password.required' => 'کلمه عبور الزامی است.',
            'password.string' => 'کلمه عبور باید به صورت متن باشد.',
            'password.max' => 'کلمه عبور نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.numeric' => 'شماره موبایل باید به صورت عدد باشد.',
            'mobile.digits' => 'شماره موبایل باید دقیقا ۱۰ رقم باشد.',
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'mobile' => 'required|numeric|digits:10',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = User::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'کاربر با موفقیت ایجاد شد.',
            'data' => $customer
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'username.required' => 'نام کاربری الزامی است.',
            'username.string' => 'نام کاربری باید به صورت متن باشد.',
            'username.max' => 'نام کاربری نباید بیشتر از ۲۵۵ کاراکتر باشد.',
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        $UserOldData = User::findOrFail($id);
        if ($request->input('password') != $UserOldData->password || $request->password == '') {
            $request->merge(['password' => $UserOldData->password]);
        }
        $UserOldData->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'کاربر با موفقیت به‌روزرسانی شد.',
            'data' => $UserOldData
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'کاربر با موفقیت حذف شد.'
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

        return response()->json(['message' => 'مجوز با موفقیت به‌روزرسانی شد.']);
    }
}
