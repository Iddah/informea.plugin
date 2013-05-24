<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('wp-ajax-response');

wp_register_style('jquery-ui-darkness', plugins_url('/informea/admin/css/ui-darkness/jquery-ui-1.7.3.custom.css'));
wp_enqueue_style('jquery-ui-darkness');

$page_countries = new imea_countries_page();
$treatyOb = new imea_treaties_page();
$terms = array();
$treaties = $page_data->get_enabled_treaties();
$countries = $page_countries->get_countries();
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
            jQuery('#image_img').attr('src', imgurl);
            jQuery('#image').val(imgurl);
            tb_remove();
        }
    });
</script>
<div class="wrap">
<div id="breadcrumb">
    You are here:
    <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_meetings">Manage meetings</a>
    &raquo;
    <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_meetings&act=event_add_event">Add new meeting</a>
</div>
<div id="icon-tools" class="icon32"><br></div>
<h2>Add new event</h2>
<?php if ($page_data->actioned) { ?>
    <div class="updated settings-error" id="setting-error-settings_updated">
        <?php if ($page_data->success) { ?>
            <strong>Event was successfully created!</strong>
        <?php } ?>
        <?php if (!$page_data->success) { ?>
            <strong>Error adding event!</strong>
            <ul>
                <?php foreach ($page_data->errors as $inpname => $inp_err) : ?>
                    <li><?php echo $inp_err; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php } ?>
    </div>
<?php } ?>

<form action="" method="post">
<?php wp_nonce_field('informea-admin_event_add_event'); ?>
<input type="hidden" name="page" value="informea_event"/>
<input type="hidden" name="act" value="event_add_event"/>

<table class="form-table">
<tr valign="top">
    <th scope="row">
        <label for="id_organization">Organization</label>
    </th>
    <td>
        <select id="id_organization" name="id_organization">
            <option value="">-- Please select --</option>
            <?php foreach ($treatyOb->get_organizations() as $row) :
                $sel = (!$page_data->success && get_request_int('id_organization') == $row->id) ? ' selected="selected"' : '';
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
        <label for="id_treaty">Treaty</label>
    </th>
    <td>
        <select id="id_treaty" name="id_treaty">
            <option value="">-- Please select --</option>
            <?php foreach ($treaties as $row) :
                $sel = (!$page_data->success && get_request_int('id_organization') == $row->id) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $row->id; ?>"<?php echo $sel; ?>><?php echo $row->short_title; ?></option>
            <?php endforeach; ?>
        </select>

        <p class="description">
            You must fill either Organization or Treaty!
        </p>

    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="event_url">URL</label>
    </th>
    <td>
        <input type="text" size="60" id="event_url" name="event_url"
               value="<?php if (!$page_data->success) {
                   echo $page_data->get_value('event_url');
               } ?>"/>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="title">Title *</label>
    </th>
    <td>
        <input type="text" size="60" id="title" name="title"
               value="<?php if (!$page_data->success) {
                   echo $page_data->get_value('title');
               } ?>"/>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="description">Description</label>
    </th>
    <td>
        <textarea name="description" cols="60" rows="5"
                  id="description"><?php if (!$page_data->success) {
                echo $page_data->get_value('description');
            }?></textarea>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="start">Start date *</label>
    </th>
    <td>
        <input type="text" size="60" id="start" name="start"
               value="<?php if (!$page_data->success) {
                   echo $page_data->get_value('start');
               } ?>"/>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="end">End date</label>
    </th>
    <td>
        <input type="text" size="60" id="end" name="end"
               value="<?php if (!$page_data->success) {
                   echo $page_data->get_value('end');
               } ?>"/>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="repetition">Repetition</label>
    </th>
    <td>
        <select id="repetition" name="repetition">
            <option value="">One time, only</option>
            <?php
            foreach ($page_data->get_repetition_enum() as $key => $value) {
                $selected = (!$page_data->success and get_request_value('repetition') == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="kind">Kind</label>
    </th>
    <td>
        <select id="kind" name="kind">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_kind_enum() as $key => $value) {
                $selected = (!$page_data->success and get_request_value('kind') == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="type">Type</label>
    </th>
    <td>
        <select id="type" name="type">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_type_enum() as $key => $value) {
                $selected = (!$page_data->success and get_request_value('type') == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="access">Access</label>
    </th>
    <td>
        <select id="access" name="access">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_access_enum() as $key => $value) {
                $selected = (!$page_data->success and get_request_value('access') == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="status">Status</label>
    </th>
    <td>
        <select id="status" name="status">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_status_enum() as $key => $value) {
                $selected = (!$page_data->success and get_request_value('status') == $key) ? ' selected="selected"' : '';
                ?>
                <option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="image">Image</label>
    </th>
    <td>
        <img id="image_img" src=""/>
        <input type="hidden" id="image" name="image" value=""/>
        <input type="button" id="upload_image_button" class="button" value="Select"/>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="image_copyright">Image Copyright</label>
    </th>
    <td>
        <input type="text" size="60" id="image_copyright" name="image_copyright"
               value="<?php if (!$page_data->success) {
                   echo $page_data->get_value('image_copyright');
               } ?>"/>
    </td>
</tr>
<tr>
    <td><label for="location">Event Location</label></td>
    <td>
        <input type="text" size="60" id="location" name="location"
               value="<?php if (!$page_data->success) {
                   echo $page_data->get_value('location');
               } ?>"/>

        <p class="description">
            Where event is taking place, like 221B Baker Street
        </p>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="city">City</label>
    </th>
    <td>
        <input type="text" size="60" id="city" name="city"
               value="<?php if (!$page_data->success) {
                   echo $page_data->get_value('city');
               } ?>"/>
    </td>
</tr>
<tr>
    <th scope="row">
        <label for="id_country">Country *</label>
    </th>
    <td>
        <select id="id_country" name="id_country">
            <option value="">-- Please select --</option>
            <?php
            foreach ($countries as $row) {
                $checked = (!$page_data->success and get_request_int('id_country') == $row->id) ? ' selected="selected"' : '';
                echo "<option value=\"{$row->id}\"$checked>{$row->name}</option>";
            }
            ?>
        </select>
    </td>
</tr>
</table>
<p class="submit">
    <input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Add event'); ?>"/>
</p>
</form>
</div>
