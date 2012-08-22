<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_activity_log">User activity log</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>User activity log</h2>
    <p>
        <?php if($limit == 'all') { ?>
        Showing all entries.
        Click <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_activity_log&limit=<?php echo $limit;?>&order=<?php echo $order;?>&asc=<?php echo $ascension; ?>&limit=100">here</a> to show only last 100 entries.
        <?php } else { ?>
        Showing latest 100 entries.
        Click <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_activity_log&limit=<?php echo $limit;?>&order=<?php echo $order;?>&asc=<?php echo $ascension; ?>&limit=all">here</a> to show full log (it may be very long).
        <?php } ?>
    </p>
    <table class="widefat fixed">
        <thead>
            <tr>
                <th width="10%">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_activity_log&limit=<?php echo $limit;?>&order=username&asc=<?php echo $ascension_rev; ?>&limit=<?php echo $limit; ?>">Username</a>
                </th>
                <th width="10%">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_activity_log&limit=<?php echo $limit;?>&order=rec_created&asc=<?php echo $ascension_rev; ?>&limit=<?php echo $limit; ?>">Time</a>
                </th>
                <th width="5%">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_activity_log&limit=<?php echo $limit;?>&order=operation&asc=<?php echo $ascension_rev; ?>&limit=<?php echo $limit; ?>">Operation</a>
                </th>
                <th width="5%">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_activity_log&limit=<?php echo $limit;?>&order=section&asc=<?php echo $ascension_rev; ?>&limit=<?php echo $limit; ?>">Section</a>
                </th>
                <th width="5%">Link</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($records as $record) { ?>
            <tr>
                <td><?php echo $record->username; ?></td>
                <td><?php echo mysql2date('d/n/Y H:i:s', $record->rec_created); ?></td>
                <td><?php echo $record->operation; ?></td>
                <td><?php echo $record->section; ?></td>
                <td>
                    <?php if(!empty($record->url)) { ?>
                        <a title="View the current item on the website" href="<?php echo $record->url; ?>">View</a>
                    <?php } else { echo '&nbsp;'; } ?>
                </td>
                <td><pre><?php echo esc_attr($record->description); ?></pre></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
