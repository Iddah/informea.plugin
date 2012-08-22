<?php
	$search2 = new InformeaSearch2($_GET);
	$sel_terms = $search2->ui_get_selected_terms();
	$terms_page = new Thesaurus(NULL);
?>
<script type="text/javascript">
	// This fragment stays here
	var freetext_default = '<?php echo $search2->ui_get_freetext_default();?>';
</script>
<div id="search" class="hidden">
	<div class="search-content">
		<div class="search-section all-search">
			<div class="search-section-title">
				<a href="javascript:void(0); ">
					<img src="<?php bloginfo('template_directory'); ?>/images/search-box/minus.png" alt="<?php _e('Toggle', 'informea'); ?>" class="left search-explorer-toggle-icon" />
				</a>
				<h3 class="left"><?php _e('Quick search ALL', 'informea'); ?></h3>
				<div class="clear"></div>
			</div>
			<div class="all-search-content search-section-content">
				<form method="get" action="<?php bloginfo('url'); ?>/search" id="explorer-all-form">
					<input type="hidden" name="q_page_size" value="10" />
					<input type="hidden" name="q_page" value="0" />
					<input type="hidden" name="q_use_meetings" value="1" />
					<input type="hidden" name="q_use_treaties" value="1" />
					<input type="hidden" name="q_use_decisions" value="1" />
					<input type="hidden" name="q_tab" value="2" />
				<?php
					$ts = $search2->ui_get_treaties_ids();
					foreach($ts as $t_id) {
				?>
					<input type="hidden" name="q_treaty[]" value="<?php echo $t_id; ?>" />
				<?php
					}
				?>
					<input type="text" id="explorer_q_freetext" name="q_freetext"
						class="search-explorer-input white long-input left" value="<?php echo $search2->get_freetext($search2->ui_get_freetext_default()); ?>" />
						<a id="treaties-submit" class="button orange" title="Search treaties and decisions" href="javascript:void(0);" onclick="$(this).closest('form').submit();">
						<span><?php _e('Search', 'informea');?></span>
					</a>
				</form>
				<div class="clear"></div>
			</div>
		</div>

		<div class="clear"></div>

		<div class="search-section treaties-search">
			<div class="search-section-title">
				<a href="javascript:void(0); ">
					<img src="<?php bloginfo('template_directory'); ?>/images/search-box/plus.png" alt="<?php _e('Toggle', 'informea'); ?>" class="left search-explorer-toggle-icon" />
				</a>
				<h3 class="left"><?php _e('Treaties and Decisions/Resolutions', 'informea'); ?></h3>
				<div class="clear"></div>
			</div>
			<div class="treaties-search-content search-section-content hidden">
				<form method="get" action="<?php bloginfo('url'); ?>/search" id="treaties-search-form">
					<input type="hidden" name="q_tab" value="2" />
					<?php include(dirname(__FILE__) . '/inc.treaties.php'); ?>
					<div class="separator"></div>

					<input id="explorer_q_use_decisions" type="checkbox" name="q_use_decisions" value="1" checked="checked" />
					<label for="explorer_q_use_decisions"><?php _e('Decisions/Resolutions', 'informea'); ?></label>

					<input id="explorer_q_use_treaties" type="checkbox" name="q_use_treaties" value="1" checked="checked" />
					<label for="explorer_q_use_treaties"><?php _e('Treaties', 'informea'); ?></label>

					<div>
						<label for="free-text">Free search text</label>
						<br />
						<span class="input-wrap">
							<img class="input-left left" title="" alt="" src="<?php $search2->img_s_gif();?>">
							<input type="text" id="free-text" value="<?php echo $search2->get_freetext($search2->ui_get_freetext_default()); ?>" name="q_freetext" class="search-explorer-input long-input left">
							<img class="input-right left" alt="" src="<?php $search2->img_s_gif();?>">
						</span>
					</div>
					<div class="clear"></div>

					<div class="tagged-terms">
						<div class="term-explorer-holder">
							<div class="autocomplete-holder">
								<label for="q_term_explorer">Type keyword</label>
								<br />
								<select id="q_term_explorer" name="q_term[]" multiple="multiple">
								<?php
									$terms = $terms_page->suggest_vocabulary_terms();
									$sterms = $search2->get_terms();
									foreach($terms as $term) {
											$search2->ui_write_option($term->id, $term->term, in_array(intval($term->id), $sterms));
									}
								?>
								</select>
							</div>
							<div class="input-holder">
								<?php
									$checked = $search2->is_terms_or() ? '' : ' checked="checked"';
								?>
								<?php $visible = count($sel_terms) > 1 ? '' : 'hidden'; ?>
								<div id="explorer_and_or_radiobuttons" class="<?php echo $visible; ?>">
									<input type="radio" id="q_term_or_and_explorer" name="q_term_or" value=""<?php echo $checked; ?> />
									<label for="q_term_or_and_explorer">AND</label>
									<?php $checked = $search2->is_terms_or() ? ' checked="checked"' : ''; ?>
									<input type="radio" id="q_term_or_or_explorer" name="q_term_or" value="or"<?php echo $checked; ?> />
									<label for="q_term_or_or_explorer">OR</label>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="selected-terms-holder"></div>
					</div>
					<div class="clear"></div>
					<?php
						include(dirname(__FILE__) . '/inc.date_interval.php');
						// include(dirname(__FILE__) . '/inc.date_timeline.php');
					?>
					<br clear="all" />
					<a id="treaties-submit" class="button orange" title="Search treaties and decisions" href="javascript:void(0);" onclick="$(this).closest('form').submit();">
						<span>Search</span>
					</a>
				</form>
				<div class="clear"></div>
			</div>
		</div>

		<div class="clear"></div>

		<div class="search-section events-search">
			<div class="search-section-title">
				<a href="javascript:void(0); ">
					<img src="<?php bloginfo('template_directory'); ?>/images/search-box/plus.png" alt="<?php _e('Toggle', 'informea'); ?>" class="left search-explorer-toggle-icon" />
				</a>
				<h3 class="left"><?php _e('Meetings and Events', 'informea'); ?></h3>
				<div class="clear"></div>
			</div>
			<div class="events-search-content hidden">
				<form method="get" action="<?php bloginfo('url'); ?>/search" id="meetings-search-form">
					<input type="hidden" name="q_use_meetings" value="1" />
					<?php include(dirname(__FILE__) . '/inc.treaties.php'); ?>
					<?php include(dirname(__FILE__) . '/inc.date_interval.php'); ?>
					<br />
					<label for="free-text2">
						<strong>
							Containing
						</strong>
					</label>
					<br />
					<span class="input-wrap">
						<img class="input-left left" title="" alt="" src="<?php $search2->img_s_gif();?>">
						<input type="text" id="free-text2" value="<?php echo $search2->get_freetext($search2->ui_get_freetext_default()); ?>" name="q_freetext" class="search-explorer-input long-input left">
						<img class="input-right left" title="Check/Uncheck" alt="" src="<?php $search2->img_s_gif();?>">
					</span>

					<div class="clear"></div>

					<a id="treaties-submit" class="button orange" title="Search treaties and decisions" href="javascript:void(0);" onclick="$(this).closest('form').submit();">
						<span>Search</span>
					</a>
				</form>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
