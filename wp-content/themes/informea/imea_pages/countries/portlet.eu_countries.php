<?php
	$eu_members = $page_data->get_eu_countries();
?>
<table>
	<caption><?php _e('Member countries'); ?></caption>
	<?php foreach($eu_members as $eu_member) { ?>
	<tr>
		<td>
			<a href="<?php echo get_permalink() . '/' . $eu_member->id ?>" title="Browse <?php echo $country->name; ?> profile">
				<img class="middle" src="<?php bloginfo('url');?>/<?php echo $eu_member->icon_medium; ?>" alt="" title="<?php echo $eu_member->name; ?>" align="middle" />
			</a>
		</td>
		<td>
			<a  href="<?php echo get_permalink() . '/' . $eu_member->id ?>"  title="Browse <?php echo $eu_member->name; ?> profile"><?php echo $eu_member->name;?></a>
		</td>
	</tr>
	<?php } ?>
</table>
