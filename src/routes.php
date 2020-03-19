<?php
Route::group(['namespace' => 'controllers'], function (){
    Route::get('calendar', 'CalendarController@index');
    Route::get('getEvent', 'CalendarController@getEvent');  
});