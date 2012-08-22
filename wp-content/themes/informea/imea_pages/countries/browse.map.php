<?php
$mobile_detect = new Mobile_Detect();
//if($mobile_detect->isMobile()) {
	/**
	 * Redirect using Javascript (PHP header() function is not working properly)
	*/
	$alphabetical_url = get_permalink() . '/alphabetical';
?>
<script type="text/javascript">
function is_mobile() {
    var agents = ['android', 'webos', 'iphone', 'ipad', 'blackberry'];
    for(i in agents) {
        if(navigator.userAgent.toLowerCase().search(agents[i]) > -1) {
		window.location = "<?php echo $alphabetical_url; ?>";
        }
    }
    return false;
}

is_mobile();
</script>
<?php
$mea_id = get_request_int('mea_id');
function inject_gis_map() {
	global $mea_id;
	global $page_data;
	$imea_options = get_option('informea_options');
?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/OpenLayers/OpenLayers.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/imea_maps.js"></script>
<!-- script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAkblOrSS9iVzkKUfXj3gOFRS-vBFrjQKcrzhlsz66qTUq-QZVkBQsr4fMu5bKIsczVgkZplWf1tldXA'></script-->

<script type='text/javascript'>
var mapserver_url = '<?php echo $imea_options["mapserver_url"]; ?>';
var mapserver_localmappath = '<?php echo $imea_options["mapserver_localmappath"]; ?>';
var countries=[
		<?php
			foreach($page_data->get_countries() as $c) {
		?>
			[<?php echo($c->id) ?>,"<?php echo($c->code2l) ?>","<?php echo($c->name) ?>","<?php echo($c->icon_medium) ?>"],
		<?php
			}
		?>
];
var base_url = "<?php echo get_permalink()?>";
var site_url = "<?php echo get_bloginfo('url')?>";
var have_membership = <?php echo ($mea_id > 0) ? 'true' : 'false'; ?>;
var membership_filter = '<?php echo $page_data->gis_get_membership_filter($mea_id); ?>';
$(document).ready(function() { init_map();});
</script>
<?php }
	add_filter('js_inject', 'inject_gis_map');
?>

<div class="left details-column-1">

	<?php include dirname(__FILE__) . '/portlet.autocomplete.php'; ?>
	<?php include dirname(__FILE__) . '/portlet.search_nfp.php'; ?>

	<div class="box-content filter-content">
		<div class="title">
			Select MEA
		</div>
		<form action="" method="get" id="country_map_mea_membership">
			<select id="mea_id" name="mea_id" multiple="multiple" onclick="javascript:$('#country_map_mea_membership').submit();">
				<?php
					$treaties = $page_data->get_treaties_with_membership();
					foreach($treaties as $treaty) {
						$selected = $treaty->id == $mea_id ? ' selected="selected"' : '';
						echo "<option value=\"{$treaty->id}\"$selected>{$treaty->short_title}</option>";
					}
				?>
			</select>
		</form>
		<div class="clear"></div>
	</div>

	<div class="left-box country-l1-membership">
		<p class="box-title"><span>Membership data</span></p>
		<div class="clear"></div>
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

	<div id="map">
	<a id="tooltip">Tooltip</a>
	</div>
	<div class="membership-warn">
		<img alt="warn" title="Important, please read carefully" src="<?php bloginfo('template_directory'); ?>/images/warning-small.png" class="middle" />
		Data is still being consolidated, inaccuracies may occur
	</div>

	<div style="float: right;">Powered by <a href="http://mapserver.org/">MapServer</a> and <a href="http://openlayers.org/">OpenLayers</a></div>
</div>
