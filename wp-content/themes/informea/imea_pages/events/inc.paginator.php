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
	<?php if($page_data->get_paginator()->has_previous()) { ?>
	<div class="nav-previous navigation">
		<a href="<?php echo $paginator->previous_url()?>#data" onclick="$('#meetings-search-previous').submit();">
			&laquo; <?php _e('Older', 'informea'); ?>
		</a>
	</div>
	<?php } ?>
	<?php /*
	<div>
		<strong><?php _e('Entries')?></strong>
		<?php echo $page_data->get_paginator()->start() ?> - <?php echo $page_data->get_paginator()->end(); ?> of <?php echo $page_data->get_paginator()->total(); ?>
	</div>
	*/ ?>

	<?php if($page_data->get_paginator()->has_next()) { ?>
		<div class="nav-next navigation">
			<a href="<?php echo $paginator->next_url()?>#data" onclick="$('#meetings-search-next').submit();">
				<?php _e('Newer', 'informea'); ?> &raquo;
			</a>
		</div>
	<?php } ?>
<?php }
}
?>
