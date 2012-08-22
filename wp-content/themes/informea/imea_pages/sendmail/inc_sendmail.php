<link rel="stylesheet" type="text/css" media="print" href="<?php echo bloginfo('url'); ?>/wp-content/themes/informea/print.css" />

<script type="text/javascript">
var RecaptchaOptions = { theme : 'clean' };

function go_back() {
	back_url = document.URL + "/../../../nfp#contact-bookmark-<?php echo $id_parent?>";
	document.location = back_url;
}

function submit_form() {
	isError = 0;
	if (jQuery.trim($('#firstname').val()) == '') {
		isError = 1;
		$('#firstname_error').show();
		$('#firstname_label').css({'color':'red','font-weight':'bold'});
	} else{
		$('#firstname_error').hide();
		$('#firstname_label').css({'color':'#666666','font-weight':'normal'});
	}
	if (jQuery.trim($('#lastname').val()) == '') {
		isError = 1;
		$('#lastname_error').show();
		$('#lastname_label').css({'color':'red','font-weight':'bold'});
	} else {
		$('#lastname_error').hide();
		$('#lastname_label').css({'color':'#666666','font-weight':'normal'});
	}
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if(reg.test($('#email').val()) == false) {
		isError = 1;
		$('#email_error').show();
		$('#email_label').css({'color':'red','font-weight':'bold'});
	} else {
		$('#email_error').hide();
		$('#email_label').css({'color':'#666666','font-weight':'normal'});
	}
	if (jQuery.trim($('#message').val()) == '') {
		isError = 1;
		$('#message_error').show();
		$('#message_label').css({'color':'red','font-weight':'bold'});
	} else {
		$('#message_error').hide();
		$('#message_label').css({'color':'#666666','font-weight':'normal'});
	}

	if (isError == 0) {
		dataJSON = {
			recaptcha_challenge_field:($('#recaptcha_challenge_field').val()),
			recaptcha_response_field:($('#recaptcha_response_field').val()),
			firstname:$('#firstname').val(),
			lastname:$('#lastname').val(),
			email:$('#email').val(),
			message:$('#message').val(),
			salutation:$('#salutation').val(),
			contact:$('#contact_id').val(),
			needcopy:$("#needcopy").attr('checked')
		};

		$('#sendmailbtn').hide();
		$('#backbtn').hide();
		$('#mailstatus').html("<?php echo _e('Sending... Please wait!', 'informea'); ?>");
		$('#mailstatus').css({'color':'#666666','font-weight':'normal'});
		$('#mailstatus').show();
		$.ajax({
			type: "POST",
			url: "/sendmail_ajax",
			dataType: "json",
			data: dataJSON,
			success: function(data) {
				if (data.error){
					$('#sendmailbtn').show();
					$('#backbtn').show();
					$('#mailstatus').css({'color':'red','font-weight':'bold'});
					$('#mailstatus').html(data.msg);
				}
				else{
					$('#recaptcha_widget_div').hide()
					$('#backbtn').show();
					$('#mailstatus').html(data.msg);
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {}
		});
	}
}
</script>
<form name="contact-form" action="">
	<input type="hidden" id="contact_id" value="<?php echo $id_contact?>" />
	<div style="margin: 1em 0 0.5em 0; color:#666666">
	<div class="contact-name">
		<?php echo $contact->prefix; ?> <?php echo $contact->first_name; ?> <?php echo $contact->last_name; ?>
	</div>
	<?php if($contact->position !== NULL) { ?>
		<em><?php echo $contact->position; ?></em>
		<br />
	<?php } ?>
	<?php if($contact->department !== NULL) { ?>
		<em><?php echo $contact->department; ?></em>
		<br />
	<?php } ?>
	<?php if($contact->institution !== NULL) { ?>
		<?php echo $contact->institution; ?>
		<br />
	<?php } ?>
	<?php if($contact->address !== NULL) { ?>
	<div class="contact-address">
		<strong><?php _e('Address:', 'informea'); ?></strong> <?php echo replace_enter_br($contact->address); ?>
	</div>
	<?php } ?>

	<?php if($contact->telephone !== NULL) { ?>
	<div>
		<strong><?php _e('Phone:', 'informea'); ?></strong> <?php echo $contact->telephone; ?>
	</div>
	<?php } ?>

	<?php if($contact->fax !== NULL) { ?>
	<div>
		<strong><?php _e('Fax:', 'informea'); ?></strong> <?php echo $contact->fax; ?>
	</div>
	<?php } ?>
	</div>

	<div id="error-zone" style="color:red;">
		<div style="display:none" id="firstname_error"> <b><?php echo _e('First Name is required', 'informea'); ?></b><br/> </div>
		<div style="display:none" id="lastname_error"> <b><?php echo _e('Last Name is required', 'informea'); ?></b><br/> </div>
		<div style="display:none" id="email_error"> <b><?php echo _e('A Valid E-Mail Address is required', 'informea'); ?></b><br/> </div>
		<div style="display:none" id="message_error"> <b><?php echo _e('Message is required', 'informea'); ?></b><br/> </div>
	</div>
	<span style="width:100px; display:block;float:left"><?php echo _e('Salutation', 'informea'); ?></span>
	<select id="salutation">
		<option><?php echo _e('Mr.', 'informea'); ?></option>
		<option><?php echo _e('Mrs.', 'informea'); ?></option>
	</select><br/>
	<span id="firstname_label" style="width:100px; display:block;float:left"><?php echo _e('First Name', 'informea'); ?></span>
	<input id="firstname" type="text" style="width:75%;float:left;"/>
	<br/>

	<div class="clear"></div>
	<span id="lastname_label" style="width:100px; display:block;float:left"><?php echo _e('Last Name', 'informea'); ?></span>
	<input id="lastname" type="text" style="width:75%;float:left;"/>
	<br/>

	<div class="clear"></div>
	<span id="email_label" style="width:100px; display:block;float:left"><?php echo _e('E-Mail Address', 'informea'); ?></span>
	<input id="email" type="text" style="width:75%;float:left;"/>
	<br/>

	<div class="clear"></div>
	<span id="message_label" style="width:100px; display:block;float:left"><?php echo _e('Message', 'informea'); ?></span>
	<textarea id="message" rows="10" style="width:75%"> </textarea><br/>
	<div class="clear"></div>

	<span>
		<label for="needcopy">I want to receive a copy of this email</label>
	</span>
	<input id="needcopy" type="checkbox" value="true" />
	<br/>

<?php
	$error = null;
	$options = get_option('informea_options');
	$public_key = $options['recaptcha_public'];
	echo recaptcha_get_html($public_key, $error);
?>
	<div id="mailstatus" style="display:none"></div>
	<input id="sendmailbtn" type="button" value="<?php echo _e('Send Email', 'informea'); ?>" onclick="submit_form();"/>
	<input id="backbtn" type="button" value="<?php echo _e('Back', 'informea'); ?>" onclick="go_back()"/>
</form>
