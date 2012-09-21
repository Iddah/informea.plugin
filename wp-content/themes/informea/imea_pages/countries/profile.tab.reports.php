<strong>Disclaimer: </strong>Please note that national reports will be accessible in the course of 2012
<?php if(count($treaties_reports)) { ?>
<ul class="list-dropdown">
<?php
	foreach($treaties_reports as $treaty) {
		$reports = (isset($all_reports[$treaty->id])) ? $all_reports[$treaty->id] : array();
?>
	<li>
		<a href="javascript:void(0);" class="left closed list-item-title-click">
			<div class="list-item-title">
				<img class="left mea-logo" title="<?php echo $treaty->short_title; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" src="<?php echo $treaty->logo_medium; ?>" />
				<div class="left mea-name"><?php echo $treaty->short_title; ?></div>
			</div>
		</a>
		<div class="list-item-content hidden">
		<?php foreach($reports as $report) { ?>
			<div style="margin: 1em 0 0.5em 0">
				<h3><?php echo $report->title; ?></h3>
				<br />
				<strong><?php _e('Date of submission', 'informea'); ?></strong> : <em><?php echo $report->submission; ?></em>
				<br />
				<?php if($report->document_url !== NULL) { ?>
				<strong><?php _e('Document', 'informea'); ?></strong>:
					<a target="_blank" href="<?php echo $report->document_url; ?>" title="<?php echo esc_attr($report->document_url); ?>">View</a>
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
