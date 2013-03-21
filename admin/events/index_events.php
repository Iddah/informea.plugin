<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_events">Manage events</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Manage events</h2>

    <p>
        On this page you can add new MEA meetings.
    </p>

    <h3>Actions</h3>
    <ul>
        <li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_events&act=event_add_event">Add
                new event</a></li>
    </ul>
</div>

<?php
$eventOb = new imea_events_page();

?>

<table class="widefat fixed">
    <thead>
    <tr>
        <th width="3%">&nbsp;</th>
        <th width="50%">Title</th>
        <th>Organization</th>
        <th>Treaty</th>
        <th>Start</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($eventOb->list_events_admin() as $row) : ?>
        <tr>
            <td>
                <a href="<?php echo admin_url('/admin.php?page=informea_events&act=event_edit_event&id_event=' . $row->id); ?>">Edit</a>
            </td>
            <td>
                <?php echo $row->title;?>
            </td>
            <td>
                <?php echo $row->name;?>
            </td>
            <td>
                <?php echo $row->short_title;?>
            </td>
            <td>
                <?php echo format_mysql_date($row->start); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>