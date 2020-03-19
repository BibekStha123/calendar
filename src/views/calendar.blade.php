@include('calendar::header')

@php
$request = request();
$session = $request->getSession();
$todayDate = $session->get('today_date');

$calendar = new gas\calendar\controllers\CalendarController();
if($request->year == null || $request->month == null)
{
    $calendarData = $calendar->index();
} else {
    $calendarData = $calendar->getCalendar($request);
}

@endphp

<div class="row card calendar-title">
    <div class="col-md-8">
        <h5><b><u>आजको मिती:</u></b></h5>
        <h5><b>बि.स:&nbsp;{{ $todayDate }} &nbsp;||&nbsp; ई.स:&nbsp;{{ $calendarData['todayEng'] }}  {{ $calendarData['current_day_name'] }}</h5>
        <form class="form-group" action="" method="GET" id="calendar-form">
            <div class="col-md-2">
                <select name="year" id="year" class="form-control">
                    @foreach ($calendarData['years'] as $year)
                        <option value="{{ $year }}">{{  $year   }}</option>                        
                    @endforeach
                </select>
            </div> 
            <div class="col-md-2">
                <select name="month" id="month" class="form-control">
                    @foreach ($calendarData['months'] as $month)
                        <option value="{{ $month }}">{{  $month   }}</option>                        
                    @endforeach
                </select>
            </div>
            <button class="btn btn-default" id="search-btn" type="submit">Search</button>&nbsp;&nbsp;&nbsp;
            <a type="submit" class="button btn-default glyphicon glyphicon-fast-backward tooltipicon" id="pre-year" title="Previous Year"></a>
            <a type="submit" class="button btn-default glyphicon glyphicon-step-backward tooltipicon" id="pre-month" title="Previous Month"></a>
            <a type="submit" class="button btn-default glyphicon glyphicon-step-forward tooltipicon" id="next-month" title="Next Month"></a>
            <a type="submit" class="button btn-default glyphicon glyphicon-fast-forward tooltipicon" id="next-year" title="Next Year"></a>
            <span hidden class="tooltiptext">Next Year</span>
        </form>
    </div>
    <div class="col-md-4" style="right:12%">
        <h4 style="color: green; padding-top: 20%">{{ $calendarData['nepaliYear'] }}, {{ $calendarData['nepaliMonth'] }}</h4>
    </div>
</div>

<div class="row display-calendar">
    <div class="calendar col-md-8">
        <div class="calendar__header">
          <div class="w_day">आइतबार </div>
          <div class="w_day">सोमबार </div>
          <div class="w_day">मंगलबार </div>
          <div class="w_day">बुधबार </div>
          <div class="w_day">बिहिबार </div>
          <div class="w_day">शुक्रबार </div>
          <div class="w_day" style="color:red">शनिबार </div>
        </div>
        <div class="calendar__week">
            @foreach ($calendarData['weeks'] as $week)
                @foreach ($week as $week_day)       
                    <div class="calendar__day">
                        <div class="calendar__holiday" >
                            @if(isset($week_day['holiday_name']))
                                {{ $week_day['holiday_name'] }}
                            @else
                                &nbsp;
                            @endif
                        </div>
                        <div class="day_of_month {{isset($week_day['is_holiday']) ? 'calendar__holiday' : ''}}">
                            <a class="day-{{ $week_day['day_number']}}" data-value="{{ $week_day['day_number'] }}">{{ gas\calendar\helpers\NepaliHelper::englishToNepali(isset($week_day['day_number']) ? $week_day['day_number'] : '') }}</a>
                        </div>
                        @if (isset($week_day['events']))
                            @foreach ($week_day['event_details'] as $events)
                                <div class="calendar__event">
                                    <a href="#" class="calendar-event-title" data-value="{{ $week_day['day_number'] }}">{{  $events['event']   }}</a>
                                </div>                             
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="row">
        {{-- Holidays --}}
        <div class="col-md-4">
            <h2>Holidays</h2>
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Holiday</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($calendarData['monthHolidays'] != null)
                        @foreach ($calendarData['monthHolidays'] as $monthHoliday)
                            <tr class='holiday-row' data-href='' style="color: red; cursor: pointer;">
                                <td>{{ $monthHoliday['date_bs'] }}</td>
                                <td>{{ $monthHoliday['holiday_name'] }}</td>
                            </tr>
                        @endforeach                        
                    @else
                        <tr style="color: red;">
                            <td style="font-size: 150%"><b>No Holidays</b></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {{-- Events --}}
        <div class="col-md-4">
            <h2>Events</h2>
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Events</th>
                        <th>Venue</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($calendarData['monthEvents'] != null)
                        @foreach ($calendarData['monthEvents'] as $monthEvent)
                            @foreach ($monthEvent as $item)
                                <tr class='event-row' data-href='' style="color: #0000CD; cursor: pointer;">
                                    <td>{{ $item['date_bs'] }}</td>
                                    <td>{{ $item['event'] }}</td>
                                    <td>{{ $item['venue'] }}</td>
                                </tr> 
                            @endforeach                                       
                        @endforeach                      
                    @else
                        <tr style="color: #0000CD;">
                            <td style="font-size: 150%"><b>No Events</b></td>
                        </tr>
                    @endif                    
                </tbody>
            </table>
        </div>
    </div>  
