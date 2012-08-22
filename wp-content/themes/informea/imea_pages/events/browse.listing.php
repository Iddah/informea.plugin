<?php
/**
 * This is the browse countries page. Displayed when user clicks 'Browse countries' or 'Countries' link from main menu.
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */

 $events = $page_data->get_events_list();
 $treaties = $page_data->get_treaties();
 $base_url = get_bloginfo('url') . '/events';
 $id_treaty = intval($page_data->req_get('id_treaty'));
 $fe_month = intval(get_request_value('fe_month', 0));
 $fe_year = intval(get_request_value('fe_year', 0));

 $years = $page_data->get_years_interval();
 $months = array (1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
		5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
		10 => 'Oct', 11 => 'Nov', 12 => 'Dec' );
?>
<script type="text/javascript">
function fe_validate() {
	var idx_month = $('#fe_month option:selected').index();
	var idx_year = $('#fe_year option:selected').index();
	var id_org = $('#id_treaty option:selected').index();
	if(idx_month == 0 && idx_year == 0 && id_org == 0) {
		alert('Please fill it at least one filter');
		return false;
	} else if(idx_month != 0 && idx_year == 0) {
		alert('When choosing a month, please enter also the year');
		return false;
	}
	return true;
}
</script>


<div class="left details-column-1">
	<?php include(dirname(__FILE__) . '/calendar.inc.php');?>
	<div class="clear separator-15px"></div>
	<?php include(dirname(__FILE__) . '/portlet.search.php'); ?>
	<div class="clear"></div>
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
	<?php if(count($events)) { ?>
	<ul class="events-listing">
		<?php
			$count = 0;
			foreach($events as $evt) {
				$title = subwords($evt->title, 15);
				$anchor = $evt->event_url !== NULL ? "<a class='link' target='_blank' href='$evt->event_url' title='{$evt->title}'>{$title}</a>" : "<span title='{$evt->title}'>$title</span>";
				$status = $page_data->decode_status($evt->status);
				$place = $page_data->event_place($evt);
		?>
			<li>
				<table>
					<tr>
						<td><img src="<?php echo $evt->logo_medium; ?>" /></td>
						<td>
							<h4><?php echo show_event_interval($evt) . ' - ' . $anchor; ?></h4>
							<p>
								Source: <strong><?php echo $evt->short_title; ?></strong>
								<br />
								<?php if($status) { ?>
								Status: <strong><?php echo $status; ?></strong>
								<br />
								<?php } ?>
								<?php if($place) { ?>
								Place: <strong><?php echo $place; ?></strong>
								<?php } ?>
							</p>
						</td>
					</tr>
				</table>
			</li>
		<?php
				$count ++;
			}
		?>
	</ul>
	<?php include(dirname(__FILE__) . '/inc.paginator.php');?>
	<?php
		} else {
	?>
		<p class="events-listing">
			<?php _e('No events recorded for the selected interval/convention.', 'informea'); } ?>
		</p>
	<div class="clear"></div>
</div>
<div class="left details-column-3">
</div>
