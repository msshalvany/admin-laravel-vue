<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TruckTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // گرفتن تمام شناسه‌های رانندگان و شرکت‌ها از جداول مرتبط
        $driverIds = DB::table('drivers')->pluck('id')->toArray();
        $companyIds = DB::table('companies')->pluck('id')->toArray();

        foreach (range(1, 50) as $index) {
            DB::table('trucks')->insert([
                'driver_id' => $faker->randomElement($driverIds), // انتخاب تصادفی یک راننده
                'company_id' => $faker->randomElement($companyIds), // انتخاب تصادفی یک شرکت
                'plate_number' => strtoupper($faker->bothify('??###??')), // شماره پلاک
                'color' => $faker->safeColorName, // رنگ
                'type' => $faker->randomElement(['غیره','کامیون', 'تریلی', 'کامیونت', 'خاور', 'وانت']), // نوع وسیله نقلیه
                'weight' => $faker->randomFloat(2, 1, 20), // وزن وسیله نقلیه
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
