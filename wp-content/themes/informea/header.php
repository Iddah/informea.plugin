<?php
require_once(dirname(__FILE__) . '/template/index.php');
$imea_options = get_option('informea_options');
// Actions for management of treaty paragraphs in edit mode
if( current_user_can('manage_options') ) {
	do_action('move_up_treaty_paragraph');
	do_action('move_down_treaty_paragraph');
}
$tpl_show_explorer = $post !== NULL && $post->post_name != 'index' && $post->post_name != 'search';
$tpl_languages = 'English | <span title="Not implemented yet">Français</span>';
$tpl_primary_menu = wp_nav_menu(array('echo' => false, 'menu' => 'Main', 'container_class' => 'menu-header', 'theme_location' => 'primary', 'walker' => new imea_menu_walker()));
$tpl_useful_links_menu = !is_user_logged_in() ?
	'<a href="' . get_bloginfo('url') . '/wp-admin/">' . __('Login', 'informea') . '</a>'
	: '<a href="' . get_bloginfo('url') . '/wp-admin/">' . __('Administration Panel', 'informea') . '</a> | <a href="' . wp_logout_url( get_permalink() ) . '">' . __('Logout', 'informea') . '</a>';
$tpl_breadcrumbtrail = ($post !== NULL && $post->post_name != 'index') ? '<div id="breadcrumb" class="clear"><span class="breadcrumb-name">' . __('You are here:', 'informea') . '</span> <a href="' . get_bloginfo('url') . '" title="">' . __('Home', 'informea') . '</a>' . apply_filters('breadcrumbtrail', '') . '</div>' : '';

$mobile = new Mobile_Detect();
add_filter('body_class', function ($classes) { $classes[] = 'informea'; return $classes; });
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

		<link rel="alternate" type="application/rss+xml" title="InforMEA &raquo; Highlights" href="<?php bloginfo('url'); ?>/highlights/rss" />
		<link rel="alternate" type="application/rss+xml" title="InforMEA &raquo; Events" href="<?php bloginfo('url'); ?>/events/rss" />

		<script type="text/javascript">
			var images_dir = '<?php bloginfo('url'); ?>/wp-content/themes/informea/images/';
			var blog_dir = '<?php bloginfo('url'); ?>';
			var ajax_url = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php';
		</script>
		<title>
			<?php
			global $page, $paged;
			wp_title( '|', true, 'right' );
			echo apply_filters('informea_page_title', '');
			bloginfo( 'name' );
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) ) {
				echo " | $site_description";
			}
			if ( $paged >= 2 || $page >= 2 ){
				echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );
			}
			?>
		</title>
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<?php tengine_head(); ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php
			// cristiroma: no comments in informea
			// if ( is_singular() && get_option( 'thread_comments' ) ){
			//	wp_enqueue_script( 'comment-reply' );
			// }
			/* Always have wp_head() just before the closing </head>
			 * tag of your theme, or you will break many plugins, which
			 * generally use this hook to add elements to <head> such
			 * as styles, scripts, and meta tags.
			 */
			wp_head();
		?>
</head>
<body <?php body_class(); ?>>
<?php tengine_header($tpl_show_explorer, $tpl_primary_menu, $tpl_useful_links_menu, $tpl_breadcrumbtrail, $tpl_languages); ?>
