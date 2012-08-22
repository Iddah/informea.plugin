<?php
$contact_data = $page_data->get_contacts();
$treaties_contacts = $contact_data['treaties'];
$all_contacts = $contact_data['contacts'];

$expand = get_request_variable('expand', 'str', 'map'); // or reports or country

$reports_data = $page_data->get_national_reports();
$treaties_reports = $reports_data['treaties'];
$all_reports = $reports_data['reports'];

$plans_data = $page_data->get_national_plans();
$treaties_plans = $plans_data['treaties'];
$all_plans = $plans_data['plans'];

$country = NULL;
$ramsarSites = $page_data->get_ramsar_sites();
$whcSites = $page_data->get_whc_sites();

if($page_data->country !== NULL) {
	$country = $page_data->country;
	$name = $page_data->country->name;
	$icon_url = get_bloginfo('url') . '/' . $page_data->country->icon_large;
}

$peblds = $page_data->get_peblds_data($id_country);

if($expand == 'sendmail') {
	$contact = $page_data->get_contact_for_id($id_contact);
}
?>
<style type="text/css">
.contact-name {
	font-size: 1.5em;
	font-weight: bold;
	margin: 1em 0 0.3em 0;
}

.contact-address {
	margin-left: 1em;
}
</style>
<div class="clear"></div>
<div class="left details-column-1">
	<div class="box-content filter-content">
		<form id="change-country" action="<?php echo bloginfo('url'); ?>/countries" method="get">
			<select name="id_country" id="id_country" class="column-select" title="Use this drop-down box to select another country" onchange="if(this.selectedIndex !== 0) {$('#change-country').submit(); }">
				<option value="">-- <?php _e('Select another country', 'informea'); ?> --</option>
			<?php
				foreach($page_data->get_countries() as $c) {
			?>
				<option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
			<?php
				}
			?>
			</select>
		</form>
	</div>
	<?php
	if($expand == 'nfp') {
		include(dirname(__FILE__) . '/portlet.search_nfp.php');
	}
	?>
	<?php
		$rUN = new UNDataWebsiteParser($page_data->country->id, $page_data->country->name);
		$un_country_img_url = $rUN->get_map_image();
		if($un_country_img_url != null) {
			echo '<br /><img src="' . $un_country_img_url . '" width="230" height="230" />';
		}

		$env_data = $rUN->get_environmental_data();
		if(!empty($env_data)) { echo '<div id="country_un_env_data">' . $env_data . '</div>'; }
	?>
</div>

<div class="left details-column-2">
<?php if($expand == 'country') { ?>
	<?php
		include(dirname(__FILE__) . '/portlet.map.php');
	?>
	<div id="map_canvas" style="width: 100%; height : 400px; border: 1px solid black;"></div>
	<a class="link" href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>" style="padding: 0; margin: 0;" title="<?php _e('Click to close the map', 'informea'); ?>"><?php _e('Hide map', 'informea'); ?></a>
<?php } else { ?>
	<div class="tab-menu">
		<ul>
			<li><a <?php echo ($expand == 'map') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/map"><?php _e('Map &amp; sites', 'informea'); ?></a></li>
			<li><a <?php echo ($expand == 'membership') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/membership"><?php _e('MEA membership', 'informea'); ?></a></li>
			<li><a <?php echo ($expand == 'nfp') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/nfp"><?php _e('Focal points', 'informea'); ?></a></li>
			<li><a <?php echo ($expand == 'reports') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/reports"><?php _e('Reports', 'informea'); ?> (<?php echo count($all_reports); ?>)</a></li>
			<li><a <?php echo ($expand == 'plans') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/plans"><?php _e('Plans', 'informea'); ?> (<?php echo count($all_plans); ?>)</a></li>
			<li><a <?php echo ($expand == 'ecolex/legislation') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/ecolex/legislation"><?php _e('Legislation', 'informea'); ?></a></li>
			<li><a <?php echo ($expand == 'ecolex/caselaw') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/ecolex/caselaw"><?php _e('Case Law', 'informea'); ?></a></li>
			<?php if($peblds->has_data) { ?>
			<li><a <?php echo ($expand == 'peblds') ? 'class="tab-active"' : ''; ?> href="<?php bloginfo('url'); ?>/countries/<?php echo $country->id; ?>/peblds"><?php _e('PEBLDS', 'informea'); ?></a></li>
			<?php } ?>
			<?php if($expand == 'sendmail') { ?>
			<li><a class="tab<?php echo ($expand == 'sendmail')?'-active':''; ?>"  href=""><?php _e('Send Mail', 'informea'); ?> </a></li>
			<?php } ?>
		</ul>
	</div>
	<div class="tab-content">
	<?php
		if($expand == 'membership') {
			include(dirname(__FILE__) . '/profile.tab.membership.php');
		}
		if($expand == 'nfp') {
			include(dirname(__FILE__) . '/profile.tab.nfp.php');
		}
		if($expand == 'reports') {
			include(dirname(__FILE__) . '/profile.tab.reports.php');
		}
		if($expand == 'plans') {
			include(dirname(__FILE__) . '/profile.tab.national_plans.php');
		}
		if($expand == 'peblds') {
			include(dirname(__FILE__) . '/profile.tab.peblds.php');
		}
		if($expand == 'map') {
			include(dirname(__FILE__) . '/profile.tab.map.php');
		}
		if($expand == 'ecolex/legislation' || $expand == 'ecolex/caselaw') {
			include(dirname(__FILE__) . '/profile.tab.ecolex.php');
		}
		if($expand == 'sendmail') {
			include(dirname(__FILE__) . '/../sendmail/inc_sendmail.php');
		}
	?>
	</div>
<?php } ?>
</div>

