<?php
/**
 * Template Name: Informea download things
 * No template
 *
 * @package WordPress
 * @subpackage InforMEA
 * @since InforMEA 1.0
 */
global $wpdb;
$entity = get_request_value('entity');
if('decision_document' == $entity) {
	$id = get_request_int('id');
	$row = $wpdb->get_row("SELECT url, path FROM ai_document WHERE id = $id");
	$remote_url = $row->url;
	$handle = curl_init($remote_url);
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($handle,  CURLOPT_TIMEOUT, 5);
	// $response = curl_exec($handle); // TODO: Not needed - workaround POPS .NETNUKE that returns 200 when 404/500
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	$error = false;
	if($httpCode == 200) {
		$error = false;
		// if (strpos('An error has occurred', $response) >= 0) {  // TODO: Not needed - workaround POPS .NETNUKE that returns 200 when 404/500
		//	$error = true;
		// }
	} else {
		$error = true;
	}
	if($error) {
		$local_url = get_bloginfo('url') . '/' . $row->path;
		header("Location: $local_url");
	} else {
		header("Location: $remote_url");
	}
}

if('terms_csv' == $entity) {
	include(dirname(__FILE__) . '/imea_pages/terms/download.csv.php');
}
if('countries_csv' == $entity) {
	include(dirname(__FILE__) . '/imea_pages/countries/download.csv.php');
}
?>
