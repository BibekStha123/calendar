<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarHoliday extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_holiday', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('holiday_type_id')->nullable();
            $table->date('date_bs')->nullable();
            $table->date('date_ad')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_lc')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('holiday_type_id','fk_calendar_holiday_holiday_type_id')->references('id')->on('holiday_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_holiday');
    }
}
