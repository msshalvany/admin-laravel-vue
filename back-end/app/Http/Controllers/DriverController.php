<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('q', ''));
        $sortColumn = $request->query('sort', 'name');
        $sortOrder = $request->query('order', 'asc');
        $countPage = $request->query('countPage', 10);

        $drivers = Driver::where('name', 'like', "%{$search}%")
            ->orWhere('address', 'like', "%{$search}%")
            ->orWhere('id', 'like', "%{$search}%")
            ->orWhere('license_number', 'like', "%{$search}%")
            ->orderBy($sortColumn, $sortOrder)
            ->paginate($countPage);
        return response()->json($drivers, 200);
    }

    public function all(Request $request)
    {
        $search = $request->query('q', '');
        $drivers = Driver::where('name', 'like', "%{$search}%")->get();
        return response()->json([
            'data' => $drivers,
            'status'=>200
        ]);
    }

    public function store(Request $request)
    {
        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'license_number' => 'required|string|max:255',
        ], [
            'name.required' => 'نام راننده الزامی است.',
            'name.string' => 'نام راننده باید به صورت متن باشد.',
            'name.max' => 'نام راننده نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'address.required' => 'آدرس راننده الزامی است.',
            'address.string' => 'آدرس راننده باید به صورت متن باشد.',
            'address.max' => 'آدرس راننده نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'license_number.required' => 'شماره گواهینامه راننده الزامی است.',
            'license_number.string' => 'شماره گواهینامه باید به صورت متن باشد.',
            'license_number.max' => 'شماره گواهینامه راننده نباید بیشتر از ۲۵۵ کاراکتر باشد.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        // ذخیره راننده جدید
        $driver = Driver::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'راننده با موفقیت ایجاد شد',
            'data' => $driver
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
        ], [
            'name.required' => 'نام راننده الزامی است.',
            'name.string' => 'نام راننده باید به صورت متن باشد.',
            'name.max' => 'نام راننده نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'address.required' => 'آدرس راننده الزامی است.',
            'address.string' => 'آدرس راننده باید به صورت متن باشد.',
            'address.max' => 'آدرس راننده نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'license_number.required' => 'شماره گواهینامه راننده الزامی است.',
            'license_number.string' => 'شماره گواهینامه باید به صورت متن باشد.',
            'license_number.max' => 'شماره گواهینامه راننده نباید بیشتر از ۵۰ کاراکتر باشد.',
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