</div>
{{-- modal --}}
<div class="modal" id="event-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Events</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Events</th>
                        <th>Venue</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="color: #0000CD">
                        <td id="event-date"></td>
                        <td id="event-name"></td>
                        <td id="event-venue"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

<script>
    $(document).ready(function(){
        $url_string = window.location.href;
        $url = new URL($url_string);
        var year = $url.searchParams.get("year");
        var month = $url.searchParams.get("month");

        $date = '<?php echo $calendarData['date_bs']; ?>';
        $dateAry = $date.split("/");
        var dateYear = $dateAry[0];
        var dateMonth = $dateAry[1];

        if(year == null || month == null){
            $("#year option:selected").html(dateYear);
            $("#year option:selected").val(dateYear);
            $("#month option:selected").html(dateMonth);
            $("#month option:selected").val(dateMonth);
        }else {
            $("#year option:selected").html(year);
            $("#month option:selected").html(month);
            $("#year option:selected").val(year);
            $("#month option:selected").val(month)
        }

        //on next month
        $("#next-month").on('click', function(){            
            $year = parseInt($("#year option:selected").val());
            $month = parseInt($("#month option:selected").val());

            $month += 1;
            //if month is greater than 12, increase year and make month 1
            if($month > 12){
                $year += 1;
                $month = 1;
                $("#year option:selected").val($year);
                $("#month option:selected").val($month);
            } else {
                $("#month option:selected").val($month);
            }
            //increment month and post form
            $("#calendar-form").submit();    
        });

        //on next year
        $("#next-year").on('click', function(){
            $year = parseInt($("#year option:selected").val());
            // console.log($year);
            $("#year option:selected").val($year+=1);
            $("#calendar-form").submit();
        });

        //on previous month
        $("#pre-month").on('click', function(){
            $year = parseInt($("#year option:selected").val());
            $month = parseInt($("#month option:selected").val());

            $month -= 1;
            //if month is less than 1, decrease year and make month 12
            if($month < 1){
                $year -= 1;
                $month = 12;
                $("#year option:selected").val($year);
                $("#month option:selected").val($month);
            } else {
                $("#month option:selected").val($month);
            }
            //increment month and post form
            $("#calendar-form").submit(); 
            
        });

        //on previous year
        $("#pre-year").on('click', function(){
                        
            $year = parseInt($("#year option:selected").val());
            // console.log($year);
            $("#year option:selected").val($year-=1);
            $("#calendar-form").submit(); 
        });

        //for link in event tr
        $(".event-row").on('click', function(){
            window.location = $(this).data("href");
        });

        //for link in holiday tr
        $(".holiday-row").on('click', function(){
            window.location = $(this).data("href");
        });

        //display single event on modal
        $(".calendar-event-title").on('click', function(){
            $title = $(this).html();
            //get date from event table
            $date = $('.event-row').find('td').html();
            //clicked day_number
            $day_number = $(this).data("value");

            $("#event-modal").modal();            
            //remove the row
            $(".modal-body").find('tbody tr').remove();
            
            //replace the day_number with clicked day_number
            $date = $date.split("-");
            $date = $date[0]+'/'+$date[1]+'/'+$day_number;
            
            $.ajax({
                type: 'GET',
                url: 'getEvent',
                data:{
                    title : $title,
                    date : $date
                },
                success: function(res){
                    $tbody = $(".modal-body").find('tbody');
                    $tr = '<tr style="color: #0000CD">'+
                                '<td>'+res['date_bs']+'</td>'+
                                '<td>'+res['name_lc']+'</td>'+
                                '<td>'+res['venue']+'</td>'+
                            '</tr>';
                    $tbody.append($tr);                 
                },
                error: function(err){
                    alert("error");
                }
            });
        });

        //total events on that day
        $(".day_of_month").on('click', function(){            
            //get the month's event date
            $date = $('.event-row').find('td').html();
            $day_number = $(this).find('a').data("value");

            //if no event available
            if($date == undefined){
                alert('No events available on this day');
            } else {
                $date = $date.split("-");
                $date = $date[0]+'/'+$date[1]+'/'+($day_number);
                
                $.ajax({
                    type: 'GET',
                    url: 'getEvent',
                    data:{
                        date : $date
                    },
                    success: function(res){
                        if(res.length == 0){
                           alert("no events on this day");                            
                        } else {
                            $("#event-modal").modal();            
                            //remove the row
                            $(".modal-body").find('tbody tr').remove();

                            $tbody = $(".modal-body").find('tbody');
                            res.forEach(event => {
                                $tr = '<tr style="color: #0000CD">'+
                                        '<td>'+event['date_bs']+'</td>'+
                                        '<td>'+event['name_lc']+'</td>'+
                                        '<td>'+event['venue']+'</td>'+
                                    '</tr>';
                                $tbody.append($tr);  
                            });                            
                        }                                        
                    },
                    error: function(err){
                        alert("error");
                    } 
                });            
            }
        });
    }); 
</script>
