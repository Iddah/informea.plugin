<?php
	$months = array (1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
		5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
		10 => 'Oct', 11 => 'Nov', 12 => 'Dec' );
	$year_intv = $page_data->get_years_interval();
	$highlight_search = get_request_value('highlight_search', '');
	$highlight_month = intval(get_request_value('highlight_month', 0));
	$highlight_year = intval(get_request_value('highlight_year', 0));
?>

<script type="text/javascript">
function date_validate() {
	if($('#highlight_month option:selected').index() == 0 && $('#highlight_year option:selected').index() == 0) {
		return true;
	}
	if($('#highlight_year option:selected').index() != 0) {
		return true;
	}
	alert('Please select year/month, then press Search');
	return false;
}
</script>
<div id="highlights-filter-form">
	<div class="related-box">
		<!-- div class="box-title"><?php _e('Quick search', 'informea'); ?></div -->
		<div class="box-content filter-content">
			<form action="<?php bloginfo('url');?>/highlights" method="GET" name="highlight_search_form" onsubmit="return date_validate();">
				<div id="highlight_search">
					<div class="title">
						<span><?php _e('Containing text', 'informea'); ?></span>
					</div>
					<input class="input-text" id="highlight_search" name="highlight_search" type="text" size="25" value="<?php echo $highlight_search; ?>" />

					<div class="title">
						<span><?php _e('Date', 'informea'); ?></span>
					</div>
					<select id="highlight_month" name="highlight_month" style="float:left;">
						<option value=""><?php _e('Month', 'informea'); ?></option>
						<?php foreach($months as $idx => $name) {
							$sel = ($idx == $highlight_month) ? ' selected' : '';
							echo "<option value='$idx'$sel>$name</option>";
						}?>
					</select>
					&nbsp;
					<select id="highlight_year" name="highlight_year">
						<option value=""><?php _e('Year', 'informea'); ?></option>
						<?php $i = $year_intv->min;
						while($i <= $year_intv->max) {
							$sel = ($i == $highlight_year) ? ' selected' : '';
							echo "<option value='$i'$sel>$i</option>";
							$i++;
						}?>
					</select>
					<div class="clear"></div>
					<a class="button blue" title="Search events" href="javascript:void(0);" onclick="$(this).closest('form').submit();">
						<span><?php _e('Search', 'informea');?></span>
					</a>
					&middot;
					<a href="<?php echo bloginfo('url');?>/highlights"><?php _e('Clear', 'informea'); ?></a>
				</div>
			</form>
		</div>
	</div>
</div>

<!--a class="link" href="<?php bloginfo('url'); ?>/highlights/rss">
	<img src="<?php bloginfo('template_directory'); ?>/images/rss.png" alt="" title="<?php _e('Events', 'informea'); ?>" class="left rss-icon"/>
	<div>
		<?php _e('Subscribe to highlights RSS feed', 'informea'); ?>
	</div>
</a-->
