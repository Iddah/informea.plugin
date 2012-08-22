<?php
/**
 * This is the paginator form that displays UI for browsing data set
 * @package InforMEA
 * @subpackage Theme
 * @since 0.1
 */
 $paginator = $page_data->get_paginator();

if($paginator->total()) {
	if ($paginator->get_method() == 'get') {
?>
<table>
	<tr>
		<td width="33%" align="left">
		<?php if($page_data->get_paginator()->has_previous()) { ?>
			<div class="nav-previous navigation">
				<a href="<?php echo $paginator->previous_url()?>#data" onclick="$('#meetings-search-previous').submit();">
					<span class="meta-nav">
						<img class="prev-icon middle" src="<?php bloginfo('template_directory'); ?>/images/prev.png" title="<?php _e('Back', 'informea'); ?>" />
					</span>
					<?php _e('Previous', 'informea'); ?>
				</a>
			</div>
			<?php } ?>
		</td>
		<td>
			<strong><?php _e('Entries')?></strong>
			<?php echo $page_data->get_paginator()->start() ?> - <?php echo $page_data->get_paginator()->end(); ?> of <?php echo $page_data->get_paginator()->total(); ?>
		</td>
		<td width="33%" align="right">
		<?php if($page_data->get_paginator()->has_next()) { ?>
			<div class="nav-next navigation">
				<a href="<?php echo $paginator->next_url()?>#data" onclick="$('#meetings-search-next').submit();">
					<?php _e('Next', 'informea'); ?>
					<span class="meta-nav">
						<img class="next-icon middle" src="<?php bloginfo('template_directory'); ?>/images/next.png" title="<?php _e('Forward', 'informea'); ?>" />
					</span>
				</a>
			</div>
		<?php } ?>
		</td>
	</tr>
</table>
<?php
	}
	if ($paginator->get_method() == 'post') {
?>
<table>
	<tr>
		<td width="33%" align="left">
			<?php if($paginator->has_previous()) { ?>
			<form action="<?php $paginator->get_target_url(); ?>" method="post" id="meetings-search-previous">

				<?php echo $paginator->previous_form(); ?>
				<div class="nav-previous navigation">
					<a href="javascript:void(0);" onclick="$('#meetings-search-previous').submit();">
						<span class="meta-nav">
							<img class="prev-icon middle" src="<?php bloginfo('template_directory'); ?>/images/prev.png" title="See newer events" />
						</span>
						<?php _e('Previous', 'informea'); ?>
					</a>
				</div>
				<noscript>
					<input type="submit" name="prev_page" value="<?php _e('Back', 'informea'); ?>" />
				</noscript>
			</form>
			<?php } ?>
		</td>
		<td>
			<strong><?php _e('Entries')?></strong>
			<?php echo $paginator->start(); ?> - <?php echo $paginator->end(); ?> of <?php echo $paginator->total(); ?>
		</td>
		<td width="33%" align="right">
			<?php if($paginator->has_next()) { ?>
			<form action="" method="post" id="meetings-search-next">
				<?php echo $paginator->next_form(); ?>
				<div class="nav-next navigation">
					<a href="javascript:void(0);" onclick="$('#meetings-search-next').submit();">
						<?php _e('Next', 'informea'); ?>
						<span class="meta-nav">
							<img class="next-icon middle" src="<?php bloginfo('template_directory'); ?>/images/next.png" title="<?php _e('Forward', 'informea'); ?>" />
						</span>
					</a>
				</div>
				<noscript>
					<input type="submit" name="next_page" value="<?php _e('Forward', 'informea'); ?>" />
				</noscript>
			</form>
			<?php } ?>
		</td>
	</tr>
</table>
<?php
	}
}
?>
