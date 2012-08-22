<?php
/**
 * This is the browse terms page. Displayed when user clicks 'Browse terms' or 'Terms' link from main menu.
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */
$term = $page_data->term;
?>
<div class="left details-column-1">
	<?php
		if(!empty($term->description)) {
			$definition = subwords($term->description, 60);
			$c1 = strlen($term->description);
			$c2 = strlen($definition);
	?>
	<p id="term_definition" class="justify">
		<?php echo $definition; ?>
		<?php if($c1 > $c2) { ?>
			<a href="javascript:showFullDefinition();">more &raquo;</a>
		<?php } ?>
	</p>
	<?php
			if($c1 > $c2) { ?>
	<p id="term_full_definition" class="justify">
			<?php echo $term->description; ?>
			<?php // <a href="javascript:showShortDefinition();">&laquo; less</a> ?>
	</p>
	<?php	} ?>
	<?php } ?>
	<?php
		if(isset($page_data->related['broader'])) {
			$broaders = $page_data->related['broader'];
	?>
	<p>
		<strong><?php _e('Broader term(s)', 'informea'); ?>:</strong>
		<?php
			$last = end($broaders);
			foreach($broaders as $broader_term) {
		?>
			<a class="no-underline" href="<?php bloginfo('url'); ?>/terms/<?php echo $broader_term->id; ?>"><?php echo $broader_term->term; ?></a><?php if($last !== $broader_term) echo ','; ?>
		<?php } ?>
	</p>
	<?php } ?>
	<?php if(!empty($term->geg_tools_url)) { ?>
	<p>
		<br />
		<a href="<?php echo $term->geg_tools_url; ?>">See Global Environmental Goals</a>
	</p>
	<?php } ?>

<?php
	// Show related and narrower terms in one cloud
	$t = array();
	if(isset($page_data->related['related'])) {
		$t = array_merge($t, $page_data->related['related']);
	}
	if(isset($page_data->related['narrower'])) {
		$t = array_merge($t, $page_data->related['narrower']);
	}
	$cloud_terms = $page_data->array_unique_terms($t);
	$cloud_terms = $page_data->compute_popularity($t);
	include(dirname(__FILE__) . '/../portlet.terms-cloud.inc.php');
?>
</div>
<div class="left details-column-2">
	<div class="tab-menu">
		<ul>
			<li>
				<a class="tab<?php echo ($tab == 'treaties')?'-active':''; ?>" href="<?php echo bloginfo('url');?>/terms/<?php echo $term->id; ?>/treaties"><?php _e('Treaties', 'informea'); ?></a>
			</li>
			<li>
				<a class="tab<?php echo ($tab == 'decisions')?'-active':''; ?>" href="<?php echo bloginfo('url');?>/terms/<?php echo $term->id; ?>/decisions"><?php _e('Decisions', 'informea'); ?></a>
			</li>
			<li>
				<a class="tab<?php echo ($tab == 'ecolex')?'-active':''; ?>" href="<?php echo bloginfo('url');?>/terms/<?php echo $term->id; ?>/ecolex"><?php _e('Ecolex literature', 'informea'); ?></a>
			</li>
		</ul>
	</div>


	<div class="tabs-wrap">
		<div class="tab-content">
			<!-- Middle column content -->
			<div class="clear"></div>
			<?php include(dirname(__FILE__) . "/details.tab.$tab.php"); ?>
		</div>
	</div>
</div>
<?php
	function js_inject_term_details() {
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#term_full_definition').hide();
		});
		function showFullDefinition() {
			$('#term_full_definition').show();
			$('#term_definition').hide();
		}
		function details(itemId) {
			var ctrl = $('#' + itemId);
			var ctrl_link = $('#link-' + itemId);
			if(ctrl.is(':visible')) {
				ctrl.slideUp('fast');
				ctrl_link.removeClass('opened');
				ctrl_link.addClass('closed');
			} else {
				ctrl.slideDown('fast');
				ctrl_link.removeClass('closed');
				ctrl_link.addClass('opened');
			}
		}
	</script>
<?php
	}
	add_action('js_inject', 'js_inject_term_details');
?>
