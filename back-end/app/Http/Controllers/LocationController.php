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
            'message' => 'مکان‌ها با موفقیت دریافت شدند',
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
        // اعتبارسنجی به زبان فارسی
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'location_name.required' => 'نام مکان الزامی است.',
            'location_name.string' => 'نام مکان باید به صورت متن باشد.',
            'location_name.max' => 'نام مکان نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'description.string' => 'توضیحات باید به صورت متن باشد.',
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
        // اعتبارسنجی به زبان فارسی
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'location_name.required' => 'نام مکان الزامی است.',
            'location_name.string' => 'نام مکان باید به صورت متن باشد.',
            'location_name.max' => 'نام مکان نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'description.string' => 'توضیحات باید به صورت متن باشد.',
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
