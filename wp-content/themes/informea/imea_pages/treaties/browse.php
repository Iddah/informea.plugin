<?php
	global $wp_query;
	$page_data->category = urldecode($page_data->category);
	$treaties = $page_data->get_treaties_by_region_by_theme($page_data->category);
?>
<div class="left details-column-1">
	<?php
		$q_use_decisions = false;
		$q_use_treaties = true;
		$q_tab = 3;
		$portlet_decision_filter_title = 'Search treaty texts';
		include(dirname(__FILE__) . '/../decisions/portlet.filter.php');
	?>
	<div class="clear"></div>
	<br />
	<?php
	$cloud_items_nr = 20;
	$terms = new Thesaurus();
	$top_concepts = $terms->get_top_concepts();
	$popular_terms = $page_data->get_popular_terms(NULL, 6);
	$cloud_terms = array_merge($top_concepts, $popular_terms);
	include(dirname(__FILE__) . '/../portlet.terms-cloud.inc.php'); ?>
</div>
<script type="text/javascript">
function onChangeView(T) {
	var url = '' + window.location;
	url = url.replace('icon', '');
	console.log(url);
	url = url.replace('grid', '');
	console.log(url);
	window.location = url + '/' + $(T).val();
}
</script>
<div class="left details-column-2">
	<?php
		$css_tab_g = $page_data->is_tab_global() ? 'tab-active' : 'tab';
		$css_tab_r = !$page_data->is_tab_global() ? 'tab-active' : 'tab';

		$tabs = array('Africa', 'Asia Pacific', 'Europe', 'Latin America and the Carribean', 'North America', 'West Asia');
		$tabs = array_unique(array_merge($tabs, $page_data->get_regions()));
		sort($tabs);
	?>
	<div class="tab-menu">
		<ul>
			<li>
				<?php imea_anchor(array( 'label' => __('Global', 'informea'), 'href' => 'treaties/region/Global', 'css' => $css_tab_g)); ?>
			</li>
			<?php
				foreach($tabs as $idx => $region) {
					$css_tab_r = $page_data->category == $region ? 'tab-active' : 'tab';
					$disabled = $page_data->region_has_treaties($region) == '0';
			?>
			<li>
				<?php
					if(!$disabled) {
						imea_anchor(array( 'label' => $region, 'href' => 'treaties/region/' . $region, 'css' => $css_tab_r));
					} else {
				?>
					<a href="javascript:void(0);" class="disabled"><?php echo $region; ?></a>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
	</div>
	<!-- END TABS -->

<?php
	if(!empty($treaties)) {
?>
		<div class="view-mode">
			<form action="">
				<label for="view-mode"><?php _e('View', 'informea'); ?></label>
				<select id="view-mode" name="view-mode" onchange="onChangeView(this);">
					<?php $selected = ($expand == 'icon') ? 'selected="selected "' : ''; ?>
					<option <?php echo $selected;?>value="icon"><?php _e('Icon', 'informea'); ?></option>
					<?php $selected = ($expand == 'grid') ? 'selected="selected "' : ''; ?>
					<option <?php echo $selected;?>value="grid"><?php _e('Grid', 'informea'); ?></option>
				</select>
			</form>
		</div>
		<div class="clear"></div>
<?php
		if($page_data->expand == 'icon') {
?>
	<!--
		- START TAB CONTENT add class="tab-content" instead of old simple div tag
		- Move the options bar at the beggining of the tab content
	-->
	<div class="tab-content">
		<?php
			foreach($treaties as $theme => $treaties) {
		?>
			<div style="margin-top: 15px;">
				<h2><?php echo $theme; ?></h2>
			<?php
				foreach($treaties as $idx => $_treaty) {
					if($_treaty->id == 9) {
						$tooltip = __('The Nagoya protocol was adopted at CBD COP 10 and will enter into force 90 days after the ratification by 50 parties. Open Nagoya Protocol', 'informea');
					} else {
						$tooltip = __('View this treaty', 'informea');
					}
					if($page_data->category == 'Europe' && $idx % 7 == 0) { echo '<div class="clear"></div>'; }
			?>
				<div class="treaty-entry">
					<div class="treaty-icon">
						<a class="link" href="<?php imea_url('treaties/' . $_treaty->odata_name); ?>" title="<?php echo $tooltip; ?>"><img src="<?php echo $_treaty->logo_medium; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" /></a>
						<br />
						<a class="link" href="<?php imea_url('treaties/' . $_treaty->odata_name); ?>" title="<?php echo $tooltip; ?>">
							<?php echo $_treaty->short_title_alternative; ?>
						</a>
					<?php
						if(!empty($_treaty->theme_secondary)) {
					?>
						<br />
						<span class="text-grey">(<?php echo $_treaty->theme_secondary; ?>)</span>
					<?php } ?>
					</div>
				</div>
		<?php
			}
		?>
				<div class="clear"></div>
			</div>
		<?php
			}
		?>
	</div>
<?php
		}
		if($page_data->expand == 'grid') {
		?>
	<table class="datatable treaty-table">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th><?php _e('Year', 'informea'); ?></th>
				<th><?php _e('Depository', 'informea'); ?></th>
				<th><?php _e('Links', 'informea'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($treaties as $theme => $treaties) {
		?>
		<tr>
			<td colspan="5"><h2 style="margin-top: 15px;"><?php echo $theme; ?></h2></td>
		</tr>
		<?php
			foreach($treaties as $_treaty) {
				if($_treaty->id == 9) {
					$tooltip = __('The Nagoya protocol was adopted at CBD COP 10 and will enter into force 90 days after the ratification by 50 parties. Open Nagoya Protocol', 'informea');
				} else {
					$tooltip = __('Open', 'informea') . ' ' . $_treaty->short_title;
				}
		?>
			<tr>
				<td class="logo">
					<a href="<?php imea_url('treaties/' . $_treaty->odata_name); ?>"><img src="<?php echo $_treaty->logo_medium; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" title="Click to see details" /></a>
				</td>
				<td class="middle">
					<a href="<?php imea_url('treaties/' . $_treaty->odata_name); ?>" title="Click to see details"><?php echo $_treaty->short_title; ?></a>
				</td>
				<td class="middlecenter">
					<?php echo (@date('Y', strtotime($_treaty->start)) > 0) ? @date('Y', strtotime($_treaty->start)) : '-'; ?>
				</td>
				<td class="middle">
					<?php echo $_treaty->depository; ?>
				</td>
				<td class="middlecenter">
					<a href="<?php echo parse_url($_treaty->url, PHP_URL_SCHEME)."://".parse_url($_treaty->url, PHP_URL_HOST); ?>"
						title="<?php _e('Visit convention website. This will open in a new window', 'informea'); ?>"
						target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/globe.png" /></a>
					<a href="<?php echo $_treaty->url; ?>"
						title="<?php _e('Read treaty text on the Convention website. This will open in a new window', 'informea'); ?>"
						target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/small-treaty.png" /></a>
				</td>
			</tr>
<?php
			}
		}
?>
		</tbody>
	</table>
	<br />
<?php
		}
	}
?>
</div>
<div class="left details-column-3"></div>
