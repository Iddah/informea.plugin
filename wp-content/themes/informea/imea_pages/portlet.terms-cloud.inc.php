<?php
if(!isset($cloud_terms)){
	$cloud_terms = array();
}

if(isset($additional_cloud_items)){
	$cloud_terms = $additional_cloud_items+$cloud_terms;
}
if(!empty($cloud_terms)) {
?>
<div class="tags">
	<ul>
	<?php
		$cloud_terms = $page_data->compute_popularity($cloud_terms);
		shuffle($cloud_terms);
		foreach($cloud_terms as $index => $cloud_term) {
			if (isset($cloud_term->tag)){
	?>
		<li>
			<a class="tag<?php echo $cloud_term->popularity; ?>" href="<?php bloginfo('url'); ?>/terms/<?php echo $cloud_term->id; ?>"><?php echo $cloud_term->tag; ?></a>
		</li>
<?php
			}
		}
?>
	</ul>
</div>
<?php } ?>
