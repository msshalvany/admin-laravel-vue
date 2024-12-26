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
        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',
            'plate_number' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'type' => 'nullable|string|in:غیره,کامیون,تریلی,کامیونت,خاور,وانت',
            'weight' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // ذخیره کامیون جدید
        $truck = Truck::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'کامیون با موفقیت ایجاد شد',
            'data' => $truck
        ], 201);
    }

    public function update(Request $request, Truck $truck)
    {
        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',
            'plate_number' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'type' => 'nullable|string|in:غیره,کامیون,تریلی,کامیونت,خاور,وانت',
            'weight' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // بروزرسانی کامیون
        $truck->update($request->all());

        return response()->json([
            'message' => 'کامیون با موفقیت ویرایش شد',
            'data' => $truck
        ], 200);
    }

    public function destroy($id)
    {
        $truck = Truck::find($id);

        if (!$truck) {
            return response()->json([
                'message' => 'کامیون یافت نشد',
            ], 404);
        }

        $truck->delete();

        return response()->json([
            'message' => 'کامیون با موفقیت حذف شد',
        ], 200);
    }
}
