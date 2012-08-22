<?php
$treaties = $page_data->get_treaties_list();

if($page_data->expand == 'treaty') {
?>

<?php
	}
?>
<div class="left details-column-1">
	<?php include(dirname(__FILE__) . '/portlet.filter.php'); ?>
	<div class="clear"></div>
	<br />
	<style type="text/css">
		.tags ul { height: auto !important; }
	</style>

	<?php
	$cloud_items_nr = 20;
	$terms = new Thesaurus(null);
	$top_concepts = $terms->get_top_concepts();
	$popular_terms = $page_data->get_popular_terms(NULL, 7);
	$cloud_terms = array_merge($top_concepts, $popular_terms);
	include(dirname(__FILE__) . '/../portlet.terms-cloud.inc.php'); ?>
</div>
<div class="left details-column-2">
	<div class="toolbar">
		<?php
			if($page_data->expand == 'term') {
		?>
			&nbsp;&nbsp;&nbsp;
			<a href="javascript:void(0); " class="expand-button"><?php _e('Expand all', 'informea'); ?></a>
			&nbsp;|&nbsp;
			<a href="javascript:void(0); " class="compress-button disabled"><?php _e('Collapse all', 'informea'); ?></a>
		<?php
			}
		?>
	</div>

	<div class="view-mode">
		<form action="">
			<label for="view-mode"><?php _e('View', 'informea'); ?></label>
			<select id="view-mode" name="view-mode" onchange="window.location = $(this).val();">
				<?php $selected = ($page_data->expand == 'treaty') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/decisions/treaty"><?php _e('By treaty', 'informea'); ?></option>
				<?php $selected = ($page_data->expand == 'term') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/decisions/term"><?php _e('By term', 'informea'); ?></option>
			</select>
		</form>
	</div>
	<div class="clear"></div>
	<?php
		if($page_data->expand == 'treaty') {
			$count = 0;
	?>
	<div id="treaty-container">
		<?php
			foreach($treaties as $theme => $t) {
		?>
			<h3><?php echo $theme; ?></h3>
			<div class="clear"></div>
			<?php
				foreach($t as $treaty) {
					if($treaty->odata_name == 'aewa') {
						continue;
					}
					if($treaty->id == 9) {
						$tooltip = __('The Nagoya protocol was adopted at CBD COP 10 and will enter into force 90 days after the ratification by 50 parties. Open Nagoya Protocol', 'informea');
					} else {
						$tooltip = __('See the decisions for', 'informea') . ' ' . $treaty->short_title;
					}
			?>
				<div class="treaty-entry">
					<div class="treaty-icon">
						<a class="link" href="<?php echo get_imea_url() . '/treaties/' . $treaty->odata_name; ?>/decisions" title="<?php echo $tooltip; ?>"><img src="<?php echo $treaty->logo_medium; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" /></a>
						<br />
						<a class="link" href="<?php echo get_imea_url() . '/treaties/' . $treaty->odata_name; ?>/decisions" title="<?php echo $tooltip; ?>">
							<?php echo $treaty->short_title_alternative; ?>
						</a>
						<br />
						<span class="text-grey">(<?php echo $treaty->theme_secondary; ?>)</span>
					</div>
				</div>
			<?php } ?>
			<div class="clear"></div>
		<?php
				$count++;
			}
		?>

		<?php
			// Hard-coding for aewa
			foreach($treaties as $theme => $t) {
				if($theme != '') { continue; }
		?>
			<h3><?php echo $theme; ?></h3>
			<div class="clear"></div>
			<?php
				foreach($t as $treaty) {
					if($treaty->odata_name != 'aewa') {
						continue;
					}
			?>
				<div class="treaty-entry">
					<div class="treaty-icon">
						<a class="link" href="<?php echo get_imea_url() . '/treaties/' . $treaty->odata_name; ?>/decisions" title="<?php echo $tooltip; ?>"><img src="<?php echo $treaty->logo_medium; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" /></a>
						<br />
						<a class="link" href="<?php echo get_imea_url() . '/treaties/' . $treaty->odata_name; ?>/decisions" title="<?php echo $tooltip; ?>">
							<?php echo $treaty->short_title_alternative; ?>
						</a>
						<br />
						<span class="text-grey">(<?php echo $treaty->theme_secondary; ?>)</span>
					</div>
				</div>
			<?php } ?>
			<div class="clear"></div>
		<?php
				$count++;
			}
		?>
	</div>
	<?php } ?>


	<?php
		if($page_data->expand == 'term') {
	?>
	<ul id="term-container" class="list-dropdown">
		<?php
			$count = 0;
			foreach( $page_data->get_themes() as $row ) {
				$tterms = $page_data->expand_theme_terms($row->id);
				if(!empty($tterms)) {
		?>
		<li class="collapsible  <?php echo($count % 2)?'odd':'even'; ?>" id="treaty-<?php echo $row->id; ?>">
			<div class="left list-item-content">
				<div class="list-item-top-details">
					<div class="left list-item-action">
						<a href="javascript:void(0); " class="toggle-treaty" id="toggle-treaty-<?php echo $row->id; ?>">
							<img src="<?php bloginfo('template_directory'); ?>/images/expand.gif" alt="<?php _e('Compress', 'informea'); ?>" title="<?php _e('Compress content', 'informea'); ?>" />
						</a>
					</div>
					<div class="left list-item-title">
						<span>
							<a href="<?php echo bloginfo('url'); ?>/terms/<?php echo $row->id; ?>/decisions"><?php echo $row->term; ?></a>
						</span>
					</div>
					<div class="clear"></div>
				</div>

				<div class="list-item-content-details hidden">
					<div class="list-item-details">
						<?php
							foreach( $tterms as $term ) {
						?>
								<div class="letter-box">
									<a  href="<?php echo bloginfo('url'); ?>/terms/<?php echo $term->id; ?>/decisions"  title="<?php _e('Browse', 'informea'); ?> <?php echo $term->term; ?>"><?php echo $term->term;?></a>
								</div>
						<?php } ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</li>
		<?php
				$count++;
				}
			}
		?>
	</ul>
	<?php } ?>
</div>
<div class="left details-column-3"></div>
