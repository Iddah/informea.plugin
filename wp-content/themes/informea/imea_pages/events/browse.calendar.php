<?php
$time = time();
$today = date('j',$time);
$cal_year = get_request_value('fe_year', date('Y', $time));
$cal_month = get_request_value('fe_month', date('n', $time));
$pn_url = get_bloginfo('url') . '/events/calendar';
// Links
$p_url = $pn_url . '?fe_year=' . (($cal_month == 1 ) ? ($cal_year - 1) : $cal_year) . '&fe_month=' . (($cal_month == 1) ? 12 : ($cal_month - 1)) ;
$n_url = $pn_url . '?fe_year=' . (($cal_month == 12 ) ? ($cal_year + 1) : $cal_year) . '&fe_month=' . (($cal_month == 12) ? 1 : ($cal_month + 1)) ;
$pn = array('&laquo;' => $p_url, '&raquo;' => $n_url);

$events = $page_data->get_month_events($cal_month, $cal_year);

$days = array();
$evt_count = array();


for ($i = 1; $i <= date('t', strtotime($cal_year.'/'.$cal_month.'/1')); $i++){
    $day_to_check = strtotime($cal_year.'/'.$cal_month.'/'.$i);
    foreach($events as $event) {
        if ((strtotime($event->start) <= $day_to_check) && (strtotime($event->end) >= $day_to_check)){
            $days[$i] = $i;
	        if(isset($evt_count[$i])) {
        		$evt_count[$i] += 1;
        	} else {
		        $evt_count[$i] = 1;
        	}
        }
    }
}

foreach($evt_count as $start => $count) {
	$c_str = $count == 1 ? '1 event' : $count . ' events';
	$c_str = $days[$start] . "<br /><a title=\"Click to see this day's events\" href=\"javascript:loadEvents($cal_year, $cal_month, $start);\">" . $c_str . '</a>';
	$days[$start] = array(NULL, "Click to see events for this month", FALSE, 'active-day', $c_str);
}

// Mark today differently

if($cal_year == date('Y', $time) && $cal_month == date('n', $time)) {
	$content = NULL;
	if(isset($days[$today])) {
		@list($t1, $t2, $t3, $t4, $t5) = $days[$today];
		$content = $t5;
	}
	$days[$today] = array(NULL, NULL, FALSE, 'today-day', $content);
}
?>
<div class="left details-column-1">
	<?php include(dirname(__FILE__) . '/portlet.search.php'); ?>
	<div class="clear separator-15px"></div>
	<div class="portlet" id="events-box" style="display: none;">
		<div class="pre-title">
			<div class="title">
				<span><?php _e('MEA Events', 'informea'); ?></span>
			</div>
		</div>
		<div class="content"><ul id="events-box-content"></ul></div>
	</div>
</div>
<div class="left details-column-2">
	<div class="view-mode">
		<form action="">
			<label for="view-mode"><?php _e('View', 'informea'); ?></label>
			<select id="view-mode" name="view-mode" onchange="window.location = $(this).val();">
				<?php $selected = ($expand == 'list') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/events/list"><?php _e('List', 'informea'); ?></option>
				<?php $selected = ($expand == 'calendar') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/events/calendar"><?php _e('Calendar', 'informea'); ?></option>
			</select>
		</form>
	</div>
	<div class="clear"></div>
	<div class="events-listing">
		<?php echo generate_calendar_eventspage($cal_year, $cal_month, $days, 3, NULL, 0, $pn); ?>
	</div>
	<div class="clear"></div>
</div>
<div class="left details-column-3">
</div>
<?php
	function inject_js_loadEvents() {
		global $cal_year;
		global $cal_month;

?>
<script type="text/javascript">
	var termsAjaxUrl = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php';
	function loadEvents(year, month, day) {
		$('#events-box').hide(100);
		$('#events-box-content').empty();
		jQuery.post(
			termsAjaxUrl, { action : 'load_events', year : year, month : month, day : day },
			function(events) {
				if(events.length > 0) {
					$.each(events, function(index, event) {
						var event_image = ((event.image !== null) && (event.image.length > 0)) ? event.image : event.logo_medium;
						var event_title = event.event_url === null ? event.title : '<a href="' + event.event_url + '">' + event.title + '</a>';
						$('<li><img src="' + event_image + '" /><div class="item"><strong>' + event.interval + '</strong> ' + event_title + '</div><div class="clear"></div></li>').appendTo($('#events-box-content'));
					});
					$('#events-box').show(100);
				}
			}
		);
	}
	loadEvents(<?php echo $cal_year; ?>, <?php echo $cal_month; ?>, <?php echo date('j'); ?>);
</script>
<?php
	}
	add_action('js_inject', 'inject_js_loadEvents');
?>
