<?php
	$ob = new imea_index_page();
	$feedback = $ob->process_feedback();
?>
<?php
	function inject_js_feedback() {
?>
<script>
	$(document).ready(function() { $('#feedback_recaptcha').hide(); });
</script>
<?php
	}
	add_action('js_inject', 'inject_js_feedback');
?>
<script>
	var RecaptchaOptions = { theme : 'clean' };
</script>
<div class="clear"></div>
<div class="feedback-form-holder">
	<form id="feedback-form" action="" method="post" onsubmit="return validate_feedback_form();">
		<?php wp_nonce_field('informea-feedback-form'); ?>
		<input type="hidden" name="submit-feedback" value="true" />
		<p class="box-title">
			<span><?php _e('Feedback &amp; suggestions', 'informea'); ?></span>
		</p>
		<br />
		<em>
			<?php _e('&mdash;We would appreciate very much your feedback and suggestions for improvement on website navigation, user friendliness and functionality.', 'informea'); ?>
		</em>
<?php
	if($feedback->actioned == TRUE) {
		if($feedback->success == TRUE) {
?>
			<div class="feedback-message"><?php echo $feedback->msg; ?></div>
<?php
		} else {
			if(count($feedback->errors)) {
?>
			<ul class="feedback-message">
<?php
				foreach($feedback->errors as $error) {
?>
				<li><?php echo $error; ?></li>
<?php
				}
?>
			</ul>
<?php
			}
		}
	}
?>
		<div class="clear"></div>
		<p>
			<label for="name"><?php _e('Name', 'informea'); ?> <img src="<?php bloginfo('template_directory'); ?>/images/feedback/required.gif" alt="*" title="<?php _e('Name is required', 'informea'); ?>" class="middle" /></label>
			<input type="text" name="feed_name" id="feedback_name" size="31" value="<?php echo $feedback->name; ?>" onclick="$('#feedback_recaptcha').show();" />
		</p>
		<p>
			<label for="email"><?php _e('Email', 'informea'); ?> <img src="<?php bloginfo('template_directory'); ?>/images/feedback/required.gif" alt="*" title="<?php _e('Email is required', 'informea'); ?>" class="middle" /></label>
			<input type="text" name="feed_email" id="feedback_email" size="31" value="<?php echo $feedback->email; ?>" onclick="$('#feedback_recaptcha').show();" />
		</p>
		<p>
			<label for="message"><?php _e('Message', 'informea'); ?> <img src="<?php bloginfo('template_directory'); ?>/images/feedback/required.gif" alt="*" title="<?php _e('Message is required', 'informea'); ?>" class="middle" /></label>
			<textarea name="feed_message" id="feedback_message" cols="30" rows="10" onclick="$('#feedback_recaptcha').show();"><?php echo $feedback->message; ?></textarea>
		</p>
		<div id="feedback_recaptcha">
			<label for="recaptcha_response_field">Spam <img src="<?php bloginfo('template_directory'); ?>/images/feedback/required.gif" alt="*" title="<?php _e('Name is required', 'informea'); ?>" class="middle" /></label>
		<?php
			$error = null;
			$options = get_option('informea_options');
			$public_key = $options['recaptcha_public'];
			echo recaptcha_get_html($public_key, $error);
		?>
		</div>
		<a class="button blue" href="javascript:void(0);" onclick="$('#feedback-form').submit();">
			<span><?php _e('Submit feedback', 'informea'); ?></span>
		</a>
		<div class="clear"></div>
	</form>
</div>
