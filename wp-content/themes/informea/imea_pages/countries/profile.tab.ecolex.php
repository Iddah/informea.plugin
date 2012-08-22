<?php
	$url = urldecode(get_request_value('next'));
	if(empty($url)) {
		if($expand == 'ecolex/legislation') {
			$url = 'http://www.ecolex.org/ecolex/ledge/view/SearchResults?screen=Legislation&index=documents&sortField=searchDate&country=' . str_replace(' ', '%20', $country->name);
		} else {
			$url = 'http://www.ecolex.org/ecolex/ledge/view/SearchResults?screen=CourtDecisions&index=courtdecisions&sortField=searchDate&country=' . str_replace(' ', '%20', $country->name);
		}
	}
	$p = new EcolexParser($url, get_bloginfo('url') . "/countries/{$country->id}/" . $expand);
?>
<div class="tab-content">
	<div class="ecolex-disclaimer">
		<img src="http://informea.org/wp-content/uploads/ecolex_header.png" />
		The content of this this area is exclusively provided by <a target="_blank" href="<?php echo $url; ?>">Ecolex</a> - the gateway to environmental law,
		which is a collaboration of IUCN, FAO and UNEP
	</div>
	<?php echo $p->get_content(); ?>
</div>
