<?php

namespace gas\calendar\Database\Seeds;

use gas\calendar\Database\Seeds\DateSettingSeeder;
use gas\calendar\Database\Seeds\HolidayTypeSeeder;
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
