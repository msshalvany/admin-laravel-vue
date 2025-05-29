<?php

namespace App\Http\Controllers;

use App\Exports\LoadingRecordExport;
use App\Models\LoadingRecord;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class LoadingRecordsController extends Controller
{

    public function index(Request $request)
    {
        $search = trim($request->query('q', ''));
        $sortColumn = $request->query('sort', 'created_at');
        $sortOrder = $request->query('order', 'asc');
        $countPage = $request->query('countPage', 10);

        $LoadingRecord = LoadingRecord::with([
            'truck' => function ($query) {
                $query->withTrashed();
            },
            'locations', // اگر locations هم soft delete دارد، همینجا اضافه کن
            'company' => function ($query) {
                $query->withTrashed();
            },
            'driver' => function ($query) {
                $query->withTrashed();
            },
        ])
            ->where('status', 'ended')
            ->where('id', 'like', "%{$search}%")
            ->orderBy($sortColumn, $sortOrder)
            ->paginate($countPage);

        return response()->json($LoadingRecord, 200);
    }

    public function count()
    {
        return response()->json([
            'count' => LoadingRecord::where('status', 'ended')->count()
        ]);
    }


    public function countPerMonth()
    {
        $fourMonthsAgo = Carbon::now()->subMonths(4)->startOfMonth();

        $records = LoadingRecord::where('status', 'ended')
            ->whereDate('entry_date', '>=', $fourMonthsAgo)
            ->get()
            ->groupBy(function ($record) {
                return Carbon::parse($record->entry_date)->format('Y-m'); // گروه‌بندی بر اساس ماه
            })
            ->map(function ($group, $month) {
                return [
                    'month' => $month,
                    'entries' => $group->count(),
                ];
            })
            ->values(); // پاک‌سازی کلیدهای آرایه

        return response()->json($records);

    }

    public function store(Request $request)
    {
        $messages = [
            'truck_id.required' => 'انتخاب کامیون الزامی است.',
            'truck_id.exists' => 'انتخاب کامیون معتبر نیست.',
            'location_ids.required' => 'انتخاب مکان‌ها الزامی است.',
            'location_ids.array' => 'شناسه مکان‌ها باید یک آرایه باشد.',
            'location_ids.*.exists' => 'یکی از شناسه‌های مکان معتبر نیست.',
            'company_id.required' => 'انتخاب شرکت الزامی است.',
            'company_id.exists' => 'شناسه شرکت معتبر نیست.',
            'driver_id.required' => 'انتخاب راننده الزامی است.',
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
            'driver_star.integer' => 'امتیاز راننده باید عدد صحیح باشد.',
            'driver_star.min' => 'امتیاز راننده نمی‌تواند کمتر از صفر باشد.',
            'driver_star.max' => 'امتیاز راننده نمی‌تواند بیشتر از 5 باشد.',
            'date.required' => 'تاریخ تردد الزامی است'
        ];
        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|exists:trucks,id',
            'location_ids' => 'required|array',
            'location_ids.*' => 'exists:locations,id',
            'company_id' => 'required|exists:companies,id',
            'driver_id' => 'required|exists:drivers,id',
            'empty_weight' => 'required|numeric|min:0|max:999999.99',
            'entry_date' => 'nullable|date|min:0|max:999999.99',
            'entry_time' => 'required|date_format:H:i',
            'date' => 'date',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        // ایجاد رکورد جدید در جدول loading_records
        $loadingRecord = LoadingRecord::create([
            'truck_id' => $request->truck_id,
            'company_id' => $request->company_id,
            'driver_id' => $request->driver_id,
            'empty_weight' => $request->empty_weight,
            'loaded_weight' => $request->loaded_weight,
            'entry_date' => Verta::parse($request->entry_date)->toCarbon(),
            'entry_time' => $request->entry_time,
            'exit_time' => $request->exit_time,
        ]);

        // ذخیره شناسه مکان‌ها در جدول واسط
        $loadingRecord->locations()->sync($request->location_ids);

        return response()->json([
            'status' => true,
            'message' => 'عملیات موفقیت‌آمیز بود',
            'data' => $loadingRecord
        ]);
    }

    public function pendingAll()
    {
        $pendingAll = LoadingRecord::with(['truck', 'locations', 'company', 'driver'])->where('status', 'pending')->get();
        return response()->json([
            'status' => true,
            'data' => $pendingAll
        ]);
    }

    public function destroy($id)
    {
        LoadingRecord::find($id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'تردد با موفقیت حذف شد',
        ]);
    }

    public function finalStore(Request $request)
    {
        $messages = [
            'loaded_weight.numeric' => 'وزن بار باید عدد باشد.',
            'loaded_weight.min' => 'وزن بار نمی‌تواند کمتر از صفر باشد.',
            'loaded_weight.max' => 'وزن بار نمی‌تواند بیشتر از 999999.99 باشد.',
            'driver_star.integer' => 'امتیاز راننده باید عدد صحیح باشد.',
            'driver_star.min' => 'امتیاز راننده نمی‌تواند کمتر از صفر باشد.',
            'driver_star.max' => 'امتیاز راننده نمی‌تواند بیشتر از 5 باشد.',
        ];
        $validator = Validator::make($request->all(), [
            'loaded_weight' => 'required|numeric|min:0|max:999999.99',
            'exit_time' => 'required|date|after_or_equal:entry_time',
        ], $messages);
        $loaded = LoadingRecord::find($request->id);
        $loaded->update([
            'loaded_weight' => $request->loaded_weight,
            'exit_time' => $request->exit_time,
            'driver_star' => $request->driver_star,
            'status' => 'ended',
        ]);
        return response()->json([
            'message' => 'تردد با نهاییی شد',
        ]);
    }

    public function report(Request $request)
    {
        // دریافت مقدار تاریخ و تبدیل آن به آرایه
        $dateRange = explode(',', $request->input('date'));

        // اطمینان از اینکه دو مقدار دریافت شده است
        if (count($dateRange) !== 2) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $from = trim($dateRange[0]);
        $to = trim($dateRange[1]);

        return Excel::download(new LoadingRecordExport($from, $to), 'users.xlsx');
    }


}
