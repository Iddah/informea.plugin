<strong>Disclaimer: </strong>Please note that more national plans will be accessible by February 2012
<?php if(count($treaties_plans)) { ?>
<ul class="list-dropdown hidden list-view" style="display: block;">
<?php
	foreach($treaties_plans as $treaty) {
		$plans = (isset($all_plans[$treaty->id])) ? $all_plans[$treaty->id] : array();
?>
	<li id="treaty-<?php echo $treaty->id; ?>" class="collapsible  even">
		<div class="left list-item-content">
			<div class="list-item-top-details">
				<div class="left list-item-action">
					<a id="toggle-treaty-<?php echo $treaty->id; ?>" class="toggle-treaty" href="javascript:void(0);">
						<img src="<?php bloginfo('template_directory'); ?>/images/expand.gif" alt="Compress" title="<?php _e('Compress content', 'informea'); ?>" />
					</a>
				</div>

				<div class="left list-item-logo">
					<a class="toggle-treaty" href="javascript:void(0);" title="Click to see the reports">
						<img title="<?php echo $treaty->short_title; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" src="<?php echo $treaty->logo_medium; ?>">
					</a>
				</div>

				<div class="left list-item-title">
					<a class="toggle-treaty" href="javascript:void(0);" title="Click to see the reports">
						<?php echo $treaty->short_title; ?>
					</a>
				</div>
				<div class="clear"></div>
			</div>

			<div style="display: none;" class="list-item-content-details">
				<div class="list-item-details">
				<?php foreach($plans as $plan) { ?>
					<div style="margin: 1em 0 0.5em 0">
						<h3><?php echo $plan->title; ?></h3>
						<br />
						<strong><?php _e('Date of submission', 'informea'); ?></strong> : <em><?php echo $plan->submission; ?></em>
						<br />
						<?php if($plan->document_url !== NULL) { ?>
						<strong><?php _e('Document', 'informea'); ?></strong>:
							<a target="_blank" href="<?php echo $plan->document_url; ?>" title="<?php echo esc_attr($plan->document_url); ?>">View</a>
						<br />
						<?php } ?>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</li>
	<?php } ?>
</ul>
<?php } ?>
