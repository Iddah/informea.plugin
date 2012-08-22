<?php
if(!class_exists( 'imeasiteadmin')) {

require_once(dirname(__FILE__) . '/admin/database.php');
require_once(dirname(__FILE__) . '/admin/page_pictures_database.php');
require_once(dirname(__FILE__) . '/admin/reorder_treaty_paragraph.php');

/**
 * Class that manages the Administration functionality of the plugin (ex. Saving properties, creating menu entries etc.)
 */

$options_defaults = array (
		'debug' => false,
		'js_optimizer' => false,
		'css_optimizer' => false,
		'google_key' => '21090649-1',
		'recaptcha_public' => '6LekCMISAAAAALgx0VrB2RMB5xCPTk-YzKTfxwc0',
		'recaptcha_private' => '6LekCMISAAAAAHS0lh2LlH7_8vpwBjlDoJnXGu26',
		'solr_server' => '127.0.0.1',
		'solr_path' => '/informea-solr',
		'solr_port' => '8080',
		'mapserver_url' => 'http://informea.org/cgi-bin/mapserv',
		'mapserver_localmappath' => '/var/www/informea/wp-content/plugins/informea/gis/countries.map',
);


class imeasiteadmin {

	function init() {
		register_setting( 'informea_options', 'informea_options', array('imeasiteadmin', 'validate_options'));
		add_settings_section('informea_plugin_main', __('Main Settings'), array('imeasiteadmin', 'render_title_main'), 'informea');
		add_settings_section('informea_plugin_solr', __('Solr Configuration'), array('imeasiteadmin', 'render_title_solr'), 'informea');
		add_settings_section('informea_plugin_mapserver', __('MapServer Configuration'), array('imeasiteadmin', 'render_title_mapserver'), 'informea');
		add_settings_field('debug', __('Enable plugin debug'), array('imeasiteadmin', 'render_field_debug'), 'informea', 'informea_plugin_main');
		add_settings_field('js_optimizer', __('Use Optimized JavaScript (minified)'), array('imeasiteadmin', 'render_field_js_optimizer'), 'informea', 'informea_plugin_main');
		add_settings_field('css_optimizer', __('Use Optimized CSS (minified)'), array('imeasiteadmin', 'render_field_css_optimizer'), 'informea', 'informea_plugin_main');
		add_settings_field('google_key', __('Google Analytics key'), array('imeasiteadmin', 'render_field_google_key'), 'informea', 'informea_plugin_main');
		add_settings_field('recaptcha_public', __('reCaptcha Public Key'), array('imeasiteadmin', 'render_field_recaptcha_public'), 'informea', 'informea_plugin_main');
		add_settings_field('recaptcha_private', __('reCaptcha Private Key'), array('imeasiteadmin', 'render_field_recaptcha_private'), 'informea', 'informea_plugin_main');

		add_settings_field('solr_server', __('Server address'), array('imeasiteadmin', 'render_field_solr_server'), 'informea', 'informea_plugin_solr');
		add_settings_field('solr_path', __('URL'), array('imeasiteadmin', 'render_field_solr_path'), 'informea', 'informea_plugin_solr');
		add_settings_field('solr_port', __('TCP port'), array('imeasiteadmin', 'render_field_solr_port'), 'informea', 'informea_plugin_solr');

		add_settings_field('mapserver_url', __('Server address'), array('imeasiteadmin', 'render_field_mapserver_url'), 'informea', 'informea_plugin_mapserver');
		add_settings_field('mapserver_localmappath', __('Local path to map'), array('imeasiteadmin', 'render_field_mapserver_localmappath'), 'informea', 'informea_plugin_mapserver');

	}

	function menu() {
		// only administrators may change plugin setup
		add_menu_page(__('InforMEA'), __('InforMEA'), 'manage_options', 'informea');
		add_submenu_page('informea', __('Settings'), __('Global settings'), 'manage_options', 'informea', array('imeasiteadmin', 'page_settings'));
		// only administrators may see the activity log
		add_submenu_page('informea', __('InforMEA Activity'), __('Activity log'), 'manage_options', 'informea_activity_log', array('imeasiteadmin', 'page_activity_log'));

		// other pages
		add_submenu_page('informea', __('InforMEA Vocabulary'), __('Manage vocabulary'), 'publish_posts', 'thesaurus', array('ThesaurusAdmin', 'router'));
		add_submenu_page('informea', __('InforMEA Treaties'), __('Manage treaties'), 'publish_posts', 'informea_treaties', array('imeasiteadmin', 'page_treaties'));
		add_submenu_page('informea', __('InforMEA Decisions'), __('Manage decisions'), 'publish_posts', 'informea_decisions', array('imeasiteadmin', 'page_decisions'));
		add_submenu_page('informea', __('InforMEA Events'), __('Manage events'), 'publish_posts', 'informea_events', array('imeasiteadmin', 'page_events'));
		add_submenu_page('informea', __('InforMEA Pictures'), __('Manage pictures'), 'manage_options', 'informea_pictures', array('imeasiteadmin', 'page_pictures'));
		add_submenu_page('informea', __('InforMEA Focal points'), __('Manage focal points'), 'manage_options', 'informea_nfp', array('imeasiteadmin', 'page_nfp'));
		add_submenu_page('informea', __('InforMEA National plans'), __('Manage national plans'), 'manage_options', 'informea_national_plans', array('imeasiteadmin', 'informea_national_plans'));
		add_submenu_page('informea', __('InforMEA National reports'), __('Manage national reports'), 'manage_options', 'informea_national_reports', array('imeasiteadmin', 'informea_national_reports'));
		add_submenu_page('informea', __('InforMEA Miscellaneous'), __('Miscellaneous'), 'manage_options', 'informea_miscellaneous', array('imeasiteadmin', 'page_miscellaneous'));

	}

	function validate_options($options) {
		$clean = array();
		$clean['debug'] = (array_key_exists('debug', $options) and $options['debug'] == 'on') ? true : false;
		$clean['js_optimizer'] = (array_key_exists('js_optimizer', $options) and $options['js_optimizer'] == 'on') ? true : false;
		$clean['css_optimizer'] = (array_key_exists('css_optimizer', $options) and $options['css_optimizer'] == 'on') ? true : false;
		$clean['google_key'] = trim($options['google_key']);
		$clean['recaptcha_public'] = trim($options['recaptcha_public']);
		$clean['recaptcha_private'] = trim($options['recaptcha_private']);

		$clean['solr_server'] = trim($options['solr_server']);
		$clean['solr_path'] = trim($options['solr_path']);
		$clean['solr_port'] = trim($options['solr_port']);

		$clean['mapserver_url'] = trim($options['mapserver_url']);
		$clean['mapserver_localmappath'] = trim($options['mapserver_localmappath']);
		return $clean;
	}

	function render_title_main() {
		echo __('From this page you can configure the InforMEA parameters');
	}

	function render_title_solr() {
		echo __('Use the fields below to configure Solr search engine');
	}

	function render_title_mapserver() {
		echo __('Use the fields below to configure Mapserver');
	}

	function render_title_ai() {
		echo __('Enter the database connection parameters to the Analytical Index database');
	}

	function render_field_google_key() {
		imeasiteadmin::render_input_field('google_key');
	}

	function render_field_recaptcha_public() {
		imeasiteadmin::render_input_field('recaptcha_public');
	}

	function render_field_recaptcha_private() {
		imeasiteadmin::render_input_field('recaptcha_private');
	}

	function render_field_solr_server() {
		imeasiteadmin::render_input_field('solr_server');
	}

	function render_field_solr_path() {
		imeasiteadmin::render_input_field('solr_path');
	}

	function render_field_solr_port() {
		imeasiteadmin::render_input_field('solr_port');
	}

	function render_field_mapserver_url() {
		imeasiteadmin::render_input_field('mapserver_url');
	}
	function render_field_mapserver_localmappath() {
		imeasiteadmin::render_input_field('mapserver_localmappath');
	}

	function render_field_debug() {
		global $options_defaults;
		$options = get_option('informea_options', $options_defaults);
		$checked = !empty($options['debug']) && $options['debug'] == true ? "checked='checked'" : '';
		echo "<input id='debug' type='checkbox' name='informea_options[debug]' $checked />";
	}

	function render_field_js_optimizer() {
		global $options_defaults;
		$options = get_option('informea_options', $options_defaults);
		$checked = !empty($options['js_optimizer']) && $options['js_optimizer'] == true ? "checked='checked'" : '';
		echo "<input id='js_optimizer' type='checkbox' name='informea_options[js_optimizer]' $checked />";
	}

	function render_field_css_optimizer() {
		global $options_defaults;
		$options = get_option('informea_options', $options_defaults);
		$checked = !empty($options['css_optimizer']) && $options['css_optimizer'] == true ? "checked='checked'" : '';
		echo "<input id='css_optimizer' type='checkbox' name='informea_options[css_optimizer]' $checked />";
	}

	public static function render_input_field($key) {
		$value = imeasiteadmin::get_field_value($key);
		echo "<input id='$key' type='text' name='informea_options[$key]' size='60' type='text' value='$value' />";
	}

	public static function get_field_value($key) {
		global $options_defaults;
		$options = get_option('informea_options');
		if(!isset($options[$key])) {
			$value = $options_defaults[$key];
		} else {
			$value = $options[$key];
		}
		return $value;
	}


	function page_settings() {
		return include(dirname(__FILE__) . '/admin/page_settings.php');
	}

	function page_treaties() {
		$success = False;
		$page_data = new imea_admin_database();
		$act = get_request_value('act');
		$actioned = get_request_value('actioned');
		if($act == 'treaty_add_treaty') {
			$page_data = new imea_treaties_page();
			if ($actioned && $page_data->validate_treaty_add_treaty()) {
				$page_data->treaty_add_treaty();
			}
			return include(dirname(__FILE__) . '/admin/treaties/add_treaty.php');
		}
		if($act == 'treaty_edit_treaty') {
			$page_data = new imea_treaties_page();
			if ($actioned && $page_data->validate_treaty_edit_treaty()) {
				$page_data->treaty_edit_treaty();
			}
			return include(dirname(__FILE__) . '/admin/treaties/edit_treaty.php');
		}
		if($act == 'treaty_add_article') {
			$page_data = new imea_treaties_page();
			if ($actioned && $page_data->validate_treaty_add_article()) {
				$page_data->treaty_add_article();
			}
			return include(dirname(__FILE__) . '/admin/treaties/add_article.php');
		}
		if($act == 'treaty_edit_article') {
			if ($actioned && $page_data->validate_treaty_edit_article()) {
				$page_data->treaty_edit_article();
			}
			return include(dirname(__FILE__) . '/admin/treaties/edit_article.php');
		}
		if($act == 'treaty_delete_article') {
			if (get_request_value('delete') != '') {
				$id_article = get_request_value('id_article');
				$pd = new imea_treaties_page();
				$pd->delete_article($id_article);
			}
			return include(dirname(__FILE__) . '/admin/treaties/edit_article.php');
		}
		if($act == 'treaty_add_article_paragraph') {
			if ($actioned && $page_data->validate_treaty_add_article_paragraph()) {
				$page_data->treaty_add_article_paragraph();
			}
			return include(dirname(__FILE__) . '/admin/treaties/add_paragraph.php');
		}
		if($act == 'treaty_edit_article_paragraph') {
			if ($actioned && $page_data->validate_treaty_edit_article_paragraph()) {
				$page_data->treaty_edit_article_paragraph();
			}
			return include(dirname(__FILE__) . '/admin/treaties/edit_paragraph.php');
		}
		if($act == 'treaty_delete_paragraph') {
			if (get_request_value('delete') != '') {
				$id_paragraph = get_request_value('id_paragraph');
				$pd = new imea_treaties_page();
				$pd->delete_paragraph($id_paragraph);
			}
			return include(dirname(__FILE__) . '/admin/treaties/edit_paragraph.php');
		}
		if($act == 'treaty_add_organization') {
			$page_data = new imea_treaties_page();
			if ($actioned && $page_data->validate_organization()) {
				$page_data->add_organization();
			}
			return include(dirname(__FILE__) . '/admin/treaties/add_organization.php');
		}
		if($act == 'treaty_edit_organization') {
			$page_data = new imea_treaties_page();
			if ($actioned && $page_data->validate_organization(true)) {
				$page_data->edit_organization();
			}
			return include(dirname(__FILE__) . '/admin/treaties/edit_organization.php');
		}

		return include(dirname(__FILE__) . '/admin/treaties/index_treaty.php');
	}

	function page_decisions() {

		$success = False;
		$page_data = new imea_decisions_page(null);
		$actioned = get_request_boolean('actioned');
		$act = get_request_value('act');
		if($act == 'decision_edit_decision') {
			if ($actioned && $page_data->validate_edit_decision()) {
				$page_data->edit_decision();
			}
			return include(dirname(__FILE__) . '/admin/decisions/tag_decision_paragraph.php');
		}
		if($act == 'decision_edit_tags') {
			if ($actioned && $page_data->validate_edit_decision()) {
				$page_data->edit_decision();
			}
			return include(dirname(__FILE__) . '/admin/decisions/edit_tags.php');
		}
		if($act == 'decision_add') {
			if ($actioned && $page_data->validate_add_decision()) {
				$page_data->add_decision();
			}
			return include(dirname(__FILE__) . '/admin/decisions/add.php');
		}
		if($act == 'decision_delete') {
			if ($actioned && $page_data->validate_delete_decision()) {
				$page_data->delete_decision();
			}
			return include(dirname(__FILE__) . '/admin/decisions/delete.php');
		}
		return include(dirname(__FILE__) . '/admin/decisions/index_decisions.php');
	}

	function page_events() {
		$success = false;
		$actioned = get_request_value('actioned');
		$delete = get_request_boolean('delete');
		$page_data = new imea_events_page();
		$act = get_request_value('act');
		if($delete) {
			$page_data->delete_event();
			return include(dirname(__FILE__) . '/admin/events/edit_event.php');
		}
		if($act == 'event_add_event') {
			if ($actioned && $page_data->validate_event_add_event()) {
				$page_data->event_add_event();
			}
			return include(dirname(__FILE__) . '/admin/events/add_event.php');
		}
		if($act == 'event_edit_event') {
			if ($actioned && $page_data->validate_event_edit_event()) {
				$page_data->event_edit_event();
			}
			return include(dirname(__FILE__) . '/admin/events/edit_event.php');
		}
		return include(dirname(__FILE__) . '/admin/events/index_events.php');
	}

	function page_miscellaneous() {
		include_once(dirname(__FILE__) . '/admin/miscellaneous/miscellaneous.php');
		$act = get_request_value('act');
		$actioned = get_request_value('actioned');
		// $page_data = new imea_events_page();
		if($act == 'import_ramsar_csv') {
			$page_data = new ImportRamsarCSV();
			if($actioned == 'Test it') {
				$page_data->test();
			}
			if($actioned == 'Just do it!') {
				$page_data->import();
			}
			return include(dirname(__FILE__) . '/admin/miscellaneous/ramsar_sites.php');
		}
		if($act == 'import_whc_xml') {
			$page_data = new ImportWHCXML();
			if($actioned == 'Test it') {
				$page_data->test();
			}
			if($actioned == 'Just do it!') {
				$page_data->import();
			}
			return include(dirname(__FILE__) . '/admin/miscellaneous/whc_sites.php');
		}
		return include(dirname(__FILE__) . '/admin/page_miscellaneous.php');
	}

	function page_pictures() {
		$success = False;
		$page_data = new imea_admin_pictures();
		$act = get_request_value('act');
		$upload = get_request_value('upload');
		$delete = get_request_value('delete');
		if($act == 'pict_slider') {
			$upload_dir = ABSPATH . "wp-content/uploads/pictures/slide_images/";
			if(!empty($upload) && $page_data->validate_add_picture($upload_dir, 600, 200)) {
				$page_data->add_picture($upload_dir);
			}
			if(!empty($delete)) {
				$page_data->delete_pictures($upload_dir);
			}
			return include(dirname(__FILE__) . '/admin/page_pictures_slider.php');
		}
		return include(dirname(__FILE__) . '/admin/page_pictures.php');
	}

	function page_nfp() {
		$success = False;
		$actioned = get_request_value('actioned');
		$act = get_request_value('act');
		$page_data = new imea_countries_page();
		$results = array();
		if(get_request_boolean('search')) {
			$results = $page_data->nfp_search();
			return include(dirname(__FILE__) . '/admin/nfp/index_nfp.php');
		}
		if(get_request_boolean('delete')) {
			$page_data->nfp_delete();
			return include(dirname(__FILE__) . '/admin/nfp/index_nfp.php');
		}
		if($act == 'edit_nfp') {
			if ($actioned && $page_data->validate_nfp_edit()) {
				$page_data->nfp_edit();
			}
			return include(dirname(__FILE__) . '/admin/nfp/nfp_edit.php');
		}
		if($act == 'add_nfp') {
			if ($actioned && $page_data->validate_nfp_add()) {
				$page_data->nfp_add();
			}
			return include(dirname(__FILE__) . '/admin/nfp/nfp_add.php');
		}
		if($act == 'duplicates') {
			$duplicates = $page_data->nfp_duplicates();
			return include(dirname(__FILE__) . '/admin/nfp/duplicates.php');
		}
		return include(dirname(__FILE__) . '/admin/nfp/index_nfp.php');
	}


	function informea_national_plans() {
		$success = false;
		$actioned = get_request_value('actioned');
		$act = get_request_value('act');
		$page_data = new imea_countries_page();
		$results = array();
		if(get_request_boolean('delete')) {
			// $page_data->nfp_delete();
			// return include(dirname(__FILE__) . '/admin/nfp/index_nfp.php');
		}
		if($act == 'edit_national_plan') {
			if ($actioned && $page_data->validate_national_plan_edit()) {
				$page_data->national_plan_edit();
			}
			return include(dirname(__FILE__) . '/admin/national_plans/national_plan_edit.php');
		}
		if($act == 'add_national_plan') {
			if ($actioned && $page_data->validate_national_plan_add()) {
				$page_data->national_plan_add();
			}
			return include(dirname(__FILE__) . '/admin/national_plans/national_plan_add.php');
		}
		return include(dirname(__FILE__) . '/admin/national_plans/index_national_plans.php');
	}

	function informea_national_reports() {
		$success = false;
		$actioned = get_request_value('actioned');
		$act = get_request_value('act');
		$page_data = new imea_countries_page();
		$results = array();
		if(get_request_boolean('delete')) {
			// $page_data->nfp_delete();
			// return include(dirname(__FILE__) . '/admin/nfp/index_nfp.php');
		}
		if($act == 'edit_national_report') {
			if ($actioned && $page_data->validate_national_report_edit()) {
				$page_data->national_report_edit();
			}
			return include(dirname(__FILE__) . '/admin/national_reports/national_report_edit.php');
		}
		if($act == 'add_national_report') {
			if ($actioned && $page_data->validate_national_report_add()) {
				$page_data->national_report_add();
			}
			return include(dirname(__FILE__) . '/admin/national_reports/national_report_add.php');
		}
		return include(dirname(__FILE__) . '/admin/national_reports/index_national_reports.php');
	}

	function page_activity_log() {
		$page_data = new imea_admin_database();
		$limit = get_request_value('limit', 100);
		$order = get_request_value('order', 'rec_created');
		$ascension = strtolower(get_request_value('asc', 'DESC'));
		$ascension_rev = ($ascension == 'asc') ? 'desc' : 'asc';

		$records = $page_data->get_activity_log($order, $ascension, $limit);
		return include(dirname(__FILE__) . '/admin/page_activity_log.php');
	}
}
}
add_action('admin_menu', array('imeasiteadmin', 'menu'));
add_action('admin_init', array('imeasiteadmin', 'init'));

add_action('admin_print_scripts', function() {
	wp_enqueue_script('media-upload'); wp_enqueue_script('thickbox'); wp_enqueue_script('jquery');
});

add_action('admin_print_styles', function() {
	wp_enqueue_style('thickbox');
});

//}
?>
