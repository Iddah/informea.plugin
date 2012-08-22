<?php
/**
 * This is the browse countries page. Displayed when user clicks 'Browse countries' or 'Countries' link from main menu.
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */
//$mode;
$letters = $page_data->get_alphabet_letters();
?>
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

	<div class="left-box country-l1-membership">
		<h3>Membership data</h3>
		<a href="<?php echo get_bloginfo('url') ?>/download?entity=countries_csv" title="This Excel document contains a table with the list of countries and year when each country signed the treaty or protocols">
			<img src="<?php bloginfo('template_directory'); ?>/images/excel.jpg" />Download country membership <br /> data to conventions and protocols
		</a>
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
	$data = $page_data->index_grid();
	$columns = $data['column'];
?>
	<table class="datatable-country">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th><?php _e('Country', 'informea') ?></th>
				<?php foreach($columns as $column) { ?>
				<th><?php echo $page_data->wrap_th($column->short_title); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
<?php
	$signatures = $data['signatures'];
	$countries = $data['countries'];
	foreach( $countries as $count => $country ) {
?>
			<tr class="alphabetically-<?php echo strtoupper($country->name[0]); ?> alphabetically-view">
				<td class="number" style="vertical-align: middle;">
					<a class="link" href="<?php echo get_permalink() . '/' . $country->id ?>" title="<?php echo $country->name; ?> &raquo; <?php _e('country profile', 'informea'); ?>">
						<img src="<?php bloginfo('url');?>/<?php echo $country->icon_medium; ?>" alt="" title="<?php echo $country->name; ?>" />
					</a>
				</td>
				<td>
					<a class="link" href="<?php echo get_permalink() . '/' . $country->id ?>" title="<?php echo $country->name; ?> &raquo; <?php _e('country profile', 'informea'); ?>">
					<?php echo $country->name; ?>
					</a>
				</td>
			<?php
				$icount = 0;
				foreach($columns as $column) {
					$id_treaty = $column->id;
					$id_country = $country->id;
					$coldata = '&nbsp;';
					if(isset($signatures[$id_treaty])) {
						$tmparr = $signatures[$id_treaty];
						if(isset($tmparr[$id_country])) {
							$coldata = $tmparr[$id_country];
						}
					}
			?>
				<td class="number<?php echo ($icount % 2) ? ' vzebra' : ''?>" title="<?php echo $column->short_title; ?>">
					<?php echo $coldata; ?>
				</td>
			<?php
					$icount++;
				}
			?>
			</tr>
<?php
		$count ++;
	}
?>
		</tbody>
	</table>
