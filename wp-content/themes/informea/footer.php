<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>

<?php
	$imea_options = get_option('informea_options');
	global $page_data;
	tengine_footer();

	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
		<?php
			if(!empty($imea_options['js_optimizer']) && $imea_options['js_optimizer'] == true) {
		?>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/script_min.js"></script>
		<?php
			} else {
		?>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/jquery-min.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/cookie.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/boxy.js"></script>
			<!-- script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/color.js"></script -->
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/hoverIntent.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/bgiframe.min.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/tipsy.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/bubbletip.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/scroll.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/functions.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/main.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/events.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/ui.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/imea_explorer.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/lof-slider.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/slider.js"></script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/vticker.js"></script>
		<?php
				// Slider controls in explorer - not implemented yet
				/* <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/slider.js"></script> */
			}
		?>
		<?php
		// Execute js_inject to give pages the chance to add JS code at the end of the page
		// such as in-place code, including custom scripts etc.
		do_action('js_inject');
?>
	<script type="text/javascript">

		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?php echo $imea_options["google_key"]; ?>']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();

/*
		$(function(){
			//demo 3
			$('select#valueAA, select#valueBB').selectToUISlider({
				labels: 12
			});
			//fix color
			fixToolTipColor();
		});
*/

		//purely for theme-switching demo... ignore this unless you're using a theme switcher
		//quick function for tooltip color match
		function fixToolTipColor() {
			//grab the bg color from the tooltip content - set top border of pointer to same
			$('.ui-tooltip-pointer-down-inner').each(function(){
				var bWidth = $('.ui-tooltip-pointer-down-inner').css('borderTopWidth');
				var bColor = $(this).parents('.ui-slider-tooltip').css('backgroundColor')
				$(this).css('border-top', bWidth+' solid '+bColor);
			});
		}
		if($('select#valueAA').length && $('select#valueBB').length) {
			$('select#valueAA, select#valueBB').selectToUISlider({
				labels: 2
			});
		}
	</script>

	<script type="text/javascript" src="http://informea.eaudeweb.ro/clickheat/js/clickheat.js"></script>
		<noscript><p><a href="http://www.dugwood.com/clickheat/index.html">ClickHeat</a></p></noscript>
		<script type="text/javascript">
		<!--
			clickHeatSite = 'informea.org';
			clickHeatGroup = encodeURIComponent(window.location.pathname+window.location.search);
			clickHeatServer = 'http://informea.org/clickheat/click.php';
			initClickHeat();
		//-->
	</script>
</body>
</html>
