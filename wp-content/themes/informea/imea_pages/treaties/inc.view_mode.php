		<div class="view-mode">
			<form action="">
				<label for="view-mode"><?php _e('View', 'informea'); ?></label>
				<select id="view-mode" name="view-mode" onchange="window.location = $(this).val();">
					<?php $selected = ($expand == 'icon') ? 'selected="selected "' : ''; ?>
					<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/treaties/"><?php _e('Icon', 'informea'); ?></option>
					<?php $selected = ($expand == 'grid') ? 'selected="selected "' : ''; ?>
					<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/treaties/grid"><?php _e('Grid', 'informea'); ?></option>
					<?php $selected = ($expand == 'list') ? 'selected="selected "' : ''; ?>
					<option <?php echo $selected;?>value="<?php bloginfo('url'); ?>/treaties/list"><?php _e('List', 'informea'); ?></option>
				</select>
			</form>
		</div>
		<div class="clear"></div>
