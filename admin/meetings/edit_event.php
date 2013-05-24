<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('wp-ajax-response');

wp_register_style('jquery-ui-darkness', plugins_url('/informea/admin/css/ui-darkness/jquery-ui-1.7.3.custom.css'));
wp_enqueue_style('jquery-ui-darkness');

$event = NULL;
$id_treaty = get_request_int('id_treaty');
$id_organization = get_request_int('id_organization');
$id_event = get_request_value('id_event');
$treatyOb = new imea_treaties_page();
if ($id_event) {
    $event = $page_data->get_event($id_event);
}
$actioned = $page_data->actioned;
?>
<script type="text/javascript">
    jQuery().ready(function () {
        jQuery('#start').datepicker({'dateFormat': 'yy-mm-dd'});
        jQuery('#end').datepicker({'dateFormat': 'yy-mm-dd'});
        jQuery('#upload_image_button').click(function () {
            tb_show('', 'media-upload.php?post_id=&amp;type=image&TB_iframe=true');
            return false;
        });

        window.send_to_editor = function (html) {
            var imgurl = jQuery('img', html).attr('src');
            jQuery('#image').val(imgurl);
            tb_remove();
        }
    });
</script>
<div class="wrap">
<div id="breadcrumb">
    You are here:
    <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_meetings">Manage Meetings</a>
    &raquo;
    <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_meetings&act=event_edit_event">Edit Event</a>
</div>
<div id="icon-tools" class="icon32"><br></div>
<h2>Edit event</h2>

Select treaty, then event from the drop-down lists and edit the selected event.

<?php if ($page_data->actioned) { ?>
    <div class="updated settings-error" id="setting-error-settings_updated">
        <?php if ($page_data->success) { ?>
            <p><strong>Event was successfully updated!</strong></p>
        <?php } ?>
        <?php if (!$page_data->success) { ?>
            <strong>Error changing treaty!</strong>
            <ul>
                <?php foreach ($page_data->errors as $inpname => $inp_err) {
                    echo "<li>$inpname : $inp_err</li>";
                } ?>
            </ul>
        <?php } ?>
    </div>
<?php } ?>
<form action="" method="post" id="sel_form">
<?php wp_nonce_field('informea-admin_event_edit_event'); ?>
<input type="hidden" name="page" value="informea_event"/>
<input type="hidden" name="act" value="event_edit_event"/>

<table class="form-table">
<tr valign="top">
    <th scope="row">
        <label for="id_organization">Organization</label>
    </th>
    <td>
        <select id="id_organization" name="id_organization">
            <option value="">-- Please select --</option>
            <?php foreach ($treatyOb->get_organizations() as $row) :
                $sel = '';
                if (($page_data->actioned && ($id_organization == $row->id))
                    || (!$page_data->actioned && ($row->id == $event->id_organization))
                ) {
                    $sel = ' selected="selected"';
                }
                ?>
                <option value="<?php echo $row->id; ?>"<?php echo $sel; ?>><?php echo $row->name; ?></option>
            <?php endforeach; ?>
        </select>

        <p class="description">
            If you cannot find the organization, add new one <a
                href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_organization">here</a>.
        </p>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="id_treaty">Treaty *</label>
    </th>
    <td>
        <select id="id_treaty" name="id_treaty"
                onchange="document.getElementById('id_event').value = '';document.getElementById('sel_form').submit();">
            <option value="">-- Please select --</option>
            <?php foreach ($page_data->get_treaties_with_meetings() as $row) :
                $sel = '';
                if (($page_data->actioned && ($id_treaty == $row->id))
                    || (!$page_data->actioned && ($row->id == $event->id_treaty))
                ) {
                    $sel = ' selected="selected"';
                }
                ?>
                <option value="<?php echo $row->id; ?>"<?php echo $sel; ?>><?php echo $row->short_title; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="event_url">URL</label>
    </th>
    <td>
        <input type="text" size="60" id="event_url" name="event_url" value="<?php echo $event->event_url; ?>"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="title">Title *</label>
    </th>
    <td>
        <textarea name="title" cols="60" id="title"><?php echo $event->title;?></textarea>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="description">Description</label>
    </th>
    <td>
        <input type="text" size="60" id="description" name="description" value="<?php echo $event->description; ?>"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="long_title">Start date *</label>
    </th>
    <td>
        <input type="text" size="60" id="start" name="start" value="<?php echo $event->start; ?>"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="year">End date</label>
    </th>
    <td>
        <input type="text" size="60" id="end" name="end" value="<?php echo $event->end; ?>"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="repetition">Repetition</label>
    </th>
    <td>
        <select id="repetition" name="repetition">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_repetition_enum() as $key => $value) {
                $selected = ($event->repetition == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="kind">Kind</label>
    </th>
    <td>
        <select id="kind" name="kind">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_kind_enum() as $key => $value) {
                $selected = ($event->kind == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="type">Type</label>
    </th>
    <td>
        <select id="type" name="type">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_type_enum() as $key => $value) {
                $selected = ($event->type == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="access">Access</label>
    </th>
    <td>
        <select id="access" name="access">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_access_enum() as $key => $value) {
                $selected = ($event->access == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="status">Status</label>
    </th>
    <td>
        <select id="status" name="status">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_status_enum() as $key => $value) {
                $selected = ($event->status == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="image">Image</label>
    </th>
    <td>
        <input type="text" id="image" name="image" value="<?php echo $event->image; ?>"/>
        <input type="button" id="upload_image_button" class="button" value="Select"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="image_copyright">Image Copyright</label>
    </th>
    <td>
        <input type="text" size="60" id="image_copyright" name="image_copyright"
               value="<?php echo $event->image_copyright; ?>"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="location">Event Location</label>
    </th>
    <td>
        <input type="text" size="40" id="location" name="location" value="<?php echo $event->location; ?>"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="city">City</label>
    </th>
    <td>
        <input type="text" size="40" id="city" name="city" value="<?php echo $event->city; ?>"/>
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="id_country">Country</label>
    </th>
    <td>
        <select id="id_country" name="id_country">
            <option value="">-- Please select --</option>
            <?php foreach ($page_data->get_countries() as $row) {
                $checked = ($event->id_country == $row->id) ? ' selected="selected"' : '';
                echo "<option value='{$row->id}'$checked>{$row->name}</option>";
            } ?>
        </select>
    </td>
</tr>
</table>
<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Save changes'); ?>"
       style="float: left;"/>
<?php if ($page_data->can_delete($event)) { ?>
    <input name="delete" type="submit" class="button" value="<?php esc_attr_e('Delete this event'); ?>"
           onclick="return confirm('Are you sure?');"/>
<?php } ?>
</form>
</div>
