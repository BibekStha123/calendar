<?php

namespace bibek\calendar\Database\Seeds;

use bibek\calendar\Database\Seeds\DateSettingSeeder;
use bibek\calendar\Database\Seeds\HolidayTypeSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            DateSettingSeeder::class,
            HolidayTypeSeeder::class
        );
    }
}
