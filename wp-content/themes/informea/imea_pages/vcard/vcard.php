<?php
	$id_contact = get_request_int('id_contact',-1);
	if ($id_contact == -1) {
		return;
	}
	$page_data = new imea_page_base_page(array());
	$contact = $page_data->get_contact_for_id($id_contact);
	if($contact != null) {
		header("Content-type: text/x-vcard; charset=utf-8");
		header("Content-Disposition: filename=\"" . $contact->first_name . " " . $contact->last_name . ".vcf\"");
		echo "BEGIN:VCARD\n";
		echo "VERSION:2.1\n";
		echo "N:" . $contact->last_name . ";" . $contact->first_name . ";;" . $contact->prefix . "\n";
		echo "FN:" . $contact->first_name . " " . $contact->last_name . "\n";
		echo "ORG:" . $contact->institution . ";" . $contact->department . "\n";
		echo "TITLE:" . $contact->position . "\n";
		echo "TEL;WORK;VOICE:" . $contact->telephone . "\n";
		echo "TEL;WORK;FAX:" . $contact->fax . "\n";
		echo "EMAIL;PREF;INTERNET:" . $contact->email . "\n";
		echo "ADR;WORK:;;" . str_replace("\r\n"," ",$contact->address) . ";;;;" . $contact->country_name . "\n";
		echo "END:VCARD\n";
	}
?>
