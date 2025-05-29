<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::all();

        return response()->json([
            'status' => true,
            'message' => 'لیست شرکت‌ها با موفقیت دریافت شد',
            'data' => $companies,
        ], 200);
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'شرکت با موفقیت پیدا شد',
            'data' => $company
        ], 200);
    }

    public function store(Request $request)
    {
        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
        ], [
            'name.required' => 'نام شرکت الزامی است.',
            'name.string' => 'نام شرکت باید به صورت متن باشد.',
            'name.max' => 'نام شرکت نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'address.string' => 'آدرس باید به صورت متن باشد.',
            'phone.string' => 'شماره تلفن باید به صورت متن باشد.',
            'phone.max' => 'شماره تلفن نباید بیشتر از ۱۵ کاراکتر باشد.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        $company = Company::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'شرکت با موفقیت ایجاد شد',
            'data' => $company
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // اعتبارسنجی داده‌ها
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
        ], [
            'name.required' => 'نام شرکت الزامی است.',
            'name.string' => 'نام شرکت باید به صورت متن باشد.',
            'name.max' => 'نام شرکت نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'address.string' => 'آدرس باید به صورت متن باشد.',
            'phone.string' => 'شماره تلفن باید به صورت متن باشد.',
            'phone.max' => 'شماره تلفن نباید بیشتر از ۱۵ کاراکتر باشد.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        $company = Company::findOrFail($id);
        $company->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'شرکت با موفقیت ویرایش شد',
            'data' => $company
        ], 200);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'شرکت با موفقیت حذف شد'
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
