<?php
if(!class_exists('imeasite')) {
/**
 * Plugin class.
 */
class imeasite {
	function install() {
		imea_log("Activating plugin ...");
	}

	/**
	 * Uninstall the plugin, at the moment doesn't seem to work the Wordpress itself.
	 * Throws error while trying to uninstall (FTP page).
	 * Use uninstall.php method.
	 */
	function uninstall() {
		imea_log("WARN! Uninstalling plugin is *NOT IMPLEMENTED* -> Use uninstall.php");
	}

	function add_home_image_link($nav) {
		$base_url = get_bloginfo('url');
		$theme_url = get_bloginfo('template_directory');
		return "<li><a href='" . $base_url . "' class='home'>Home</a></li>" . $nav;
	}

	/**
	 * Fill the breadcrumb for exceptional pages (not treaties/decisions/countries/terms/events - handled by their respective classes).
	 * 'index' post_name has no need of breadcrumbtrail.
	 */
	function breadcrumbtrail() {
		global $post;
		if($post) {
			if(!in_array($post->post_name, array('index', 'treaties', 'decisions', 'print', 'countries', 'terms', 'events'))) {
				if($post->post_name == 'about') {
					return " &raquo; <span class='current'>{$post->post_title}</span>";
				} else if($post->post_name == 'disclaimer') {
					return " &raquo; <span class='current'>{$post->post_title}</span>";
				} else if($post->post_name == 'newsletter') {
					return " &raquo; <span class='current'>{$post->post_title}</span>";
				} else if($post->post_name == 'search') {
					return " &raquo; MEA Explorer &raquo; <span class='current'>Search results</span>";
				} else { // This is for highlights that have an variable post title.
					return ' &raquo; <a href="/highlights"><span class="current">' . __('Highlights') . '</span></a> &raquo; <span class="current">' . __('Article') . '</span>';
				}
			}
		} else { // Error page seem not to have $post
			return ' &raquo; <span class="current">' . __('Error') . '</span>';
		}
	}


	/** Rewrite the permalinks so we can have pretty URLS when browsing portal for treaties/decisions etc */
	function create_rewrite_rules($rules) {
		$imea_rules = array();

		$imea_rules['(ro|es|fr)?/?rss?'] = 'index.php?pagename=rss-feeds&lng=$matches[1]'; // http://informea/rss

		$imea_rules['(ro|es|fr)?/?events/(list|calendar)?$'] = 'index.php?pagename=events&expand=$matches[2]&lng=$matches[1]'; // http://informea/events/list
		$imea_rules['(ro|es|fr)?/?events/rss?'] = 'index.php?pagename=events&show_rss=True&lng=$matches[1]'; // http://informea/events/rss

		$imea_rules['(ro|es|fr)/(about|treaties|decisions|countries|terms|events)/?$'] = 'index.php?pagename=$matches[2]&lng=$matches[1]'; // Pages L2

		$imea_rules['(ro|es|fr)?/?countries/(grid|alphabetical|map)?$'] = 'index.php?pagename=countries&mode=$matches[2]&lng=$matches[1]'; // http://informea/countries/grid
		$imea_rules['(ro|es|fr)?/?countries/(\d*)/?(membership|nfp|reports|map|plans|peblds|ecolex/legislation|ecolex/caselaw)?$'] = 'index.php?pagename=countries&id_country=$matches[2]&expand=$matches[3]&lng=$matches[1]'; // http://informea/countries/id/nfp
		$imea_rules['(ro|es|fr)?/?countries/(\d*)/?(sendmail)?/?(\d*)?/?(\d*)?$'] = 'index.php?pagename=countries&id_country=$matches[2]&expand=$matches[3]&id_contact=$matches[4]&id_parent=$matches[5]&lng=$matches[1]'; // http://informea/countries/id/sendmail/1234/1


		// Preserve line below for backward-compatibility with old URLs without tabs
		$imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(sendmail)?/?(\d*)?/?(\d*)?$'] = 'index.php?pagename=treaties&id_treaty=$matches[2]&expand=$matches[3]&id_contact=$matches[4]&id_parent=$matches[5]&lng=$matches[1]'; // http://informea/treaties/id/sendmail/1234/1
		$imea_rules['(ro|es|fr)?/?treaties/(.*)/sendmail/(\d*)/(\d*)?$'] = 'index.php?pagename=treaties&treaty=$matches[2]&expand=sendmail&id_contact=$matches[3]&id_parent=$matches[4]&lng=$matches[1]'; // http://informea/treaties/odata_name/sendmail/1234/1
		$imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(\d*)?$'] = 'index.php?pagename=treaties&id_treaty=$matches[2]&id_treaty_article=$matches[3]&lng=$matches[1]'; // http://informea/treaties/id/id_treaty_article#article_id_paragraph_id
		$imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(treaty|decisions|print|nfp|coverage)?/?(showall)?$'] = 'index.php?pagename=treaties&id_treaty=$matches[2]&expand=$matches[3]&showall=$matches[4]&lng=$matches[1]'; // http://informea/treaties/id/decisions/showall#decisions-id

		//URL: /treaties/region/global, /treaties/region/Europe ...
		$imea_rules['(ro|es|fr)?/?treaties/region/(.*)/icon$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=icon&lng=$matches[1]'; // /icon suffix
		$imea_rules['(ro|es|fr)?/?treaties/region/(.*)/grid$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=grid&lng=$matches[1]'; // /grid suffix
		$imea_rules['(ro|es|fr)?/?treaties/region/(.*)$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=icon&lng=$matches[1]'; // no suffix

		$imea_rules['(ro|es|fr)?/?treaties/(.*)/(general|treaty|decisions|print|nfp|coverage)?/?(showall)?$'] = 'index.php?pagename=treaties&treaty=$matches[2]&expand=$matches[3]&showall=$matches[4]&lng=$matches[1]'; // http://informea/treaties/id/decisions/showall#decisions-id
		$imea_rules['(ro|es|fr)?/?treaties/(.*)?$'] = 'index.php?pagename=treaties&treaty=$matches[2]&lng=$matches[1]'; // http://informea/treaties/aewa - identify by slug (we use odata_name)

		$imea_rules['(ro|es|fr)?/?decisions/treaty/?$'] = 'index.php?pagename=decisions&expand=treaty&lng=$matches[1]'; // http://informea/decisions/treaty/
		$imea_rules['(ro|es|fr)?/?decisions/term/?$'] = 'index.php?pagename=decisions&expand=term&lng=$matches[1]'; // http://informea/decisions/term/

		$imea_rules['(ro|es|fr)?/?terms/(theme|alphabet)?$'] = 'index.php?pagename=terms&expand=$matches[2]&lng=$matches[1]'; // http://informea/terms/theme
		$imea_rules['(ro|es|fr)?/?terms/(\d*)/?(treaties|decisions|ecolex)?$'] = 'index.php?pagename=terms&id_term=$matches[2]&tab=$matches[3]&lng=$matches[1]'; // http://informea/terms/id/treaties

		$imea_rules['(ro|es|fr)?/?highlights/rss?'] = 'index.php?pagename=highlights&show_rss=True&lng=$matches[1]'; // http://informea/highlights/rss
		$imea_rules['(ro|es|fr)?/?highlights/(.*)/(\d*)$'] = 'index.php?pagename=highlights&highlight_category=$matches[2]&page=$matches[3]&lng=$matches[1]'; // http://informea/highlights/category_name & http://informea/highlights/category_name/page (pagination)
		$imea_rules['(ro|es|fr)?/?highlights/(.*)$'] = 'index.php?pagename=highlights&highlight_category=$matches[2]&lng=$matches[1]'; // http://informea/highlights/category_name & http://informea/highlights/category_name

		//var_dump($rules);
		return $imea_rules + $rules;
	}

	/** Append custom parameters to Wordpress list of known query variables ($wp_rewrite->query_vars) */
	function query_vars($vars) {
		$vars[] = 'action';
		$vars[] = 'treaty';
		$vars[] = 'id_treaty';
		$vars[] = 'id_treaty_article';
		$vars[] = 'expand';
		$vars[] = 'tab';
		$vars[] = 'id_term';
		$vars[] = 'id_country';
		$vars[] = 'showall';

		$vars[] = 'mode';

		$vars[] = 's_column';
		$vars[] = 's_order';

		$vars[] = 'lng';
		$vars[] = 'pagename';
		$vars[] = 'id_contact';
		$vars[] = 'id_parent';

		$vars[] = 'highlight_category';
		$vars[] = 'highlight_id';

		$vars[] = 'show_rss';
		$vars[] = 'category';
		return $vars;
	}

	function custom_body_class($classes) {
		global $wp_query;
		if(is_page()) {
			$page_id = $wp_query->get_queried_object_id();
			$post = get_page($page_id);
			$classes[] = 'imea-' . $post->post_name;
		}
		return $classes;
	}
}

$mobile_detect = new Mobile_Detect();
function serve_mobile($theme) {
	global $mobile_detect;

	//return 'mobile';
	// return $theme;
	// TODO - enable this when we have special theme
	//if($mobile_detect->isMobile()) {
	//	$theme = 'mobile';
	//}
	return $theme;
}

if(!$mobile_detect->isMobile()) {
	/** Add filter to intercept menu creation and inject the Home link */
	add_filter('wp_nav_menu_items', array('imeasite', 'add_home_image_link'));
}

/** Add filter to intercept breadcrumbtrail and handle special pages (About/News) to correctly set breacrumbtrail */
add_filter('breadcrumbtrail', array('imeasite', 'breadcrumbtrail'));

/** Rewrite URLs for custom INFORMEA permalinks */
add_filter('query_vars', array('imeasite', 'query_vars'));
add_filter('rewrite_rules_array', array('imeasite', 'create_rewrite_rules'));
add_filter('body_class', array('imeasite', 'custom_body_class'));

/* Filters for mobile devices */
add_filter('template', 'serve_mobile');
add_filter('option_template', 'serve_mobile');
add_filter('option_stylesheet', 'serve_mobile');

}
