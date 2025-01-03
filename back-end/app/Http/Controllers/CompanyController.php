<?php

// app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        // دریافت پارامترهای صفحه‌بندی و جستجو
        $page = $request->input('page', 1); // صفحه فعلی (پیش‌فرض 1)
        $pageSize = $request->input('pageSize', 10); // تعداد رکوردها در هر صفحه (پیش‌فرض 10)
        $search = $request->input('search', ''); // جستجو بر اساس نام

        // ساخت کوئری برای گرفتن لیست کمپانی‌ها با فیلتر جستجو و صفحه‌بندی
        $query = Company::query();

        // اگر جستجویی وجود داشته باشد، فیلتر را اعمال می‌کنیم
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // انجام صفحه‌بندی با تعداد رکوردهای مشخص
        $companies = $query->paginate($pageSize, ['*'], 'page', $page);

        // بازگرداندن پاسخ
        return response()->json([
            'status' => true,
            'message' => 'Companies retrieved successfully',
            'data' => $companies->items(),
            'total' => $companies->total(),
            'current_page' => $companies->currentPage(),
            'last_page' => $companies->lastPage(),
        ], 200);
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Company found successfully',
            'data' => $company
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $company = Company::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $company = Company::findOrFail($id);
        $company->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Company updated successfully',
            'data' => $company
        ], 200);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Company deleted successfully'
        ], 204);
    }

    public function getTrucks($id)
    {
        $company = Company::find($id);
        return response()->json([
            'status' => true,
            'data' => $company->trucks,
        ]);
    }
}
