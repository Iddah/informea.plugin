<?php
// Template engine functions
function tengine_head() {
	$imea_options = get_option('informea_options');
	$mobile = new Mobile_Detect();
?>
	<?php
		if(!empty($imea_options['css_optimizer']) && $imea_options['css_optimizer'] == true) {
	?><link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_directory'); ?>/style_min.css" />
	<?php
		} else {
	?><link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo( 'template_directory' ); ?>/style.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo( 'template_directory' ); ?>/tipsy.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo( 'template_directory' ); ?>/bubble.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo( 'template_directory' ); ?>/ui.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_directory'); ?>/bubbletip.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_directory'); ?>/boxy.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo( 'template_directory' ); ?>/feedback.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_directory'); ?>/slider.css" />
	<?php
	}
	?><!--[if IE]>
	<link href="<?php bloginfo('template_directory'); ?>/bubbletip-IE.css" rel="stylesheet" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE 7]>
	<link href="<?php bloginfo('template_directory'); ?>/fix-IE7.css" rel="stylesheet" type="text/css" media="screen" />
	<![endif]-->
	<!--[if gte IE 8]>
	<link href="<?php bloginfo('template_directory'); ?>/fix-IE8.css" rel="stylesheet" type="text/css" media="screen" />
	<![endif]-->
	<?php
		// Execute css_inject to give pages the chance to add CSS code in html header of the page
		do_action('css_inject');
	?><link rel="stylesheet" type="text/css" media="print" href="<?php bloginfo('template_directory'); ?>/print.css" />
	<link media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" href="<?php bloginfo('template_directory'); ?>/handheld.css" type="text/css" rel="stylesheet" />
	<?php if($mobile->isMobile()) { ?>
	<!--<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_directory'); ?>/handheld.css" />-->
	<?php } ?>
<?php
}
