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

        // بارگذاری همه ids فقط یک بار پیش از اجرای حلقه
        $driverIds = DB::table('drivers')->pluck('id')->toArray();
        $companyIds = DB::table('companies')->pluck('id')->toArray();

        // حروف مجاز پلاک فارسی
        $persianLetters = ['الف', 'ب', 'ج', 'د', 'س', 'ص', 'ط', 'ق', 'ل', 'م', 'ن', 'و', 'ه', 'ی'];

        // تولید 50 رکورد
        foreach (range(1, 50) as $index) {
            // تولید شماره پلاک
            $plateRight = str_pad($faker->numberBetween(10, 99), 2, '0', STR_PAD_LEFT); // 2 رقمی
            $plateChar = $faker->randomElement($persianLetters); // حرف فارسی
            $plateLeft = str_pad($faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT); // 3 رقمی
            $plateCity = str_pad($faker->numberBetween(10, 99), 2, '0', STR_PAD_LEFT); // 2 رقمی

            // ترکیب پلاک کامل
            $plateFull = $plateRight . $plateChar . $plateLeft . '-' . $plateCity;

            // وارد کردن رکوردها به جدول trucks
            DB::table('trucks')->insert([
                'driver_id' => $faker->randomElement($driverIds),
                'company_id' => $faker->randomElement($companyIds),
                'plate_right' => $plateRight,
                'plate_char' => $plateChar,
                'plate_left' => $plateLeft,
                'plate_city' => $plateCity,
                'plate_full' => $plateFull,
                'color' => $faker->safeColorName,
                'type' => $faker->randomElement(['غیره', 'کامیون', 'تریلی', 'کامیونت', 'خاور', 'وانت']),
                'weight' => $faker->randomFloat(2, 1, 20),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
