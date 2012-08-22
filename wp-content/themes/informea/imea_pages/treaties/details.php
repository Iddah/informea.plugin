<?php
    $additional_cloud_items_nr = 0;
	$url_article_id = get_request_variable('id_treaty_article', 1);
	function js_inject_treaty_details() {
?>
<script type="text/javascript">
$(document).ready(function() {
	var paraDiv = $('#' + window.location.hash.replace('#', ''));
//	console.log(paraDiv);
	if(paraDiv.length > 0) {
		paraDiv.css('background-color', '#FFE974');
//		paraDiv.attr('class', '');
		var scp = paraDiv.position().top - 200;
		$.scrollTo('' + scp + 'px', 100);
	}
	if ($("#treaties-details-short_title").height() < 30) {
		$("#treaties-details-short_title").css("marginTop","12px");
	}
});
function abstract_collapse(){
	$("#abstract_partial").hide();
	$("#abstract_full").show();
}
</script>
<?php
	}
	add_action('js_inject', 'js_inject_treaty_details');

	$treaties = $page_data->get_treaties();
	$treaty = $page_data->treaty;
	$expand = get_request_variable('expand', 'str', 'treaty'); // or decisions
	$decisions_count = $page_data->get_decisions_count($page_data->treaty->id);
	$no_parties = 0;
	$parties = $page_data->get_parties($page_data->treaty->id);
	if(count($parties) > 0) {
		$no_parties = count($parties);
	} else if($treaty->number_of_parties > 0) {
		$no_parties = $treaty->number_of_parties;
	}

	$contact_data = $page_data->get_contacts();
	$countries_contacts = $contact_data['countries'];
	$all_contacts = $contact_data['contacts'];

	if($expand == 'sendmail') {
		$contact = $page_data->get_contact_for_id($id_contact);
	}

?>
<div class="left details-column-1">
	<div id="page-title">
		<div class="treaties-details-info">
			<div class="img">
				<img src="<?php echo $treaty->logo_medium; ?>" alt="<?php _e('Convention logo', 'informea'); ?>" title="<?php echo $treaty->short_title; ?> logo" />
			</div>
			<h1 class="left" id="treaties-details-short_title"><?php echo $treaty->short_title; ?></h1>
		</div>
		<div class="clear"></div>
		<?php if ($treaty->long_title) { ?>
		<h2 class="left"><?php echo $treaty->long_title; ?></h2>
		<?php } ?>
	</div>
	<div class="clear"></div>

	<form id="change-treaty" action="<?php echo bloginfo('url'); ?>/treaties" method="get">
		<select name="id_treaty" id="" class="column-select" onchange="var opt = $('#change-treaty option:selected'); if(opt.index() !== 0) { window.location = blog_dir + '/treaties/' + opt.attr('value'); }">
			<option value="">-- <?php _e('Select another instrument', 'informea'); ?> --</option>
		<?php
			foreach($treaties as $ltr) {
		?>
			<option value="<?php echo $ltr->odata_name; ?>">
				<?php echo $ltr->short_title; ?>
			</option>
		<?php
			}
		?>
		</select>
	</form>


<?php
	if ($no_parties > 0) {
?>
	<p>
		<span class="label"><?php _e('Number of parties', 'informea'); ?>:</span>
		<?php echo $no_parties; ?>
	</p>
<?php
	}
?>

	<?php if (!empty($treaty->theme)) { ?>
	<p>
		<span class="label"><?php _e('Theme', 'informea'); ?>:</span>
		<?php echo $treaty->theme; ?>
	</p>
	<?php } ?>
	<?php if ($treaty->abstract) { ?>
	<p>
		<span class="label"><?php _e('Abstract', 'informea'); ?>:</span>
		<?php
			$value = subwords($treaty->abstract, 30);
			echo '<span id="abstract_partial">';
			echo $value;
			if(strlen($value) < strlen($treaty->abstract)) { echo '<a href="#" onclick="abstract_collapse();">more &raquo;</a>'; }
			echo '</span>';
			if(strlen($value) < strlen($treaty->abstract)) {
				echo '<span id="abstract_full" style="display:none;">' . $treaty->abstract . '</span>';
			}
		?>
	</p>
	<?php } ?>

	<?php if (!empty($treaty->url)) { ?>
	<p>
		<span class="label"><?php _e('URL', 'informea'); ?>:</span>
		<a href="<?php echo $treaty->url; ?>" target="_blank" title="Visit convention website. This page will open in a new window">Visit convention website</a>
	</p>
	<?php } ?>
	<?php if (@date('Y', strtotime($treaty->start))) { ?>
	<p>
		<span class="label"><?php _e('Entry into force', 'informea'); ?>:</span>
		<?php echo (@date('Y', strtotime($treaty->start)) > 0) ? @date('Y', strtotime($treaty->start)) : '-'; ?>
	</p>
	<?php } ?>

	<?php if ($treaty->depository) { ?>
	<p>
		<span class="label"><?php _e('Depository', 'informea'); ?>:</span>
		<?php echo $treaty->depository; ?>
	</p>
	<?php } ?>


