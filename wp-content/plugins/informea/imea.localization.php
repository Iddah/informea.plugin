<?php

$imea_language = 'en';
$imea_language_set = FALSE;
$imea_domain_loaded = FALSE;

function set_language() {
	global $imea_language, $imea_language_set;
	if(!$imea_language_set) {
		$imea_language = get_request_variable('lng', 'str', 'en');
		$imea_language_set = TRUE;
	}
}

function get_imea_url($url_suffix = '') {
	global $imea_language;
	if(strpos($url_suffix, 'javascript:') !== false) {
		return $url_suffix;
	} else {
		set_language();
		$url = get_bloginfo('url');
		if($imea_language !== 'en') {
			$url .= '/' . $imea_language;
		}
		if(!empty($url_suffix)) {
			$url .= '/' . $url_suffix;
		}
		return $url;
	}
}


function imea_url($url_suffix = '') {
	echo get_imea_url($url_suffix);
}

function get_imea_anchor($args) {
	$ret = "<a";
	if(isset($args['title'])) {
		$ret .= ' title="' . $args['title'] . '"';
	}
	$css_classes = '';
	if(isset($args['css'])) {
		$css_classes = $args['css'];
	}
	if(isset($args['css_cb'])) {
		$callback = $args['css_cb'];
		$css_classes .= $callback();
	}
	if(!empty($css_classes)) {
		$ret .= ' class="' . $css_classes . '"';
	}
	$href = isset($args['href']) ? get_imea_url($args['href']) : get_imea_url();
	$label = isset($args['label']) ? $args['label'] : '';
	$ret .= ' href="' . $href . '">' . $label . '</a>';
	return $ret;
}

function imea_anchor($args) {
	echo get_imea_anchor($args);
}

function language_link($lng, $title, $echo = true) {
	global $imea_language;
	set_language();
	if($lng == $imea_language) {
		echo "<strong>$title</strong>";
	} else {
		// Rebuild the request with language prefix URL
		$url = 'http://' . $_SERVER['HTTP_HOST'];
		if($lng !== 'en') {
			$url .= "/$lng";
		}
		// Remove language if exists from REQUEST_URI
		$req_uri = $_SERVER['REQUEST_URI'];
		$req_uri = str_replace( array('/ro', '/fr', '/es'), '', $req_uri);
		$req_uri = str_replace('/fr', '', $req_uri);
		$req_uri = str_replace('/es', '', $req_uri);
		$url .= $req_uri;
		//echo "<a href='$url'>$title</a>";
		if($echo) {
			echo _e('<a href="javascript:void(0);" title="Switch to ' . $title . '">' . $title . '</a>');
		} else {
			return _e('<a href="javascript:void(0);" title="Switch to ' . $title . '">' . $title . '</a>');
		}
	}
}

function imea_gettext($translation, $text = null, $domain = null) {
	global $imea_language, $imea_language_set, $imea_domain_loaded;
	if($imea_language !== NULL && $imea_language != 'en' && $domain == 'informea') {

		if(!$imea_domain_loaded) {
			$mo_path = get_template_directory() . '/languages/informea-' . $imea_language . '.mo';
			load_textdomain('informea', $mo_path);
			$imea_domain_loaded = TRUE;
		}
		$translations = &get_translations_for_domain( $domain );
		return $translations->translate( $text );
	}
	return $translation;
}

add_filter('gettext', 'imea_gettext', 10, 3);


/** Fix the links in menu items URLs using custom walker */
if(!class_exists('imea_menu_walker')) {

class imea_menu_walker extends Walker_Nav_Menu {

	function start_el(&$output, $item, $depth, $args) {
		global $wp_query, $imea_language;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';

		// Localize the URL
		if($imea_language == 'en') {
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		} else {
			$url = $item->url;
			$b_url = 'http://' . $_SERVER['HTTP_HOST'] . '/';
			$url = str_replace( $b_url, $b_url . "$imea_language/", $url);
			$attributes .= ! empty( $url )        ? ' href="'   . esc_attr( $url        ) .'"' : '';
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', __($item->title, 'informea') , $item->ID ) . $args->link_after; // Informea: Localize the title of the page
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
}
