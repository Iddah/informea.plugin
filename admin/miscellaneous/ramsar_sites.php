<h2>Import Ramsar sites from CSV dump</h2>

Steps:
<br />
1. Go to <a target="_blank" href="http://ramsar.wetlands.org/Database/Searchforsites/tabid/765/Default.aspx">http://ramsar.wetlands.org/Database/Searchforsites/tabid/765/Default.aspx</a>
<br />
2. Select all regions from the select control and press "Search"
<br />
3. On the results page press 'Export sites data' and save file on your computer as sites.csv
<br />
4. Upload the file in the form below, and press "Test" first to make a simulation
<br />
5. Upload the file again and press "Just do it!"

<p class="form">
<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="page" value="informea_miscellaneous" />
	<input type="hidden" name="act" value="import_ramsar_csv" />
	<input type="file" name="file" />

	<input type="submit" name="actioned" value="Test it" class="button" />
	<input type="submit" name="actioned" value="Just do it!" class="button-primary" />
</form>

<?php
	if($page_data->actioned && $page_data->success) {
		$errors = 0;
		foreach($page_data->sites as $site) {
			if($site->id_country == 'ERROR') {
				$errors++;
			}
		}


?>
		<h3>Erroneous data (<?php echo $errors; ?>)</h3>
		<table>
			<thead>
				<tr>
					<th style="width: 40px;">Id</th>
					<th>Site name</th>
					<th style="width: 250px;">Country</th>
					<th style="width: 100px;">Code</th>
					<th style="width: 100px;">Latitude</th>
					<th style="width: 100px;">Longitude</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($page_data->sites as $site) {
						if($site->id_country == 'ERROR') {
				?>
				<tr>
					<td><?php echo $site->id; ?></td>
					<td><?php echo $site->name; ?></td>
					<td><?php echo $site->country; ?></td>
					<td><?php echo $site->id_country; ?></td>
					<td><?php echo $site->latitude; ?></td>
					<td><?php echo $site->longitude; ?></td>
				</tr>
				<?php
						}
					}
				?>
			</tbody>
		</table>


		<h3>All data (<?php echo count($page_data->sites); ?>)</h3>

		<table>
			<thead>
				<tr>
					<th style="width: 40px;">Id</th>
					<th>Site name</th>
					<th style="width: 250px;">Country</th>
					<th style="width: 100px;">Code</th>
					<th style="width: 100px;">Latitude</th>
					<th style="width: 100px;">Longitude</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($page_data->sites as $site) {?>
				<tr>
					<td><?php echo $site->id; ?></td>
					<td><?php echo $site->name; ?></td>
					<td><?php echo $site->country; ?></td>
					<td><?php echo $site->id_country; ?></td>
					<td><?php echo $site->latitude; ?></td>
					<td><?php echo $site->longitude; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
<?php } ?>
</p>