<?php
    if (!isset($cloud_items_nr)){
        $cloud_items_nr = 20;
    }

    if ($treaty->id){
	$cloud_terms = $page_data->get_popular_terms($treaty->id, $cloud_items_nr);
    }

if (!empty($page_data->tags)) {
	foreach ($page_data->tags as $tag) {
		$additional_cloud_items[$additional_cloud_items_nr] = new StdClass();
		$additional_cloud_items[$additional_cloud_items_nr]->text = $tag->term;
		$additional_cloud_items[$additional_cloud_items_nr]->url = get_bloginfo('url').'/terms/'.$tag->id;;
		$additional_cloud_items[$additional_cloud_items_nr]->popularity = 0;
		$additional_cloud_items_nr++;
	}
    }

    if (!empty($page_data->other_agreements)) {
	foreach ($page_data->other_agreements as $agr) {
		$additional_cloud_items[$additional_cloud_items_nr] = new StdClass();
	    $additional_cloud_items[$additional_cloud_items_nr]->text = $agr->short_title;
	    $additional_cloud_items[$additional_cloud_items_nr]->url = get_permalink() . '/' . $agr->id;
	    $additional_cloud_items[$additional_cloud_items_nr]->popularity = 0;
	    $additional_cloud_items_nr++;
	}
    }

	if ($page_data->agreement) {
		$additional_cloud_items[$additional_cloud_items_nr] = new StdClass();
		$additional_cloud_items[$additional_cloud_items_nr]->text = $page_data->agreement->short_title;
		$additional_cloud_items[$additional_cloud_items_nr]->url = get_permalink() . '/' . $page_data->agreement->id;
		$additional_cloud_items[$additional_cloud_items_nr]->popularity = 0;
		$additional_cloud_items_nr++;
	}
?>

<?php include(dirname(__FILE__) . '/../portlet.terms-cloud.inc.php'); ?>

</div>

<!-- Column 2 w/ tabs -->
<div class="left details-column-2">
	<div class="tab-menu">
		<ul>
			<li>
				<a class="tab<?php echo ($expand == 'treaty')?'-active':''; ?>"  href="<?php echo get_permalink() . '/' . $treaty->odata_name . '/treaty'; ?>"><?php _e('Treaty text', 'informea'); ?></a>
			</li>
			<li>
				<a class="tab<?php echo ($expand == 'decisions')?'-active':''; ?>" href="<?php echo get_permalink() . '/' . $treaty->odata_name . '/decisions'; ?>"><?php _e('Decisions', 'informea'); ?> (<?php echo $decisions_count; ?>)</a>
			</li>
			<li>
				<a class="tab<?php echo ($expand == 'nfp')?'-active':''; ?>"  href="<?php echo get_permalink() . '/' . $treaty->odata_name . '/nfp'; ?>"><?php _e('Focal Points', 'informea'); ?> (<?php echo count($all_contacts); ?>)</a>
			</li>
			<?php if($page_data->has_coverage()) { ?>
			<li>
				<a class="tab<?php echo ($expand == 'coverage')?'-active':''; ?>"  href="<?php echo get_permalink() . '/' . $treaty->odata_name . '/coverage'; ?>"><?php _e('Map and Membership', 'informea'); ?></a>
			</li>
			<?php } ?>
			<?php if($expand == 'sendmail') { ?>
			<li>
				<a class="tab<?php echo ($expand == 'sendmail')?'-active':''; ?>"  href=""><?php _e('Send Mail', 'informea'); ?> </a>
			</li>
			<?php } ?>
		</ul>
	</div>
<?php
	if($expand == 'treaty') {
		include(dirname(__FILE__) . '/details.tab.treaty.php');
	}
	if($expand == 'decisions') {
		include(dirname(__FILE__) . '/details.tab.decisions.php');
	}
	if($expand == 'nfp') {
		include(dirname(__FILE__) . '/details.tab.nfp.php');
	}
	if($expand == 'coverage') {
		include(dirname(__FILE__) . '/details.tab.coverage.php');
	}
	if($expand == 'sendmail') {
		include(dirname(__FILE__) . '/../sendmail/inc_sendmail.php');
	}
?>
</div>
<div class="clear"></div>
