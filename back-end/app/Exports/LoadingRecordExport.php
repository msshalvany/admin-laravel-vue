<?php
namespace App\Exports;

use App\Models\LoadingRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoadingRecordExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        return LoadingRecord::with(['truck.driver', 'truck.company', 'locations'])
            ->where('status', 'ended')
            ->whereBetween('updated_at', [$this->from, $this->to])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'شناسه بارگیری',
            'تاریخ ورود',
            'زمان ورود',
            'زمان خروج',
            'وزن خالی (تن)',
            'وزن با بار (تن)',
            'امتیاز راننده',
            'پلاک کامیون',
            'رنگ کامیون',
            'نوع کامیون',
            'وزن کامیون (تن)',
            'نام راننده',
            'شماره گواهینامه راننده',
            'نام شرکت',
            'آدرس شرکت',
            'تلفن شرکت',
            'مکان‌های بارگیری'
        ];
    }

    public function map($record): array
    {
        return [
            $record->id,
            $record->entry_date,
            $record->entry_time ?? 'نامشخص',
            $record->exit_time ?? 'نامشخص',
            $record->empty_weight,
            $record->loaded_weight ?? 'نامشخص',
            $record->driver_star,
            $record->truck->plate_number ?? 'نامشخص',
            $record->truck->color ?? 'نامشخص',
            $record->truck->type ?? 'نامشخص',
            $record->truck->weight ?? 'نامشخص',
            $record->truck->driver->name ?? 'نامشخص',
            $record->truck->driver->license_number ?? 'نامشخص',
            $record->truck->company->name ?? 'نامشخص',
            $record->truck->company->address ?? 'نامشخص',
            $record->truck->company->phone ?? 'نامشخص',
            $record->locations->pluck('location_name')->join(', '), // نمایش مکان‌ها در یک ستون
        ];
    }
}
