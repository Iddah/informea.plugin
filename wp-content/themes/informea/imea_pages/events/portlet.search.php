<div id="events-filter-form">
	<div class="related-box">
		<!-- div class="box-title"><?php _e('Quick search', 'informea'); ?></div -->
		<div class="box-content filter-content">
			<form action="<?php bloginfo('url'); ?>/events" method="get" onsubmit="return fe_validate();">
				<input type="hidden" name="filter" value="1" />
				<div class="title">
					<span><?php _e('Convention', 'informea'); ?></span>
				</div>
				<select id="id_treaty" name="id_treaty" class="select-box">
					<option value=""><?php _e('-- All conventions --', 'informea'); ?></option>
					<?php
						foreach($orgs as $org) {
							$selected = intval($id_treaty) == $org->id;
					?>
					<option value="<?php echo $org->id; ?>"<?php echo $selected ? 'selected="selected"' : '' ; ?>><?php echo $org->short_title; ?></option>
					<?php } ?>
				</select>
				<div class="title">
					<span><?php _e('Date', 'informea'); ?></span>
				</div>
				<select id="fe_month" name="fe_month">
					<option value=""><?php _e('Month', 'informea'); ?></option>
				<?php
					foreach($page_data->get_months_indexed() as $idx => $name) {
						$sel = ($idx == $fe_month) ? ' selected' : '';
						echo "<option value='$idx'$sel>$name</option>";
					}
				?>
				</select>
				<select id="fe_year" name="fe_year">
					<option value=""><?php _e('Year', 'informea'); ?></option>
				<?php
					foreach($years as $year) {
						$sel = ($year == $fe_year) ? ' selected' : '';
						echo "<option value='$year'$sel>$year</option>";
					}
				?>
				</select>
				<div class="clear"></div>
				<a class="button blue" title="Search events" href="javascript:void(0);" onclick="$(this).closest('form').submit();">
					<span><?php _e('Search', 'informea');?></span>
				</a>
				&middot;
				<a href="<?php echo bloginfo('url');?>/events"><?php _e('Clear', 'informea'); ?></a>
			</form>
		</div>
	</div>
</div>

<a class="link" href="<?php bloginfo('url'); ?>/events/rss">
	<img src="<?php bloginfo('template_directory'); ?>/images/rss.png" alt="" title="<?php _e('Events', 'informea'); ?>" class="left rss-icon"/>
	<div>
		<?php _e('Subscribe to events RSS feed', 'informea'); ?>
	</div>
</a>
