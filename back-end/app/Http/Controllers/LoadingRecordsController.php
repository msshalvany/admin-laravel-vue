<?php

namespace App\Http\Controllers;

use App\Models\LoadingRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoadingRecordsController extends Controller
{

    public function store(Request $request)
    {
        $messages = [
            'truck_id.required' => 'شناسه کامیون الزامی است.',
            'truck_id.exists' => 'شناسه کامیون معتبر نیست.',
            'location_id.required' => 'شناسه مکان بارگیری الزامی است.',
            'location_id.exists' => 'شناسه مکان بارگیری معتبر نیست.',
            'company_id.required' => 'شناسه شرکت الزامی است.',
            'company_id.exists' => 'شناسه شرکت معتبر نیست.',
            'driver_id.required' => 'شناسه راننده الزامی است.',
            'driver_id.exists' => 'شناسه راننده معتبر نیست.',
            'empty_weight.required' => 'وزن خالی الزامی است.',
            'empty_weight.numeric' => 'وزن خالی باید عدد باشد.',
            'empty_weight.min' => 'وزن خالی نمی‌تواند کمتر از صفر باشد.',
            'empty_weight.max' => 'وزن خالی نمی‌تواند بیشتر از 999999.99 باشد.',
            'loaded_weight.numeric' => 'وزن بار باید عدد باشد.',
            'loaded_weight.min' => 'وزن بار نمی‌تواند کمتر از صفر باشد.',
            'loaded_weight.max' => 'وزن بار نمی‌تواند بیشتر از 999999.99 باشد.',
            'entry_time.required' => 'زمان ورود الزامی است.',
            'entry_time.date_format:H:i' => 'زمان ورود باید یک تاریخ معتبر باشد.',
            'exit_time.date' => 'زمان خروج باید یک تاریخ معتبر باشد.',
            'exit_time.after_or_equal' => 'زمان خروج باید برابر یا بعد از زمان ورود باشد.',
            'driver_star.integer' => 'امتیاز راننده باید عدد صحیح باشد.',
            'driver_star.min' => 'امتیاز راننده نمی‌تواند کمتر از صفر باشد.',
            'driver_star.max' => 'امتیاز راننده نمی‌تواند بیشتر از 5 باشد.',
        ];

        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|exists:trucks,id',
            'location_id' => 'required|exists:locations,id',
            'company_id' => 'required|exists:companies,id',
            'driver_id' => 'required|exists:drivers,id',
            'empty_weight' => 'required|numeric|min:0|max:999999.99',
            'loaded_weight' => 'nullable|numeric|min:0|max:999999.99',
            'entry_time' => 'required|date_format:H:i',
            'exit_time' => 'nullable|date|after_or_equal:entry_time',
            'driver_star' => 'integer|min:0|max:5',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

//        LoadingRecord::create($request->all());

        // ساخت رکورد
        return $request->company_id;
        LoadingRecord::create([
            'truck_id' => $request->truck_id,
            'location_id' => $request->location_id,
            'company_id' => $request->company_id,
            'driver_id' => $request->driver_id,
            'empty_weight' => $request->empty_weight,
            'entry_date' => Carbon::createFromFormat('Y/m/d', $request->entry_date)->toDateString(),
            'entry_time' => $request->entry_time,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'عملیات موفقیت‌آمیز بود',
        ]);
    }


}
