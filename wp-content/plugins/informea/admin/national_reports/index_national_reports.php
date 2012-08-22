<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_national_reports">Manage national reports</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Manage national reports</h2>
	<p>
		On this page you can MANUALLY manage national reports
	</p>
	<h3>Actions</h3>
	<ul>
		<li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_national_reports&act=add_national_report">Add new national report</a></li>
	</ul>

	<h3>Search/Edit national report</h3>
	<?php
		$id_treaty = get_request_int('id_treaty');
		$id_country = get_request_int('id_country');

		$treaties = $page_data->get_treaties();
		$countries = $page_data->get_countries();
	?>
	<form action="" method="get" id="edit_form">
		<input type="hidden" name="page" value="informea_national_reports" />
		<label for="id_treaty">Treaty *</label></td>
		<select id="id_treaty" name="id_treaty" onchange="document.getElementById('edit_form').submit();">
			<option value="">-- Please select --</option>
		<?php
			foreach($treaties as $row) {
				$checked = (!$page_data->success and get_request_int('id_treaty') == $row->id) ? ' selected="selected"' : '';
				echo "<option value=\"{$row->id}\"'$checked>{$row->short_title}</option>";
			}
		?>
		</select>
		<br />
		<label for="id_country">Country *</label></td>
		<select id="id_country" name="id_country" onchange="document.getElementById('edit_form').submit();">
			<option value="">-- Please select --</option>
			<?php
				foreach($countries as $row) {
					$checked = (!$page_data->success and get_request_int('id_country') == $row->id) ? ' selected="selected"' : '';
					echo "<option value=\"{$row->id}\"$checked>{$row->name}</option>";
				}
			?>
		</select>

		<?php
			$results = $page_data->filter_national_reports($id_treaty, $id_country);
		?>
	</form>
	<?php
		if(count($results)) {
			echo 'Reports found: ' . count($results);
			echo '<table id="reports" class="widefat fixed">
					<thead>
					<tr>
						<th class="edit">Edit</th>
						<th>Title</th>
						<th>Submission date</th>
					</tr>
					</thead>
					<tbody>';
			foreach($results as $result) {
	?>
			<tr>
				<td>
					<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_national_reports&act=edit_national_report&id_national_report=<?php echo $result->id; ?>">Edit</a>
				</td>
				<td><?php echo $result->title; ?></td>
				<td><?php echo $result->submission; ?></td>
			</tr>
	<?php
			}
			echo '</tbody></table>';
		} else { echo '<p>No reports found</p>'; }
	?>
</div>
