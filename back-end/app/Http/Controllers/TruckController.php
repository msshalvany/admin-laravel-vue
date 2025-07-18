<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

            'plate_type.required' => 'نوع پلاک الزامی است.',
            'plate_type.integer' => 'نوع پلاک باید عدد باشد.',
            'plate_type.in' => 'نوع پلاک نامعتبر است.',

            'color.required' => 'رنگ کامیون الزامی است.',
            'color.string' => 'رنگ کامیون باید به صورت متن باشد.',
            'color.max' => 'رنگ کامیون نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'type.integer' => 'نوع کامیون باید عدد باشد.',
            'type.in' => 'نوع کامیون نامعتبر است.',

            'weight.required' => 'وزن کامیون الزامی است.',
            'weight.numeric' => 'وزن کامیون باید به صورت عدد باشد.',
            'weight.min' => 'وزن کامیون نمی‌تواند منفی باشد.',
        ];

        $allowedPlateTypes = [1, 2, 3, 4, 5]; // مقدار مجاز نوع پلاک
        $allowedTruckTypes = [0, 1, 2, 3, 4, 5]; // مقدار مجاز نوع کامیون

        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',

            'plate_right' => 'required|digits:2',
            'plate_char' => 'required|size:1',
            'plate_left' => 'required|digits_between:1,3',
            'plate_city' => 'required|digits:2',

            'plate_type' => ['required', 'integer', Rule::in($allowedPlateTypes)],
            'color' => 'required|string|max:255',
            'type' => ['nullable', 'integer', Rule::in($allowedTruckTypes)],
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
        $plateFull = $request->plate_right . $request->plate_char . $request->plate_left . '-' . $request->plate_city;

        // ذخیره در دیتابیس
        $truck = Truck::create([
            'driver_id'    => $request->driver_id,
            'company_id'   => $request->company_id,
            'plate_right'  => $request->plate_right,
            'plate_char'   => $request->plate_char,
            'plate_left'   => $request->plate_left,
            'plate_city'   => $request->plate_city,
            'plate_full'   => $plateFull,
            'plate_type'   => $request->plate_type,  // معادل عددی نوع پلاک
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
        $messages = [
            'driver_id.required' => 'شناسه راننده الزامی است.',
            'driver_id.exists' => 'راننده با این شناسه یافت نشد.',
            'company_id.required' => 'شناسه شرکت الزامی است.',
            'company_id.exists' => 'شرکت با این شناسه یافت نشد.',

            'plate_type.integer' => 'نوع پلاک باید عدد باشد.',
            'plate_type.in' => 'نوع پلاک نامعتبر است.',

            'color.required' => 'رنگ کامیون الزامی است.',
            'color.string' => 'رنگ کامیون باید به صورت متن باشد.',
            'color.max' => 'رنگ کامیون نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'type.integer' => 'نوع کامیون باید عدد باشد.',
            'type.in' => 'نوع کامیون نامعتبر است.',

            'weight.required' => 'وزن کامیون الزامی است.',
            'weight.numeric' => 'وزن کامیون باید به صورت عدد باشد.',
            'weight.min' => 'وزن کامیون نمی‌تواند منفی باشد.',
        ];

        $allowedPlateTypes = [1, 2, 3, 4, 5];
        $allowedTruckTypes = [0, 1, 2, 3, 4, 5];

        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',
            'plate_type' => ['integer', Rule::in($allowedPlateTypes)],
            'color' => 'required|string|max:255',
            'type' => ['nullable', 'integer', Rule::in($allowedTruckTypes)],
            'weight' => 'required|numeric|min:0',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        // آپدیت فیلدهای قابل بروزرسانی
        $truck->update([
            'driver_id'  => $request->driver_id,
            'company_id' => $request->company_id,
            'plate_type' => $request->plate_type ?? $truck->plate_type,
            'color'      => $request->color,
            'type'       => $request->type,
            'weight'     => $request->weight,
        ]);

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
