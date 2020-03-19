<?php

namespace bibek\calendar\controllers;

use Carbon\Carbon;
use App\Models\DateSetting;
use Illuminate\Http\Request;
use bibek\calendar\Models\Event;
use App\Http\Controllers\Controller;
use bibek\calendar\helpers\DateHelper;
use bibek\calendar\Models\CalendarHoliday;


class CalendarController extends Controller
{
    public function index()
    {
        $calendarData = $this->loadCalendar(null);
        return [
            'current_day_name' => $calendarData['current_day_name'],
            'date_bs' => $calendarData['date_bs'],
            'today' => $calendarData['today'],
            'weeks' => $calendarData['weeks'],
            'monthHolidays' => $calendarData['monthHolidays'],
            'monthEvents' => $calendarData['monthEvents'],
            'years' => $calendarData['years'],
            'months' => $calendarData['months'],
            'nepaliMonth' => $calendarData['nepaliMonth'],
            'nepaliYear' => $calendarData['nepaliYear'],
            'todayEng' => $calendarData['todayEng']
         ];
    }

    //on search year and month
    public function getCalendar(Request $request)
    {     
        $calendarData = $this->loadCalendar($request);

        return [
            'current_day_name' => $calendarData['current_day_name'],
            'date_bs' => $calendarData['date_bs'],
            'today' => $calendarData['today'],
            'weeks' => $calendarData['weeks'],
            'monthHolidays' => $calendarData['monthHolidays'],
            'monthEvents' => $calendarData['monthEvents'],
            'years' => $calendarData['years'],
            'months' => $calendarData['months'],
            'nepaliMonth' => $calendarData['nepaliMonth'],
            'nepaliYear' => $calendarData['nepaliYear'],
            'todayEng' => $calendarData['todayEng']
         ];
    }

