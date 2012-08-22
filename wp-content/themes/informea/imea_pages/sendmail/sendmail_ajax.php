<?php
	$return['error'] = false;
	$return['msg'] = __('The e-mail was sent. If you selected, soon you should receive a copy of the e-mail.', 'informea');
	$options = get_option('informea_options');

	$private_key = $options['recaptcha_private'];
	$resp = recaptcha_check_answer($private_key, $_SERVER["REMOTE_ADDR"],
					get_request_value("recaptcha_challenge_field"),
					get_request_value("recaptcha_response_field")
			);

	if (!$resp->is_valid) {
		$return['error'] = true;
		$return['msg'] = __('Incorrect spam verification keywords. Please try again or refresh the picture', 'informea');
	} else {
		$page_data = new imea_page_base_page(array());
		$contact = $page_data->get_contact_for_id(get_request_value('contact', -1));

		$to = $contact->email;
		$subject = __("Contact mail send from InforMEA portal", 'informea');
		$body = get_request_value('message');
		$fullbody = "$body \n\n\n" . __('This email was automatically send by the InforMEA portal (http://www.informea.org) because a person requested to contact you via our portal', 'informea');

		$salutation = get_request_value('salutation');
		$first_name = get_request_value('firstname');
		$last_name = get_request_value('lastname');
		$email = get_request_value('email');

		$header = "From: $salutation $first_name $last_name <$email>";
		if(mail($to, $subject, $fullbody, $header)) {
			if(get_request_value('needcopy') == 'true') {
				$contact_prefix = $contact->prefix;
				$contact_first_name = $contact->first_name;
				$contact_last_name = $contact->last_name;

				$dest = $to;
				$to = get_request_value('email');
				$subject = __("Copy of Contact mail send from InforMEA portal", 'informea');
				$fullbody = "This email was automatically send by the InforMEA portal (http://www.informea.org) because you requested to contact $contact_prefix $contact_first_name $contact_last_name <$dest> via our portal. \n\n Your message was: $body";
				if(!mail($to, $subject, $fullbody, $header)) {
					$return['error'] = false;
					$return['msg'] = __("Mail was successfully sent, but we couldn't send you the copy of the email.", 'informea');
				}
			}
		} else {
			$return['error'] = true;
			$return['msg'] = __("Sorry, we couldn't send the email. Please try again later, or contact our technical support", 'informea');
		}
	}
	echo json_encode($return);
?>
