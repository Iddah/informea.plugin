<?php
$mea_membership = array();
if($page_data->country !== NULL) {
	$mea_membership = $page_data->mea_membership;
}
if(count($mea_membership)) {
?>
	<table class="mea-membership">
		<thead>
			<th>&nbsp;</th>
			<th><?php _e('MEA', 'informea'); ?></th>
			<th class="align-right"><?php _e('Party since', 'informea'); ?></th>
			<th class="align-right"><?php _e('Status', 'informea'); ?></th>
			<th class="align-right"><?php _e('Observations', 'informea'); ?></th>
		</thead>
		<tbody>
		<?php
			foreach ($mea_membership as $count => $row) { ?>
			<tr class="<?php echo ($count % 2) ? 'odd' : 'even'; ?>">
				<td align="middle">
					<img src="<?php echo $row->logo_medium; ?>" />
				</td>
				<td>
					<a href="<?php bloginfo('url'); ?>/treaties/<?php echo $row->id; ?>">
						<?php echo $row->short_title; ?>
					</a>
				</td>
				<td class="align-right"><?php echo $row->year; ?></td>
				<td class="align-right">n.a.</td>
				<td class="align-right">-</td>
			</tr>
		<?php
			}
		?>
		</tbody>
	</table>
<?php
	}
?>

