<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        // دریافت پارامترها
        $search = $request->query('q', '');
        $sortColumn = $request->query('sort', 'name');
        $sortOrder = $request->query('order', 'asc');

        // واکشی داده‌ها با جستجو، مرتب‌سازی و صفحه‌بندی
        $drivers = Driver::where('name', 'like', "%{$search}%")
            ->orWhere('address', 'like', "%{$search}%")
            ->orWhere('id', 'like', "%{$search}%")
            ->orWhere('license_number', 'like', "%{$search}%")
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(10); // تعداد آیتم‌ها در هر صفحه

        return response()->json($drivers, 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'license_number'   => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        for ($i=0;$i<300;$i++ ) {
        $customer = Driver::create($request->all());

        }
        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201);
    }

    public function destroy($id)
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json([
                'message' => 'راننده یافت نشد',
            ], 404);
        }

        $driver->delete();

        return response()->json([
            'message' => 'راننده با موفقیت حذف شد',
        ], 200);
    }

    public function update(Request $request, Driver $driver)
    {
        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'license_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // بروزرسانی اطلاعات راننده
        $driver->name = $request->name;
        $driver->address = $request->address;
        $driver->license_number = $request->license_number;
        $driver->save();

        // ارسال پاسخ موفقیت‌آمیز
        return response()->json([
            'message' => 'راننده با موفقیت ویرایش شد.',
            'data' => $driver
        ], 200);
    }
}
