<?php // $peblds - external variable coming from $page_data->get_peblds_data() ?>
PEBLDS stands for Pan-European Biological and Landscape Diversity Strategy.
For more details, please visit <a href="http://www.peblds.org" target="_blank">http://www.peblds.org</a>.
<br />
<?php if(!empty($peblds->projects)) { ?>
	<div class="peblds_entity">
	<h2>Projects</h2>
	<ul>
	<?php foreach($peblds->projects as $project) { ?>
		<li>
			<h4>
				<?php echo $project->title; ?>
			</h4>
			<a href="http://peblds.localhost/countries/<?php echo $country->code; ?>/" title="View online version on www.peblds.org website" target="_blank">View on www.peblds.org</a>
			<br />
			<?php echo date_interval($project->start_date, $project->end_date); ?>
			<table>
				<tbody>
					<tr>
						<td>Budget</td>
						<td><?php echo $project->budget; ?></td>
					</tr>
					<tr>
						<td>Objective</td>
						<td><?php echo $project->objective; ?></td>
					</tr>
					<tr>
						<td>Description</td>
						<td><?php echo $project->description; ?></td>
					</tr>
					<tr>
						<td>Contact</td>
						<td>
							<?php echo $project->contact; ?>
							<br />
							<?php echo $project->contact_details; ?>
						</td>
					</tr>
					<tr>
						<td>Outcome</td>
						<td><?php echo $project->outcome; ?></td>
					</tr>
					<tr>
						<td>Funding agency</td>
						<td><?php echo $project->funding_agency; ?></td>
					</tr>
					<tr>
						<td>Covered countries</td>
						<td>
						<?php
							$c = count($project->countries);
							foreach($project->countries as $idx => $l_country) {
								$url = get_bloginfo('url') . '/countries/' . $l_country->id;
								$furl = get_bloginfo('url') . '/' . $l_country->icon_medium;
								echo "<a href='{$url}'><img src='{$furl}' />{$l_country->name}</a> ";
							}
						?>
						</td>
					</tr>
				</tbody>
			</table>
		</li>
	<?php } ?>
	</ul>
	</div>
<?php } ?>

<?php if(!empty($peblds->best_practices)) { ?>
	<div class="peblds_entity">
	<h2>Best practice</h2>
	<ul>
	<?php foreach($peblds->best_practices as $practice) { ?>
		<li>
			<h4>
				<?php echo $practice->title; ?>
			</h4>
			<a href="http://peblds.localhost/countries/<?php echo $country->code; ?>/" title="View online version on www.peblds.org website" target="_blank">View on www.peblds.org</a>
			<table>
				<tbody>
					<tr>
						<td>Topic</td>
						<td><?php echo $practice->topic->name; ?></td>
					</tr>
					<tr>
						<td>Treaty</td>
						<td>
							<img src="<?php echo $practice->treaty->logo_medium; ?>" alt="<?php echo $practice->treaty->short_title; ?> logo" /><?php echo $practice->treaty->short_title; ?>
						</td>
					</tr>
					<tr>
						<td>Content</td>
						<td><?php echo $practice->content; ?></td>
					</tr>
					<tr>
						<td>Submitted</td>
						<td><?php echo format_mysql_date($practice->submitted); ?></td>
					</tr>
					<tr>
						<td>Contact</td>
						<td>
							<?php echo $practice->contact; ?>
							<br />
							<?php echo $practice->contact_details; ?>
						</td>
					</tr>
					<tr>
						<td>Documents</td>
						<td>
						<?php
							$c = count($practice->files);
							foreach($practice->files as $idx => $l_file) {
								echo "<a href='{$l_file->url}'>{$l_file->filename}</a> ";
								if($idx < $c - 1) { echo ', '; }
							}
						?>
						</td>
					</tr>
				</tbody>
			</table>
		</li>
	<?php } ?>
	</ul>
	</div>
<?php } ?>

<?php if(!empty($peblds->technical_reports)) { ?>
	<div class="peblds_entity">
	<h2>Technical reports</h2>
	<ul>
	<?php foreach($peblds->technical_reports as $report) { ?>
		<li>
			<h4>
				<?php echo $report->title; ?>
			</h4>
			<a href="http://peblds.localhost/countries/<?php echo $country->code; ?>/" title="View online version on www.peblds.org website" target="_blank">View on www.peblds.org</a>
			<table>
				<tbody>
					<tr>
						<td>Treaty</td>
						<td>
							<img src="<?php echo $report->treaty->logo_medium; ?>" alt="<?php echo $report->treaty->short_title; ?> logo" /><?php echo $report->treaty->short_title; ?>
						</td>
					</tr>
					<tr>
						<td>Topics</td>
						<td>
						<?php
							$c = count($report->topics);
							foreach($report->topics as $idx => $l_topic) {
								echo $l_topic->name;
								if($idx < $c - 1) { echo ', '; }
							}
						?>
						</td>
					</tr>
					<tr>
						<td>Submitted</td>
						<td><?php echo format_mysql_date($report->submitted); ?></td>
					</tr>
					<tr>
						<td>Documents</td>
						<td>
						<?php
							$c = count($report->files);
							foreach($report->files as $idx => $l_file) {
								echo "<a href='{$l_file->url}'>{$l_file->filename}</a> ";
								if($idx < $c - 1) { echo ', '; }
							}
						?>
						</td>
					</tr>
				</tbody>
			</table>
		</li>
	<?php } ?>
	</ul>
	</div>
<?php } ?>
