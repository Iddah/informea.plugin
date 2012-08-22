<?php
	$url = urldecode(get_request_value('next'));
	if(empty($url)) {
		$url = 'http://www.ecolex.org/ecolex/ledge/view/SearchResults?screen=Literature&index=literature&sortField=searchDate&keyword=' . str_replace(' ', '%20', $term->term);
	}
	$p = new EcolexParser($url, get_bloginfo('url') . "/terms/{$term->id}/ecolex");
?>
<p class="ecolex-disclaimer">
	<img src="http://informea.org/wp-content/uploads/ecolex_header_terms.png" class="middle" />
	The content of this this area is exclusively provided by <a target="_blank" href="<?php echo $url; ?>">Ecolex</a> - the gateway to environmental law, which is a collaboration of IUCN, FAO and UNEP
</p>
<?php echo $p->get_content(); ?>
