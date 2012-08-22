<?php
$id_treaty_events = NULL;
if (isset($treaty_ids)) {
    $mea_events = $page_data->get_meetings_for_ids($treaty_ids);
}
else{
    if(isset($treaty)) {
	    $mea_events = $page_data->get_meetings($treaty->id);
    } else {
	    $mea_events = $page_data->get_meetings();
    }
}
if(!empty($mea_events)) {
?>
<div class="portlet">
	<div class="pre-title">
		<div class="title">
			<span><?php
				if (!isset($replace_title_with)){
	    		    _e('MEA Events', 'informea');
	    		}
	    		else {
	    		    echo $replace_title_with;
	    		}

			?></span>
		</div>
	</div>
	<div class="content">
		<ul>
		<?php
			$count = 0;
			foreach($mea_events as $event) {
				$interval = show_event_interval($event);
				$image = !empty($event->image) ? $event->image : $event->logo_medium;
				$img_title = $event->treaty_short_title;
		?>
			<li>
			<?php if(!empty($image)) { ?>
				<img src="<?php echo $image; ?>" title="<?php echo esc_attr($img_title); ?>" />
			<?php } ?>
			<div class="item">
				<strong><?php echo $interval; ?></strong>
			<?php if(empty($event->event_url)) {
				echo $event->title;
				} else {
			?>
				<a target="_blank" href="<?php echo $event->event_url; ?>"><?php echo $event->title; ?></a>
			<?php
				}
			?>
			</div>
				<div class="clear"></div>
			</li>
		<?php
				$count++;
			}
		?>
		</ul>
		<a href="<?php echo bloginfo('url')."/events"?>" class="more-link">More events &raquo;</a>
	</div>
</div>
<?php
}
?>
