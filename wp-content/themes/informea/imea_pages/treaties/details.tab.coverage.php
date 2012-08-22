<?php
$countries = new imea_countries_page();
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
$mea_id = $treaty->id;
function inject_gis_map() {
	global $mea_id;
	global $countries;
	$imea_options = get_option('informea_options');
?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/OpenLayers/OpenLayers.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/imea_maps.js"></script>
<script type='text/javascript'>
var base_url = "<?php echo bloginfo('url')?>/countries/";
var site_url = "<?php echo get_bloginfo('url')?>";
var mapserver_url = '<?php echo $imea_options["mapserver_url"]; ?>';
var mapserver_localmappath = '<?php echo $imea_options["mapserver_localmappath"]; ?>';
var mapWidth = 480;
var mapHeight = 290;
var have_membership = true;
var membership_filter = '<?php echo $countries->gis_get_membership_filter($mea_id); ?>';
var countries=[
		<?php
			foreach($countries->get_countries() as $c) {
		?>
			[<?php echo($c->id) ?>,"<?php echo($c->code2l) ?>","<?php echo($c->name) ?>","<?php echo($c->icon_medium) ?>"],
		<?php
			}
		?>
];
$(document).ready(function() { init_map();});
</script>
<?php }
add_filter('js_inject', 'inject_gis_map');
?>
<div id="treaty-map">
	<div id="map">
		<a id="tooltip">Tooltip</a>
	</div>
	<div style="float: right;">Powered by <a href="http://mapserver.org/">MapServer</a> and <a href="http://openlayers.org/">OpenLayers</a></div>
</div>
<div class="clear"></div>
<br />
<?php

$parties = $page_data->get_parties($page_data->treaty->id);
if(count($parties)) {
?>
	<table class="datatable treaty-membership">
		<thead>
			<tr>
				<th>Member country</th>
				<th>Since</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($parties as $country) { ?>
			<tr>
				<td>
					<a href="<?php echo $country->id; ?>"><?php echo $country->name; ?></a>
				</td>
				<td>
					<?php echo $country->year; ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>
