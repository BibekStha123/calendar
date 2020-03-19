<?php

namespace gas\calendar\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidayTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('holiday_type')->insert([
            array('id' => 1, 'name_en' => 'Public Holiday','name_lc' => 'सार्बजनिक बिदा'),
            array('id' => 2, 'name_en' => 'Local Holiday','name_lc' => 'स्थानिय बिदा'),
            array('id' => 3, 'name_en' => 'Other Holiday','name_lc' => 'अन्य बिदा'),
        ]
        );
    }
}
