<?php
function tengine_header($show_explorer = true, $primary_menu = '', $useful_links_menu = '', $breadcrumbtrail = '', $languages = '', $secondary_menu = '') {
	global $post;
?>
	<div id="informea-topnav">
		<div class="language">
			<a name="top"></a>
			<?php echo (!empty($languages)) ? $languages : '<span id="informea_template_languages_menu"></span>'; ?>
		</div>

		<div class="useful-links">
			<?php echo (!empty($useful_links_menu)) ? $useful_links_menu : '<span id="informea_template_useful_links_menu"></span>'; ?>
		</div>
		<div class="clear"></div>
	</div>

	<div id="informea-wrapper" class="hfeed">
		<div id="informea-header">
			<div id="masthead">
				<div id="branding" role="banner">
					<div id="logo-holder">
						<?php
						// Check if this is a post or page, if it has a thumbnail, and if it's a big one
						if ( is_singular() &&
								has_post_thumbnail( $post->ID ) &&
								( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' ) ) &&
								$image[1] >= HEADER_IMAGE_WIDTH ) :
							// Houston, we have a new header image!
							echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
						else : ?>
							<a href="/"><img src="<?php header_image(); ?>" alt="" title="<?php echo bloginfo('name'); ?>" id="logo-image" /></a>
						<?php endif; ?>
					</div>
					<p class="site-description"><?php bloginfo( 'description' ); ?></p>
				</div><!-- #branding -->

				<div id="access" role="navigation" class="clear">
					<?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
					<div class="skip-link screen-reader-text">
						<a href="#content" title="<?php _e( 'Skip to content', 'informea' ); ?>"><?php _e( 'Skip to content', 'informea' ); ?></a>
					</div>
					<?php echo (!empty($primary_menu)) ? $primary_menu : '<span id="informea_template_primary_menu"></span>'; ?>
					<?php
						if($show_explorer) {
					?>
					<div class="access-right">
						<div class="search-explorer-button right">
							<div class="search-explorer-button-left left">&nbsp;</div>
							<div class="search-explorer-button-content left">
								<div class="explorer-toggle-icon left">
									<img src="<?php bloginfo('template_directory'); ?>/images/s.gif" alt="" title="<?php _e('Toggle MEA Explorer box', 'informea'); ?>" class="explorer-toggle normal" />
								</div>
								<div class="explorer-button-text">
									<?php _e('MEA Explorer', 'informea'); ?>
								</div>
								<span class="explorer-button-links"><?php _e('Start your search here', 'informea'); ?></span>
							</div>
						</div>
						<div class="clear"></div>
						<?php include(dirname(__FILE__) . '/../imea_pages/explorer/inc.explorer.php'); ?>
					</div>
				<?php
					}
				?>
				</div><!-- #access -->

				<div class="clear"></div>
			</div><!-- #masthead -->
		</div><!-- #header -->
		<div id="main">
		<?php echo (!empty($breadcrumbtrail)) ? $breadcrumbtrail : '<span id="informea_template_breadcrumbtrail"></span>'; ?>
<?php
	}
?>
