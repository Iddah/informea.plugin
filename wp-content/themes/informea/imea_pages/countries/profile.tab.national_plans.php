<strong>Disclaimer: </strong>Please note that more national plans will be accessible by February 2012
<?php if(count($treaties_plans)) { ?>
<ul class="list-dropdown hidden list-view" style="display: block;">
<?php
	foreach($treaties_plans as $treaty) {
		$plans = (isset($all_plans[$treaty->id])) ? $all_plans[$treaty->id] : array();
?>
	<li>
		<a href="javascript:void(0);" class="left closed list-item-title-click">
			<div class="list-item-title">
				<img class="left mea-logo" title="<?php echo $treaty->short_title; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" src="<?php echo $treaty->logo_medium; ?>" />
				<div class="left mea-name"><?php echo $treaty->short_title; ?></div>
			</div>
		</a>
		<div class="list-item-content hidden">
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
		<div class="clear"></div>
	</li>
	<?php } ?>
</ul>
<?php } ?>
