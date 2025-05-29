<?php

namespace Database\Seeders;

use App\Models\LoadingRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LoadingRecordTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        // فرض: truck_id، company_id، driver_id مقدار معتبر بین 1 تا 10 دارند
        for ($i = 0; $i < 400; $i++) {
            $status = rand(1, 100) <= 70 ? 'ended' : 'pending'; // 70٪ ended

            // تاریخ ورود تصادفی بین 6 ماه اخیر تا ماه آینده
            $entryDate = Carbon::now()->subMonths(rand(0, 6))->startOfMonth()->addDays(rand(0, 27));
            $entryTime = Carbon::createFromTime(rand(6, 12), rand(0, 59));
            $exitTime = $status === 'ended'
                ? $entryTime->copy()->addHours(rand(2, 5))
                : null;

            $driverIds = DB::table('drivers')->pluck('id')->toArray();
            $companyIds = DB::table('companies')->pluck('id')->toArray();
            $trucks = DB::table('trucks')->pluck('id')->toArray();

            LoadingRecord::create([
                'truck_id' =>$faker->randomElement($trucks),
                'driver_id' => $faker->randomElement($driverIds),
                'company_id' => $faker->randomElement($companyIds),
                'empty_weight' => rand(8000, 12000),
                'loaded_weight' => $status === 'ended' ? rand(15000, 25000) : null,
                'status' => $status,
                'entry_date' => $entryDate->toDateString(),
                'entry_time' => $entryTime->toTimeString(),
                'exit_time' => $exitTime?->toTimeString(),
                'driver_star' => rand(0, 50) / 10, // ستاره بین 0 تا 5
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
