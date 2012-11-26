<style>
#contacts th.edit {width: 50px;}
#contacts th.prefix {width: 50px;}
#contacts th.primary {width: 50px;}
</style>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_nfp">Manage focal points</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Manage national focal points</h2>
	<p>
		On this page you can MANUALLY manage national focal points
	</p>
	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p>
				<strong>The contact was removed successfully</strong>
			</p>
		<?php } else {?>
			<p>
				<strong>The contact was not removed. Please contact technical support</strong>
			</p>
		<?php } ?>
	</div>
	<?php } ?>
	<h3>Actions</h3>
	<ul>
		<li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp&act=add_nfp">Add new focal point</a></li>
		<li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp&act=edit_nfp">Edit focal point</a></li>
		<li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp&act=duplicates">Identify duplicate contacts</a></li>
	</ul>

	<form method="" action="">
		<input type="hidden" name="page" value="informea_nfp" />
		<input type="hidden" name="act" value="index_nfp" />
		<label for="text">Look for contact</label>
		<input type="text" id="text" name="text" value="<?php echo get_request_value('text'); ?>" />
		<input type="submit" class="button-primary" name="search" value="Search" />
	</form>
	<?php
		if(count($results)) {
			echo 'Focal points found: ' . count($results);
			echo '<table id="contacts" class="widefat fixed">
					<thead>
					<tr>
						<th class="edit">Edit</th>
						<th>Country</th>
						<th class="prefix">Prefix</th>
						<th>First name</th>
						<th>Last name</th>
						<th>Position</th>
						<th>Address</th>
						<th>E-mail</th>
						<th>Telephone</th>
						<th>Fax</th>
						<th class="primary">Primary</th>
						<th>Treaties</th>
					</tr>
					</thead>
					<tbody>';
			foreach($results as $result) {
				$treaties = $page_data->get_contact_treaties($result);
	?>
			<tr>
				<td>
					<?php if(count($treaties)) { ?>
					<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp&act=edit_nfp&id_treaty=<?php echo $treaties[0]; ?>&id_people=<?php echo $result->id; ?>">Edit</a>
					<?php } else { ?>
					<strong>ERROR - no treaty!</strong>
					<?php } ?>
				</td>
				<td><?php echo $result->country_name; ?></td>
				<td><?php echo $result->prefix; ?></td>
				<td><?php echo $result->first_name; ?></td>
				<td><?php echo $result->last_name; ?></td>
				<td><?php echo $result->position; ?></td>
				<td><?php echo $result->address; ?></td>
				<td><?php echo $result->email; ?></td>
				<td><?php echo $result->telephone; ?></td>
				<td><?php echo $result->fax; ?></td>
				<td><?php echo $result->is_primary == 1 ? 'yes' : 'no'; ?></td>
				<td>
					<?php
						$all_treaties = $page_data->get_treaties_indexed();
						foreach($treaties as $idx => $id_treaty) {
							$treaty = $all_treaties[$id_treaty];
							echo $treaty->short_title;
							if($idx < count($treaties) - 1) {
								echo ',';
							}
						?>
					<?php } ?>
				</td>
			</tr>
	<?php
			}
			echo '</tbody></table>';
		}
	?>
</div>
