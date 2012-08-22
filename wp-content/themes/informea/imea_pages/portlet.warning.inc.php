<?php
if(!isset($warning_text)) {
	$warning_text = __('Please note that the decisions from Ramsar, UNCCD and UNFCCC and the Kyoto Protocol, other than those listed here, will be accessible in the course of 2012', 'informea');
}
?>
<div class="related-box">
	<div class="box-content">
		<img alt="warn" title="Important, please read carefully" src="<?php bloginfo('template_directory'); ?>/images/warning-small.png" class="middle" />
		<?php echo $warning_text; ?>
	</div>
</div>
<br />
