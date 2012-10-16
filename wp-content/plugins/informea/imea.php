<?php
/*
Plugin Name: InforMEA
Plugin URI: http://informea.org/
Description: This is the plugin that implements the InforMEA project search interface.
Version: 0.1
Author: cristiroma
Author URI: http://eaudeweb.ro
License: GPL2
*/

/* Copyright 2011 Eau De Web  (email : office@eaudeweb.ro)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('INFORMEA_VERSION', '0.1');

if (!function_exists('add_action')) {
	// Silence is golden
	exit;
}

include_once ("recaptchalib.php");
include_once ("imea.functions.php");
include_once ("imea.class.php");
include_once ("imea.admin.class.php");
include_once ("imea.localization.php");
include_once ("pages/index.class.php");
include_once ("pages/countries.class.php");
include_once ("pages/treaties.class.php");
include_once ("pages/decisions.class.php");
include_once ("pages/events.class.php");
include_once ("pages/highlights.class.php");

include_once ("search/InformeaSearch2.php");
include_once ("search/AbstractSearch.php");
include_once ("search/CacheManager.php");
include_once ("search/InformeaSearchRenderer.php");
include_once ("search/InformeaSearch3.php");

include_once ("rss.php");

register_activation_hook(__FILE__, array('imeasite', 'install'));
register_uninstall_hook(__FILE__, array('imeasite', 'uninstall' ));

?>