<div class="left details-column-3">
<?php
	if(count($ramsarSites)) {
?>
	<div class="related-box">
		<div class="related-box-title">
			<div class="left">
				<h3><?php _e('Ramsar sites', 'informea'); ?></h3>
			</div>
			<div class="clear"></div>
		</div>
		<div class="related-box-content">
			<ul class="related-links">
			<?php
				$count = 0;
				foreach($ramsarSites as $site) {
					$id = str_replace('ramsar-', '', $site->original_id);
			?>
				<li class="<?php echo ($count % 2) ? 'even' : 'odd' ;?>">
				<?php if($site->url !== NULL) { ?>
					<a href="javascript:void(0);" onclick="openRamsarSite('<?php echo $id; ?>');"><?php echo $site->name; ?></a>
				<?php } else { ?>
				<?php } ?>
				</li>
			<?php
					$count++;
				}
			?>
			</ul>
		</div>
	</div>
	<br />
<?php
	}
	$whc_sites = $page_data->get_whc_sites();
	if(count($whc_sites)) {
?>
	<div class="related-box">
		<div class="related-box-title">
			<h3><?php _e('WHC sites', 'informea'); ?></h3>
			<div class="clear"></div>
		</div>
		<div class="related-box-content">
			<ul class="related-links">
			<?php
				$count = 0;
				foreach($whc_sites as $site) {
			?>
				<li class="<?php echo ($count % 2) ? 'even' : 'odd' ;?>">
				<?php if($site->url !== NULL) { ?>
					<a target="_blank" href="<?php echo $site->url; ?>"><?php echo $site->name; ?></a>
				<?php } else { ?>
				<?php } ?>
				</li>
			<?php
					$count++;
				}
			?>
			</ul>
		</div>
	</div>
	<br />
<?php
	}
	$events = $page_data->get_related_events();
	if(count($events)) {
?>
	<div class="related-box">
		<div class="related-box-title">
			<h3><?php _e('Upcoming Events', 'informea'); ?></h3>
		</div>
		<div class="related-box-content">
			<ul class="related-links">
			<?php
				$count = 0;
				foreach($events as $event) { ?>
				<li class="<?php echo ($count % 2 == 0) ? 'odd' : 'even'; ?><?php echo ($event->title != '') ? ' tooltip' : ''; ?>" title="<?php echo $event->title; ?>">
				<strong><?php echo $event->short_title; ?></strong> :
				<?php if($event->event_url !== NULL) { ?>
					<a target="_blank" href="<?php echo $event->event_url; ?>"><?php echo subwords($event->title, 10); ?></a>
				<?php } else { ?>
					<?php echo subwords($event->title); ?>
				<?php } ?>
				<br />
				<?php echo show_event_interval($event); ?> <?php echo ($event->location !== NULL) ? $event->location : ''; ?> <?php echo ($event->city !== NULL) ? $event->city : ''; ?>
				</li>
			<?php
					$count++;
				}
			?>
			</ul>
		</div>
	</div>
<?php
	}
	if($country->id == 184) { // European Union - show parties
		include(dirname(__FILE__) . '/portlet.eu_countries.php');
	}
?>
</div>
