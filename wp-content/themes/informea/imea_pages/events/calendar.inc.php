<?php
$time = time();
$today = date('j',$time);
$cal_year = get_request_value('fe_year', date('Y', $time));
$cal_month = get_request_value('fe_month', date('n', $time));
$pn_url = get_bloginfo('url') . '/events';
// Links
$p_url = $pn_url . '?filter=1&fe_year=' . (($cal_month == 1 ) ? ($cal_year - 1) : $cal_year) . '&fe_month=' . (($cal_month == 1) ? 12 : ($cal_month - 1)) ;
$n_url = $pn_url . '?filter=1&fe_year=' . (($cal_month == 12 ) ? ($cal_year + 1) : $cal_year) . '&fe_month=' . (($cal_month == 12) ? 1 : ($cal_month + 1)) ;
$pn = array('&laquo;' => $p_url, '&raquo;' => $n_url);

// Today
$days = array();
$cal_events = $page_data->get_month_events($cal_month, $cal_year);


$event_url = get_bloginfo('url') . "/events/?filter=1&fe_month=$cal_month&fe_year=$cal_year";
for ($i = 1; $i <= date('t', strtotime($cal_year.'/'.$cal_month.'/1')); $i++){
    $day_to_check = strtotime($cal_year.'/'.$cal_month.'/'.$i);
    foreach($cal_events as $event) {
        if ((strtotime($event->start) <= $day_to_check) && (strtotime($event->end) >= $day_to_check)){
            $days[$i] = array($event_url, "Click to see events for this month", FALSE, 'active-day');
        }
    }
}

if($cal_year == date('Y', $time) && $cal_month == date('n', $time)) {
    if (!isset($days[$today])){
	    $days[$today] = array(NULL, NULL, NULL, 'today-day', NULL);
	}
	else{
	    $days[$today] = array($event_url, NULL, NULL, 'active-day today-day', NULL);
	}
}

echo generate_calendar($cal_year, $cal_month, $days, 3, NULL, 0, $pn);
?>