    private function loadCalendar($request)
    {
        $date = Carbon::now()->todatestring();
        $date_ad = str_replace('-', '/',$date);
        $current_day_name = Carbon::parse($date_ad)->dayName;
        $dateHelper = new DateHelper();
        //get current date_bs
        $date_bs = $dateHelper->convertBsFromAd($date_ad);
        $dayMonthYear = $dateHelper->getDayMonthYear($date_bs);
        //on load while not searching, load the calendar from current date
        if($request == null){
            $dateSetting = $dateHelper->getDateTable($dayMonthYear['year'], $dayMonthYear['month']);
            $holidays = CalendarHoliday::whereMonth('date_bs', $dayMonthYear['month'])->whereYear('date_bs', $dayMonthYear['year'])->get();
            $events = Event::whereMonth('date_bs', $dayMonthYear['month'])->whereYear('date_bs', $dayMonthYear['year'])->get();
            $nepali_year = $dayMonthYear['year'];
            $nepali_month = $this->_get_nepali_month($dayMonthYear['month']);
        } else {//when searching month or year
            $year = $request->year;
            $month = $request->month;

            $dateSetting = $dateHelper->getDateTable($year, $month);
            
            $holidays = CalendarHoliday::whereMonth('date_bs', $month)->whereYear('date_bs', $year)->get();
            $events = Event::whereMonth('date_bs', $month)->whereYear('date_bs', $year)->get();

            $nepali_year = $year;
            $nepali_month = $this->_get_nepali_month($month);
        }

        //no of days for displaying on calendar
        $noOfDays = $dateSetting->days_bs;
        $dateAd = $dateSetting->date_ad;

        $calendar_data = [];
        $weekMap = [
            0 => 'SU',
            1 => 'MO',
            2 => 'TU',
            3 => 'WE',
            4 => 'TH',
            5 => 'FR',
            6 => 'SA',
        ];

        $monthHolidays = [];
        $holiday_date_array = [];
        //check the condition if there is no holiday 
        if(!empty($holidays)){
            foreach ($holidays as $holiday) {
                // $holidayDetail['day'] = Carbon::parse($holiday->date_ad)->dayName;
                $holidayDetail['day_number'] = substr($holiday->date_bs, 8, 2);
                $holidayDetail['holiday_name'] = $holiday->name_lc;
                $holidayDetail['date_bs'] = $holiday->date_bs;
    
                $monthHolidays [intval(substr($holiday->date_bs, 8, 2))] = $holidayDetail;

                //store holiday date_number
                $holiday_day_number = (substr($holiday->date_bs, 8, 2));
                array_push($holiday_date_array, $holiday_day_number);
            }  
        }
        
        $monthEvents = [];
        $anotherMonthEvents = [];
        $event_date_array = [];
        $eventDetails = [];

        //check if there is no event
        if (!empty($events)) {
            foreach($events as $key=> $event){
                $eventDetails[$key]['day_number'] = substr($event->date_bs, 8, 2);
                $eventDetails[$key]['event'] = $event->name_lc;
                $eventDetails[$key]['date_bs'] = $event->date_bs;
                $eventDetails[$key]['venue'] = $event->venue;

                //store event date
                $date_number = substr($event->date_bs, 8, 2);
                array_push($event_date_array, $date_number);
            }

            foreach($eventDetails as $key=> $ed){
                //if the day_number starts with 0, store last digit only
                if(substr($ed['day_number'], 0, 1) == 0)
                {
                    $ed['day_number'] = substr($ed['day_number'], 1, 2);
                }
                $monthEvents[$ed['day_number']][$key]=$ed;
            }
        }

        $weeks = [];
        $res = [];
        $nepaliDay = 1;
        //get the day of the week
        for ($week=1; $week <= 6; $week++) {             
            $data = [];
            for ($day = 0; $day < 7; $day++) {
                $first_day = Carbon::parse($dateAd)->addDays($nepaliDay - 1)->dayOfWeek;//get the day number(0-6)
                
                if ($nepaliDay > $noOfDays) {
                    break;
                }
                
                if ($first_day == $day) {
                    $res['day_number'] = $nepaliDay;
                    $res['day_string'] = $weekMap[$day];
                    $nepaliDay++;
                } else {
                    $res['day_number'] = '';
                    $res['day_string'] = $weekMap[$day];
                } 

                //checking for saturday and holidays
                if(($day == 6) || in_array($res['day_number'], $holiday_date_array)){ 
                    $res['is_holiday'] = "yes";
                    if(in_array($res['day_number'], $holiday_date_array)){
                        $res['holiday_name'] = $monthHolidays[$res["day_number"]]["holiday_name"];
                    }
                } else{
                    unset($res['is_holiday']);
                    unset($res['holiday_name']);
                }
                
                //check events
                if(in_array($res['day_number'], $event_date_array)){
                    $res['events'] = "yes";
                    if(array_key_exists($res['day_number'], $monthEvents)){
                        $res['event_details'] = $monthEvents[$res['day_number']];
                    }
                } else{
                    unset($res['events']);
                }

                $data[] = $res;
            }
            $weeks[] = $data;
        }
        
        $years = array('Year', '2070', '2071', '2072', '2073', '2074', '2075', '2076', '2077', '2078', '2079', '2080', '2081');
        $months = array('Month', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');

        $dateFormat = Carbon::parse($date)->formatLocalized('%Y %b %d');
        return [
            'current_day_name' => $current_day_name, 
            'date_bs' => $date_bs,
            'today' => substr($date_bs, 8, 2),
            'weeks' => $weeks,
            'monthHolidays' => $monthHolidays,
            'monthEvents' => $monthEvents,
            'years' => $years,
            'months' => $months,
            'nepaliMonth' => $nepali_month,
            'nepaliYear' => $nepali_year,
            'todayEng' => $dateFormat
        ];        
    }

    //get events
    public function getEvent(Request $request)
    {
        $name_lc = $request->title;
        $date = $request->date;

        $dateHelper = new DateHelper();
        $date_ad = $dateHelper->convertAdFromBs($date);

        //for all events on that day
        if($name_lc == null)
        {
            $event = Event::whereDate('date_ad', $date_ad)->get();
        } else {//for single event
            $event = Event::where('name_lc', $name_lc)->whereDate('date_ad', $date_ad)->first();
        }

        return $event;
    }

    private function _get_nepali_month($m)
		{
			$n_month = FALSE;
			switch ($m)
			{
				case 1:
					// $n_month = "Baishak";
					$n_month = "बैशाख";
					break;
				case 2:
					// $n_month = "Jestha";
					$n_month = "जेठ";
					break;
				case 3:
					// $n_month = "Ashad";
					$n_month = "आषाढ";
					break;
				case 4:
					// $n_month = "Shrawn";
					$n_month = "साउन";
					break;
				case 5:
					// $n_month = "Bhadra";
					$n_month = "भाद्र";
					break;
				case 6:
					// $n_month = "Ashwin";
					$n_month = "आश्विन";
					break;
				case 7:
					// $n_month = "kartik";
					$n_month = "कार्तिक";
					break;
				case 8:
					// $n_month = "Mangshir";
					$n_month = "मंसिर";
					break;
				case 9:
					// $n_month = "Poush";
					$n_month = "पौष";
					break;
				case 10:
					// $n_month = "Magh";
					$n_month = "माघ";
					break;
				case 11:
					// $n_month = "Falgun";
					$n_month = "फाल्गुण";
					break;
				case 12:
					// $n_month = "Chaitra";
					$n_month = "चैत्र";
					break;
			}
			return $n_month;
		}
}
