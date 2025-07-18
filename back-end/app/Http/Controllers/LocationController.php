<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::all();

        return response()->json([
            'status' => true,
            'message' => 'با موفقیت دریافت شدند',
            'data' => $locations,
        ], 200);
    }

    public function show($id)
    {
        $locations = Location::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'مکان با موفقیت پیدا شد',
            'data' => $locations,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => ['required', 'string', 'regex:/^(\+98|0)?9\d{9}$/'], // شماره موبایل ایران
            'ip' => ['required','string']
        ], [
            'location_name.required' => 'نام مکان الزامی است.',
            'location_name.string' => 'نام مکان باید به صورت متن باشد.',
            'location_name.max' => 'نام مکان نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'description.string' => 'توضیحات باید به صورت متن باشد.',
            'phone.required' => 'شماره موبایل الزامی است.',
            'phone.string' => 'شماره موبایل باید به صورت متن باشد.',
            'phone.regex' => 'شماره موبایل معتبر نیست.',
            'ip.required' => 'آدرس آی‌پی همراه با پورت الزامی است.',
            'ip.regex' => 'آدرس آی‌پی همراه با پورت معتبر نیست. فرمت باید مثلا 192.168.1.1:8080 باشد.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        $locations = Location::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'مکان با موفقیت ایجاد شد',
            'data' => $locations
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => ['required', 'string', 'regex:/^(\+98|0)?9\d{9}$/'],
            'ip' => ['required','string']
        ], [
            'location_name.required' => 'نام مکان الزامی است.',
            'location_name.string' => 'نام مکان باید به صورت متن باشد.',
            'location_name.max' => 'نام مکان نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'description.string' => 'توضیحات باید به صورت متن باشد.',
            'phone.required' => 'شماره موبایل الزامی است.',
            'phone.string' => 'شماره موبایل باید به صورت متن باشد.',
            'phone.regex' => 'شماره موبایل معتبر نیست.',
            'ip.required' => 'آدرس آی‌پی همراه با پورت الزامی است.',
            'ip.regex' => 'آدرس آی‌پی همراه با پورت معتبر نیست. فرمت باید مثلا 192.168.1.1:8080 باشد.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        $locations = Location::findOrFail($id);
        $locations->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'مکان با موفقیت ویرایش شد',
            'data' => $locations
        ], 200);
    }

    public function destroy($id)
    {
        $locations = Location::findOrFail($id);
        $locations->delete();

        return response()->json([
            'status' => true,
            'message' => 'مکان با موفقیت حذف شد'
        ], 204);
    }
}
