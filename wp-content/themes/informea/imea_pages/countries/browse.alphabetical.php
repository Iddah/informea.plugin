<?php
/**
 * This is the browse countries page. Displayed when user clicks 'Browse countries' or 'Countries' link from main menu.
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */
$letters = $page_data->get_alphabet_letters();
?>
<div class="left details-column-1">
	<?php include(dirname(__FILE__) . '/portlet.autocomplete.php'); ?>
	<?php include(dirname(__FILE__) . '/portlet.search_nfp.php'); ?>
	<div class="left-box country-l1-membership">
		<h3>Membership data</h3>
		<a href="<?php echo get_bloginfo('url') ?>/download?entity=countries_csv" title="This Excel document contains a table with the list of countries and year when each country signed the treaty or protocols">
			<img src="<?php bloginfo('template_directory'); ?>/images/excel.jpg" />Download country membership data to conventions and protocols
		</a>
	</div>
</div>
<div class="left details-column-2">
	<div class="view-mode">
		<form action="">
			<label for="view-mode"><?php _e('View', 'informea'); ?></label>
			<select id="view-mode" name="view-mode" onchange="window.location = $(this).val();" >
				<?php $selected = ($mode == 'map') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/countries/map"><?php _e('Map', 'informea'); ?></option>
				<?php $selected = ($mode == 'alphabetical') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/countries/alphabetical"><?php _e('Alphabetical', 'informea'); ?></option>
				<?php $selected = ($mode == 'grid') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/countries/grid"><?php _e('Grid', 'informea'); ?></option>
			</select>
		</form>
	</div>
	<div class="clear"></div>

	<div class="toolbar">
		<span class="option-name"><?php _e('Show letter:', 'informea'); ?>&nbsp;</span>
		<?php if($mode == 'grid') { ?>
			<span class="link-button">
				<a href="javascript:void(0); " title="<?php _e('Show all countries', 'informea'); ?>" class="all-letters tooltip alphabet-order-link"><?php _e('All countries', 'informea'); ?></a>
			</span>
		<?php } ?>
		<?php foreach($letters as $letter) { ?>
				<span class="link-button">
				<?php if($mode == 'grid') { ?>
					<a href="javascript:void(0); " class="show-<?php echo $letter->letter; ?> by-letter tooltip alphabet-order-link" title="<?php _e('Show countries beggining with', 'informea'); ?> <?php echo $letter->letter; ?>"><?php echo $letter->letter; ?></a>
				<?php } else { ?>
					<a href="#countries-letter-<?php echo $letter->letter; ?>" class="show-<?php echo $letter->letter; ?> tooltip alphabet-order-link" title="<?php _e('Show countries beggining with', 'informea'); ?> <?php echo $letter->letter; ?>"><?php echo $letter->letter; ?></a>
				<?php } ?>
				</span>
		<?php } ?>
	</div>
	<div class="separator-15px clear"></div>

<?php
	$data = $page_data->index_alphabetical();
	foreach($letters as $count => $letter) {
?>
		<div class="country-alphabet-separator">
			<div class="list-item-title">
				<a class="country-letter" name="countries-letter-<?php echo $letter->letter; ?>"><?php echo $letter->letter; ?></a>
			</div>
	<?php
		$countries = $data[$letter->letter];
		foreach($countries as $idx => $country) {
	?>
			<div class="country-box<?php echo ($idx % 4 == 0) ? ' clear-left' : ''; ?>">
				<a class="country-flag" href="<?php echo get_permalink() . '/' . $country->id ?>" title="Browse <?php echo $country->name; ?> profile">
					<img class="middle" src="<?php bloginfo('url');?>/<?php echo $country->icon_medium; ?>" alt="" title="<?php echo $country->name; ?>" align="middle" />
				</a>
				<a href="<?php echo get_permalink() . '/' . $country->id ?>"  title="Browse <?php echo $country->name; ?> profile"><?php echo $country->name;?></a>
			</div>
	<?php
		}
	?>
			<div class="clear"></div>
	<?php
		if($count && $count % 3 == 0) {
	?>
			<a class="link" href="#top" class="link" style="float: right;"><?php _e('Top', 'informea'); ?> &uarr;</a>
	<?php
		}
	?>
		</div>
<?php
		$count ++;
	}
?>
</div>
<div class="left details-column-3"></div>
