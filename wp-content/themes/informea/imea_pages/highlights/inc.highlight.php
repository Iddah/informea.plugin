<?php
	if(isset($ni->image) && $show_img) {
		$img = '<img src="' . $ni->image . '" />';
	} else {
		$img = '<img src="' . get_bloginfo('template_directory') . '/images/pixel.gif' . '" style="width: 40px; height: 25px !important;" />';
	}
	$title = subwords($ni->title, 15);
?>

<li class="story">
	<table>
		<tr>
			<td><?php echo $img; ?></td>
			<td>
				<h4><?php echo $title; ?></h4>
				<p>
				<?php echo $summary; ?>
					<strong>
						<?php if(!empty($ni->source)) { ?><?php echo $ni->source; ?>, <?php } ?>
						<?php echo $ni->date_formatted; ?>
					</strong>
					<a <?php echo $target; ?>title="<?php _e('Click to read the full article on original website', 'informea'); ?>" href="<?php echo $permalink; ?>">more &raquo;</a>
				</p>
			</td>
		</tr>
	</table>
</li>
