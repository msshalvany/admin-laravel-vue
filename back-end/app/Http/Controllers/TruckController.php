<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TruckController extends Controller
{
    public function index(Request $request)
    {
        // دریافت پارامترها برای جستجو، مرتب‌سازی و صفحه‌بندی
        $search = $request->query('q', '');
        $sortColumn = $request->query('sort', 'plate_number');
        $sortOrder = $request->query('order', 'asc');

        // واکشی کامیون‌ها با فیلترها
        $trucks = Truck::with('driver','company')->where('plate_number', 'like', "%{$search}%")
            ->orWhere('color', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(10);

        return response()->json($trucks, 200);
    }

    public function store(Request $request)
    {
        // پیام‌های اعتبارسنجی به زبان فارسی
        $messages = [
            'driver_id.required' => 'شناسه راننده الزامی است.',
            'driver_id.exists' => 'راننده با این شناسه یافت نشد.',
            'company_id.required' => 'شناسه شرکت الزامی است.',
            'company_id.exists' => 'شرکت با این شناسه یافت نشد.',
            'plate_number.required' => 'شماره پلاک الزامی است.',
            'plate_number.string' => 'شماره پلاک باید به صورت متن باشد.',
            'plate_number.max' => 'شماره پلاک نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'color.required' => 'رنگ کامیون الزامی است.',
            'color.string' => 'رنگ کامیون باید به صورت متن باشد.',
            'color.max' => 'رنگ کامیون نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'type.string' => 'نوع کامیون باید به صورت متن باشد.',
            'type.in' => 'نوع کامیون باید یکی از مقادیر: "غیره", "کامیون", "تریلی", "کامیونت", "خاور", "وانت" باشد.',
            'weight.required' => 'وزن کامیون الزامی است.',
            'weight.numeric' => 'وزن کامیون باید به صورت عدد باشد.',
            'weight.min' => 'وزن کامیون نمی‌تواند منفی باشد.',
        ];

        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',
            'plate_number' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'type' => 'nullable|string|in:غیره,کامیون,تریلی,کامیونت,خاور,وانت',
            'weight' => 'required|numeric|min:0',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        // ذخیره کامیون جدید
        $truck = Truck::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'کامیون با موفقیت ایجاد شد.',
            'data' => $truck
        ], 201);
    }

    public function update(Request $request, Truck $truck)
    {
        // پیام‌های اعتبارسنجی به زبان فارسی
        $messages = [
            'driver_id.required' => 'شناسه راننده الزامی است.',
            'driver_id.exists' => 'راننده با این شناسه یافت نشد.',
            'company_id.required' => 'شناسه شرکت الزامی است.',
            'company_id.exists' => 'شرکت با این شناسه یافت نشد.',
            'plate_number.required' => 'شماره پلاک الزامی است.',
            'plate_number.string' => 'شماره پلاک باید به صورت متن باشد.',
            'plate_number.max' => 'شماره پلاک نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'color.required' => 'رنگ کامیون الزامی است.',
            'color.string' => 'رنگ کامیون باید به صورت متن باشد.',
            'color.max' => 'رنگ کامیون نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'type.string' => 'نوع کامیون باید به صورت متن باشد.',
            'type.in' => 'نوع کامیون باید یکی از مقادیر: "غیره", "کامیون", "تریلی", "کامیونت", "خاور", "وانت" باشد.',
            'weight.required' => 'وزن کامیون الزامی است.',
            'weight.numeric' => 'وزن کامیون باید به صورت عدد باشد.',
            'weight.min' => 'وزن کامیون نمی‌تواند منفی باشد.',
        ];

        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',
            'plate_number' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'type' => 'nullable|string|in:غیره,کامیون,تریلی,کامیونت,خاور,وانت',
            'weight' => 'required|numeric|min:0',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        // بروزرسانی کامیون
        $truck->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'کامیون با موفقیت ویرایش شد.',
            'data' => $truck
        ], 200);
    }

    public function destroy($id)
    {
        $truck = Truck::find($id);

        if (!$truck) {
            return response()->json([
                'message' => 'کامیون یافت نشد.',
            ], 404);
        }

        $truck->delete();

        return response()->json([
            'message' => 'کامیون با موفقیت حذف شد.',
        ], 200);
    }
}
