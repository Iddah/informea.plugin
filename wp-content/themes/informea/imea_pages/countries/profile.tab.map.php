<?php
/// External variables - $ramsarSites, $whcSites
function js_inject_country_map_inc() {
	global $country;
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var map;
$(document).ready(function() {
	var latlng = null;
	var zoom = 0;
<?php if($country->id == 184) { // European Union ?>
	latlng = new google.maps.LatLng(50.397, 15.644);
	zoom = 3;
	showMap(latlng, zoom);
	showRamsarSites(map);
<?php } else if($country->id == 61) { // Georgia ?>
	latlng = new google.maps.LatLng(42.180058, 43.699322);
	zoom = 6;
	showMap(latlng, zoom);
	showRamsarSites(map);
<?php } else { ?>
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({ address : '<?php echo str_replace('\'', '\\\'', $country->name); ?>' }, function(results) {
		if(results.length > 0) {
			var p = results[0].geometry.location;
			latlng = new google.maps.LatLng(p.lat(), p.lng());
			zoom = 6;
			showMap(latlng, zoom);
			showRamsarSites(map);
			showWHCSites(map);
		}
	});
<?php } ?>
});

function openRamsarSite(id) {
	var form = document.createElement('form');
	form.setAttribute('method', 'post');
	form.setAttribute('action', 'http://www.wetlands.org/reports/output.cfm');

	var site_id = document.createElement('input');
	site_id.setAttribute('type', 'hidden');
	site_id.setAttribute('name', 'site_id');
	site_id.setAttribute('value', id);
	form.appendChild(site_id);

	var button = document.createElement('input');
	button.setAttribute('type', 'hidden');
	button.setAttribute('name', 'RepAll');
	button.setAttribute('value', '1');
	form.appendChild(button);

	document.body.appendChild(form);
	form.submit();
}

function randomMarkers(map) {
	var marker = new google.maps.Marker({clickable : true, title : '<?php _e('Sample National Focal Point location', 'informea'); ?>', position : map.getCenter()});
	marker.setMap(map);
}

function showRamsarSites(map) {
	<?php
		global $ramsarSites;
		if(count($ramsarSites)) {
	?>
	var infowindow = new google.maps.InfoWindow(
	{
			size: new google.maps.Size(60,50)
	});
	var sites = [<?php
		foreach($ramsarSites as $site) {
			$id = str_replace('ramsar-', '', $site->original_id);
			$marker_icon = get_bloginfo('url') . '/wp-content/uploads/ramsar_marker.png';
			echo "{ the_id : '$id', clickable: true, icon : '$marker_icon', position : new google.maps.LatLng({$site->latitude}, {$site->longitude}), title:\"" . esc_attr($site->name) . "\"},";
		}
	?>
	];
	$.each(sites, function(idx, site) {
		var marker = new google.maps.Marker(site);
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.content = '<h3><a href="javascript:void(0)" onclick="openRamsarSite(' + site.the_id + ')" title="Click to find more info">' + site.title + '</a></h3>';
			infowindow.content += '<div style="float: left;"><img src="<?php bloginfo('url'); ?>/wp-content/uploads/ramsar.png" width="40" height="50"></div>';
			infowindow.content += '<div style="float: left; padding: 5px;"><p>';
			infowindow.content += '<strong>Latitude</strong>: ' + site.position.lat() + '<br /><strong>Longitude</strong>: ' + site.position.lng();
			infowindow.content += '</p></div>';
			infowindow.open(map, marker);
		});
		marker.setMap(map);
	});
	<?php } ?>
}

function showWHCSites(map) {
	<?php
		global $whcSites;
		if(count($whcSites)) {
	?>
	var infowindow = new google.maps.InfoWindow(
	{
			size: new google.maps.Size(60,50)
	});
	var sites = [<?php
		foreach($whcSites as $site) {
			$id = $site->original_id;
			$marker_icon = get_bloginfo('url') . '/wp-content/uploads/whc_marker.png';
			echo "{ the_id : '$id', clickable: true, icon : '$marker_icon', position : new google.maps.LatLng({$site->latitude}, {$site->longitude}), title:\"" . esc_attr($site->name) . "\"},";
		}
	?>
	];
	$.each(sites, function(idx, site) {
		var marker = new google.maps.Marker(site);
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.content = '<h3><a href="<?php echo $site->url; ?>" target="_blank" title="Click to find more info">' + site.title + '</a></h3>';
			infowindow.content += '<div style="float: left;"><img src="<?php bloginfo('url'); ?>/wp-content/uploads/whc.png" width="50" height="50"></div>';
			infowindow.content += '<div style="float: left; padding: 5px;"><p>';
			infowindow.content += '<strong>Latitude</strong>: ' + site.position.lat() + '<br /><strong>Longitude</strong>: ' + site.position.lng();
			infowindow.content += '</p></div>';
			infowindow.open(map, marker);
		});
		marker.setMap(map);
	});
	<?php } ?>
}

function showMap(latlng, zoom) {
	var myOptions = { zoom: zoom, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP, streetViewControl : false };
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	//google.maps.event.addListener(map, 'click', onClickMap);
	//randomMarkers(map);
}

function onClickMap(p) {
	// console.log(p);
}
</script>
<?php
}
add_action('js_inject', 'js_inject_country_map_inc');
?>
<div id="map_canvas" style="width: 720px; height : 490px; border: 1px solid black;"></div>
<div class="clear"></div>
<table class="sites">
	<thead>
		<th><img src="<?php bloginfo('url'); ?>/wp-content/uploads/ramsar.png" width="16" height="16" /> Ramsar sites</th>
		<th><img src="<?php bloginfo('url'); ?>/wp-content/uploads/whc.png" width="16" height="16" /> WHC sites</th>
	</thead>
<?php
	$m = max(count($whcSites), count($ramsarSites));
	for($i = 0; $i < $m; $i++) {
?>
	<tr>
		<td>
		<?php
			if(!empty($ramsarSites[$i])) {
				$rs = $ramsarSites[$i];
				$id = str_replace('ramsar-', '', $rs->original_id);
				echo "<a target=\"_blank\" href=\"javascript:void(0)\" onclick=\"openRamsarSite('$id');\">{$rs->name}</a>";
			}
			?>
		</td>
		<td>
		<?php
			if(!empty($whcSites[$i])) {
				$ws = $whcSites[$i];
				echo "<a target=\"_blank\" href=\"{$ws->url}\">{$ws->name}</a>";
			}
			?>
		</td>
	</tr>
<?php
	}
?>
</table>
