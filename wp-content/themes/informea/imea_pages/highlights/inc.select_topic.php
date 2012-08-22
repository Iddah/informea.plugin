<form action="" class="right">
	<select name="topic" onchange="var url = this.options[this.selectedIndex].value; if(url != '') { window.location = url; }">
		<option value="">-- Change topic here --</option>
	<?php
		foreach($page_data->non_empty_categories as $c) {
	?>
		<option value="<?php bloginfo('url'); ?>/highlights/<?php echo $c->slug; ?>"><?php echo $c->title; ?></option>
	<?php
		}
	?>
	</select>
</form>
<div class="clear"></div>
