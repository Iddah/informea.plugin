<?php
// Actions for management of treaty paragraphs in edit mode
if( current_user_can('manage_options') ) {
	do_action('move_up_treaty_paragraph');
	do_action('move_down_treaty_paragraph');
}

$treaty = $page_data->treaty;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<script type="text/javascript">
			var images_dir = '<?php bloginfo('url'); ?>/wp-content/themes/informea/images/';
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
		<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
		<!--[if IE 7]>
		<link href="<?php bloginfo('template_directory'); ?>/fix-IE7.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<!--[if gte IE 8]>
		<link href="<?php bloginfo('template_directory'); ?>/fix-IE8.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php
			if ( is_singular() && get_option( 'thread_comments' ) ){
				wp_enqueue_script( 'comment-reply' );
			}

			wp_head();
		?>

		<style type="text/css" media="all">
			body { background-color: white; }
			.unep-logo { vertical-align:middle;  }
			h2 { margin: 0; padding: 0; color: black !important; }
			table { page-break-inside: auto }
			tr { page-break-inside:avoid; page-break-after:auto }
			td { page-break-before: auto; }
			#treaty-print .td-tags { padding-left: 15px; width: 170px; }
			#treaty-print .content { text-align: justify;  }
			#treaty-print tr td { padding-bottom: 10px; vertical-align: top; }
			#treaty-print tr.new-article td { padding-top: 20px; }
			#un-button { display: none; }
		</style>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/jquery-min.js"></script>
	</head>

	<body <?php body_class(); ?>>
	<img class="unep-logo" src="<?php echo bloginfo("template_directory"); ?>/images/logo-black.png" alt="<?php echo bloginfo('name'); ?> &middot; Logo" title="<?php echo bloginfo('name'); ?>" id="logo-image" />
		<h1>
			<?php echo $treaty->long_title; ?>
		</h1>
		<table id="treaty-print" class="treaty-<?php echo $treaty->odata_name; ?>">
		<?php
			$c = 0;
			foreach($page_data->articles as $article) {
				$at = $page_data->get_article_tags($article->id);
				$paragraphs = isset($page_data->paragraphs_by_article[$article->id]) ? $page_data->paragraphs_by_article[$article->id] : array();
				$atags_ob = $page_data->get_article_tags($article->id);
				$atags = array();
		?>
			<tr class="new-article">
				<td><h2><?php echo $article->official_order; ?> <?php echo $article->title; ?></h2></td>
				<td class="td-tags">
					<?php
						if(count($atags_ob)) {
							foreach($atags_ob as $atag) { $atags[] = $atag->term; }
							echo '<strong>Tags</strong>: ';
							echo implode(', ', $atags);
						}
					?>
					&nbsp;
				</td>
			</tr>
			<?php
				if(count($paragraphs)) {
					foreach($paragraphs as $paragraph) {
						$zebra = ($c++ % 2 == 0) ? 'zebra' : 'normal';
						$ptags_ob = $page_data->get_paragraph_tags($paragraph->id);
						$ptags = array();
			?>
			<tr class="<?php echo $zebra; ?>">
				<td class="content"><?php echo $paragraph->content; ?></td>
				<td class="td-tags">
					<?php
						if(count($ptags_ob)) {
							echo '<strong>Tags</strong>: ';
							foreach($ptags_ob as $ptag) { $ptags[] = $ptag->term; }
							echo implode(', ', $ptags);
						}
					?>
					&nbsp;
				</td>
			</tr>
			<?php
					}
				} else {
					// No paragraphs, list the entire article
			?>
			<tr>
				<td class="content"><?php echo $article->content; ?></td>
				<td class="td-tags">
					<?php
						if(count($atags_ob)) {
							foreach($atags_ob as $atag) { $atags[] = $atag->term; }
							echo '<strong>Tags</strong>: ';
							echo implode(', ', $atags);
						}
					?>
					&nbsp;
				</td>
			</tr>
		<?php
				}
			}
		?>
		</table>
	</body>
</html>
