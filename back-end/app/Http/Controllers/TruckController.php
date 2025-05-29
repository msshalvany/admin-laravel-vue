<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TruckController extends Controller
{
    public function index(Request $request)
    {
        // دریافت پارامتر ها برای جستجو، مرتب‌سازی و صفحه‌بندی
        $search = $request->query('q', '');
        $sortColumn = $request->query('sort', 'plate_number');
        $sortOrder = $request->query('order', 'asc');
        $countPage = $request->query('countPage', 10);

        // واکشی کامیون‌ها با فیلترها
        $trucks = Truck::with('driver','company')->where('plate_number', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orWhere('id', 'like', "%{$search}%")
            ->orderBy($sortColumn, $sortOrder)
            ->paginate($countPage);

        return response()->json($trucks, 200);
    }

    public function all(Request $request)
    {
        $search = $request->query('q', '');
        $trucks = Truck::with('driver','company')->where('plate_number', 'like', "%{$search}%")->get();
        return response()->json([
            'data' => $trucks,
            'status'=>200
        ]);
    }

    public function store(Request $request)
    {
        $messages = [
            'driver_id.required' => 'شناسه راننده الزامی است.',
            'driver_id.exists' => 'راننده با این شناسه یافت نشد.',
            'company_id.required' => 'شناسه شرکت الزامی است.',
            'company_id.exists' => 'شرکت با این شناسه یافت نشد.',

            'plate_right.required' => 'بخش راست پلاک الزامی است.',
            'plate_right.digits' => 'بخش راست پلاک باید دو رقمی باشد.',
            'plate_char.required' => 'حرف پلاک الزامی است.',
            'plate_char.size' => 'حرف پلاک باید یک کاراکتر باشد.',
            'plate_left.required' => 'بخش چپ پلاک الزامی است.',
            'plate_left.digits_between' => 'بخش چپ پلاک باید بین 1 تا 3 رقم باشد.',
            'plate_city.required' => 'کد استان الزامی است.',
            'plate_city.digits' => 'کد استان باید دو رقمی باشد.',

            'color.required' => 'رنگ کامیون الزامی است.',
            'color.string' => 'رنگ کامیون باید به صورت متن باشد.',
            'color.max' => 'رنگ کامیون نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'type.string' => 'نوع کامیون باید به صورت متن باشد.',
            'type.in' => 'نوع کامیون باید یکی از مقادیر: "غیره", "کامیون", "تریلی", "کامیونت", "خاور", "وانت" باشد.',

            'weight.required' => 'وزن کامیون الزامی است.',
            'weight.numeric' => 'وزن کامیون باید به صورت عدد باشد.',
            'weight.min' => 'وزن کامیون نمی‌تواند منفی باشد.',
        ];

        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',

            'plate_right' => 'required|digits:2',
            'plate_char' => 'required|size:1',
            'plate_left' => 'required|digits_between:1,3',
            'plate_city' => 'required|digits:2',

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

        // ساخت پلاک کامل
        // ذخیره در دیتابیس
        $truck = Truck::create([
            'driver_id'    => $request->driver_id,
            'company_id'   => $request->company_id,
            'plate_right'  => $request->plate_right,
            'plate_char'   => $request->plate_char,
            'plate_left'   => $request->plate_left,
            'plate_city'   => $request->plate_city,
            'plate_full'   => $request->plate_right . $request->plate_char . $request->plate_left . '-' . $request->plate_city,
            'color'        => $request->color,
            'type'         => $request->type,
            'weight'       => $request->weight,
        ]);


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
