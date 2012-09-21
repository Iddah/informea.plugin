<?php
/**
 * This is the browse terms page. Displayed when user clicks 'Browse terms' or 'Terms' link from main menu.
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */
$letters = $page_data->get_alphabet_letters();
?>
<div class="left details-column-1">
	<?php include(dirname(__FILE__) . '/portlet.autocomplete.php'); ?>
	<div class="related-box download-vocabulary">
		<div class="box-title">Download vocabulary</div>
		<div class="box-content">
			<ul class="vocabulary-download">
				<li><a class="tooltip" href="<?php bloginfo('url'); ?>/download?entity=terms_csv&lang=en" title="Download this file if your computer is set to use english/american locale">English locale</a></li>
				<li><a class="tooltip" href="<?php bloginfo('url'); ?>/download?entity=terms_csv&lang=fr" title="Download this file if your computer is set to use french/swiss locale">French locale</a></li>
			</ul>
		</div>
	</div>
	<div class="clear"></div>
	<div class="portlet-terms-tags">
	<style type="text/css">
		.tags ul { height: auto !important; }
	</style>
	<?php
		$top_concepts = $page_data->get_top_concepts();
		$popular_terms = $page_data->get_popular_terms(NULL, 7);
		$cloud_terms = array_merge($top_concepts, $popular_terms);
		//$cloud_terms = $page_data->get_top_concepts();
		include(dirname(__FILE__) . '/../portlet.terms-cloud.inc.php');
	?>
	</div>
</div>

<div class="left details-column-2">
	<div class="view-mode">
		<form action="">
			<label for="view-mode"><?php _e('View', 'informea'); ?></label>
			<select id="view-mode" name="view-mode" onchange="window.location = $(this).val();">
				<?php $selected = ($expand == 'theme') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/terms/theme"><?php _e('Hierarchical', 'informea'); ?></option>
				<?php $selected = ($expand == 'alphabet') ? 'selected="selected "' : ''; ?>
				<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/terms/alphabet"><?php _e('Alphabetical', 'informea'); ?></option>
			</select>
		</form>
	</div>
	<div class="clear"></div>
<?php if($expand == 'theme') { ?>
	<div id="tab-menu-substantives-content" class="tab-content">
		<a href="javascript:void(0);" onclick="expandSubstantives();">Open all</a>
		<div id="termsTree_substantives" class="terms_tree">
			<div class="loading">
				<img src="<?php bloginfo('template_directory'); ?>/images/loading-big.gif" /><br />Loading, please wait ...
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<?php } ?>


	<?php if($expand == 'alphabet') { ?>
	<div class="toolbar left">
		<a href="javascript:void(0); " class="expand-button"><?php _e('Expand all', 'informea'); ?></a>
		<a href="javascript:void(0); " class="compress-button disabled"><?php _e('Collapse all', 'informea'); ?></a>
	</div>
	<div class="clear"></div>
	<div class="alphabet-container">
		<ul class="list-dropdown">
			<?php
				$count = 0;
				$dictionary = $page_data->index_alphabetical();
				foreach($letters as $letter) {
			?>
				<li class="<?php echo($count % 2)?'odd':'even'; ?>" id="treaty-<?php echo $letter->letter; ?>">
				<a href="javascript:void(0);" class="left closed list-item-title-click">
					<div class="list-item-title">
						<div class="left">
							<?php echo $letter->letter; ?>
						</div>
					</div>
				</a>
				<div class="list-item-content hidden">
				<?php
					$terms = $dictionary[$letter->letter];
					foreach($terms as $term) {
				?>
					<table>
						<td style="width: 200px;">
							<a href="<?php echo get_permalink() . '/' . $term->id; ?>"  title="<?php _e('Browse', 'informea'); ?> '<?php echo $term->term; ?>'"><?php echo $term->term;?></a>
						</td>
						<td>
							<?php echo $term->description; ?>
						</td>
					</table>
				<?php } ?>
				</div>
				<div class="clear"></div>
			</li>
			<?php
					$count++;
				}
			?>
		</ul>
	</div>
<?php } ?>
</div>
<div class="left details-column-3"></div>

<?php
if($expand == 'theme') {
	function js_inject_terms_grooveshark() {
?>
	<script src="<?php bloginfo('template_directory'); ?>/scripts/dhtmlxtree/dhtmlxcommon.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/scripts/dhtmlxtree/dhtmlxtree.js"></script>
	<script type="text/javascript">
		var treeXMLUrl = ajax_url + '?action=generate_terms_tree_public';
		var treeImagePath = "<?php bloginfo('template_directory'); ?>/scripts/dhtmlxtree/imgs/terms_blue/";

		var allnodes_substantives = Array();

		var tree_substantives;

		$(document).ready(function() {
			treeImagePath = "<?php bloginfo('template_directory'); ?>/scripts/dhtmlxtree/imgs/terms_blue/";

			tree_substantives = new dhtmlXTreeObject('termsTree_substantives', '100%', '100%', 0);
			tree_substantives.setImagePath(treeImagePath); // Global variable
			tree_substantives.enableTreeImages(false);
			tree_substantives.enableDragAndDrop(false);
			tree_substantives.loadXML(treeXMLUrl + '&substantives=1', function() {
				$('.loading').hide();
				allnodes_substantives = tree_substantives.getAllSubItems(0).split(',');
				expandRoots();
			});

			tree_substantives.attachEvent('onClick', function(id, prevId) {
				var termId = tree_substantives.getUserData(id, 'term_id');
				window.location = blog_dir + '/terms/' + termId;
				return false;
			});
		});

		function setTab(tab) {
			$('.tab-menu li a').removeClass('tab-active');
			$('.tab-menu li a').addClass('tab');
			$('#' + tab + ' a').removeClass('tab');
			$('#' + tab + ' a').addClass('tab-active');

			$('.tab-content').hide();
			$('#' + tab + '-content').show();
		}

		function focusNextItem(tree, items, item) {
			var found = false;
			var rgx = new RegExp(item, 'i');
			$.each(items, function(idx, nodeId) {
				var label = tree.getItemText(nodeId);
				if(rgx.exec(label)) {
					tree.openItem(nodeId);
					tree.focusItem(nodeId);
					tree.selectItem(nodeId);
					found = true;
					return false;
				}
			});
		}

		function expandSubstantives() {
			tree_substantives.openAllItems(0);
		}

		function expandRoots() {
			tree_substantives.openItem('generic');
			tree_substantives.openItem('substantives');
		}
	</script>
<?php
	}
	add_filter('js_inject', 'js_inject_terms_grooveshark');
}
?>
