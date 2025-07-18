<?php

namespace Database\Factories;

use App\Models\Truck;
use App\Models\Driver;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class TruckFactory extends Factory
{
    protected $model = Truck::class;

    public function definition(): array
    {
        $plateTypes = [
            'private',
            'public',
            'governmental',
            'police',
            'diplomatic',
            'temporary',
            'free_zone',
        ];

        return [
            'driver_id'   => Driver::factory(),
            'company_id'  => Company::factory(),
            'plate_right' => $this->faker->numberBetween(11, 99),
            'plate_char'  => $this->faker->randomElement(['ب', 'د', 'س', 'ش', 'ق']),
            'plate_left'  => $this->faker->numberBetween(100, 999),
            'plate_city'  => $this->faker->numberBetween(10, 99),
            'plate_full'  => $this->faker->regexify('[1-9]{2}[ب-ی][1-9]{3}'),
            'plate_type'  => $this->faker->randomElement($plateTypes),
            'color'       => $this->faker->safeColorName(),
            'type'        => $this->faker->randomElement(['تریلی', 'کامیون', 'وانت']),
            'weight'      => $this->faker->numberBetween(1000, 25000),
        ];
    }
}
