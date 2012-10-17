Between
<br />
<select name="q_start_month">
	<option value="">Month</option>
<?php
	foreach($search->ui_get_months() as $idx => $mon) {
		$search->ui_write_option($idx, $mon, $idx == $search->ui_get_start_month());
	}
?>
</select>
<select name="q_start_year">
	<option value="">Year</option>
<?php
	foreach($search->ui_compute_years() as $y) {
		$search->ui_write_option($y, $y, $y == $search->ui_get_start_year());
	}
?>
</select>
<br />
and
<br />
<select name="q_end_month">
	<option value="">Month</option>
<?php
	foreach($search->ui_get_months() as $idx => $mon) {
		$search->ui_write_option($idx, $mon, $idx == $search->ui_get_end_month());
	}
?>
</select>
<select name="q_end_year">
	<option value="">Year</option>
<?php
	foreach(array_reverse($search->ui_compute_years()) as $y) {
		$search->ui_write_option($y, $y, $y == $search->ui_get_end_year());
	}
?>
</select>
