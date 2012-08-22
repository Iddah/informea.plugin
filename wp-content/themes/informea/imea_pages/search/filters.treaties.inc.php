<div class="explorer_all_treaties_items">
<?php
	$count = 0;
	$themes = $search2->ui_get_treaties();
	foreach($themes as $theme => $treaties) {
		$checked = $search2->ui_is_checked_treaty('theme-' . $count);
?>
<ul class="main-list">
	<li>
		<input id="<?php echo slugify($theme); ?>" type="checkbox" <?php echo $checked ? 'checked="checked"' : '';?> class="explorer-treaty-click-children" />
		<label for="<?php echo slugify($theme); ?>"><?php echo $theme; ?></label>
		<ul class="sublist">
		<?php
			foreach($treaties as $id => $data) {
				$children = $data['children'];
				$title = $data['title'];
				$theme = $data['theme'];
				$checked = $search2->ui_is_checked_treaty($id) ? 'checked="checked"' : '';
		?>
				<li>
					<input type="checkbox" id="filter_treaty_<?php echo $id; ?>" name="q_treaty[]"
						value="<?php echo $id; ?>" <?php echo $checked; ?>  class="explorer-treaty-click-children" />
					<label for="filter_treaty_<?php echo $id; ?>">
						<?php echo $title; ?>
						<?php if($theme) { ?><span class="description" style="color: #777777 !important; font-size: 12px !important;"><?php echo '('.$theme.')'; ?></span><?php } ?>
					</label>
					<?php if(count($children)) { ?>
					<ul class="sublist">
					<?php
						foreach($children as $id => $child) {
							$title = $child['title'];
							$theme = $child['theme'];
							$checked = $search2->ui_is_checked_treaty($id) ? 'checked="checked"' : '';
					?>
						<li>
							<input type="checkbox" id="filter_treaty_<?php echo $id; ?>" name="q_treaty[]" value="<?php echo $id; ?>" <?php echo $checked; ?> class="explorer-treaty-click-children" />
							<label for="filter_treaty_<?php echo $id; ?>">
								<?php echo $title; ?>
								<?php if($theme) { ?><span class="description" style="color: #777777 !important; font-size: 12px !important;"><?php echo '('. $theme . ')'; ?></span><?php } ?>
							</label>
						</li>
					<?php } ?>
					</ul>
					<?php } ?>
				</li>
		<?php } ?>
		</ul>
	</li>
<?php
		if($count++ % 3 == 0) {
			echo '<div class="clear"></div>';
		}
?>
</ul>
<?php }  ?>
<ul>
	<li>
		<?php
			$ot = $search2->ui_get_other_treaties();
			$checked = '';
			foreach($ot as $id) {
				if($search2->ui_is_checked_treaty($id)) { $checked = 'checked="checked"'; break; }
			}
		?>

		<input id="explorer_other_treaties" type="checkbox" <?php echo $checked; ?> class="explorer-treaty-click-children" />
		<label for="explorer_other_treaties">Other<span class="description" style="color: #777777 !important; font-size: 12px !important;">(Regional, etc.)</span></label>
		<ul class="sublist">
			<li>
				<?php
					foreach($ot as $id) {
						$checked = $search2->ui_is_checked_treaty($id) ? 'checked="checked"' : '';
				?>
				<input type="checkbox" id="explorer_treaty_<?php echo $id; ?>" name="q_treaty[]" value="<?php echo $id; ?>" <?php echo $checked; ?> class="hidden" />
				<?php } ?>
			</li>
		</ul>
	</li>
</ul>
</div>
