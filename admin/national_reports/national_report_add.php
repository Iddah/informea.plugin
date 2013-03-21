<?php
$id_treaty = get_request_int('id_treaty');
$page_events = new imea_events_page();
$treaties = $page_data->get_treaties();
$countries = $page_data->get_countries();
$events = array();
if (!empty($id_treaty)) {
    $events = $page_events->get_events($id_treaty);
}
?>
<link rel='stylesheet' href='<?php bloginfo('template_directory'); ?>/ui.css' type='text/css' media='screen'/>
<script src="<?php bloginfo('template_directory'); ?>/scripts/jquery-min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/ui.js"></script>
<script type="text/javascript">
    $().ready(function () {
        $('#submission').datepicker({'dateFormat': 'yy-mm-dd'});
    });
</script>

<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_national_reports">Manage national
            reports</a>
        &raquo;
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_national_reports&act=add_national_report">Add
            new national report</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Add new national report</h2>

    <p class="red">
        <em>Please fill in the details and press Add national report button</em>
    </p>
    <?php if ($page_data->actioned) { ?>
        <div class="updated settings-error" id="setting-error-settings_updated">
            <?php if ($page_data->success) { ?>
                <p><strong>National report was successfully added!</strong></p>
            <?php } ?>
            <?php if (!$page_data->success) { ?>
                <p><strong>Error adding national report!</strong>
                <ul>
                    <?php foreach ($page_data->errors as $inpname => $inp_err) {
                        echo "<li>$inpname : $inp_err</li>";
                    } ?>
                </ul>
                </p>
            <?php } ?>
        </div>
    <?php } ?>

    <form action="" method="post" id="sel_form">
        <?php wp_nonce_field('add_national_report'); ?>
        <input type="hidden" name="page" value="informea_national_reports"/>
        <input type="hidden" name="act" value="add_national_report"/>
        <table>
            <tr>
                <td><label for="id_treaty">Treaty *</label></td>
                <td>
                    <select id="id_treaty" name="id_treaty" onchange="document.getElementById('sel_form').submit();">
                        <option value="">-- Please select --</option>
                        <?php
                        foreach ($treaties as $row) {
                            $checked = (!$page_data->success and get_request_int('id_treaty') == $row->id) ? ' selected="selected"' : '';
                            echo "<option value=\"{$row->id}\"'$checked>{$row->short_title}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="id_country">Country *</label></td>
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
            <tr>
                <td><label for="id_event">Meeting</label></td>
                <td>
                    <select id="id_event" name="id_event">
                        <option value="">-- Please select --</option>
                        <?php
                        foreach ($events as $row) {
                            $checked = (!$page_data->success and get_request_int('id_event') == $row->id) ? ' selected="selected"' : '';
                            echo "<option value=\"{$row->id}\"$checked>{$row->title}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="title">Title *</label></td>
                <td>
                    <input type="text" size="60" id="title" name="title"
                           value="<?php if (!$page_data->success) {
                               echo $page_data->get_value('title');
                           } ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="submission">Submission date *</label></td>
                <td>
                    <input type="text" size="60" id="submission" name="submission"
                           value="<?php if (!$page_data->success) {
                               echo $page_data->get_value('submission');
                           } ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="document_url">Document URL *</label></td>
                <td>
                    <input type="text" size="60" id="document_url" name="document_url"
                           value="<?php if (!$page_data->success) {
                               echo $page_data->get_value('document_url');
                           } ?>"/>
                </td>
            </tr>
        </table>
        <p> * - Required field(s) </p>

        <p class="submit">
            <input name="actioned" type="submit" class="button-primary"
                   value="<?php esc_attr_e('Add national report'); ?>"/>
        </p>
    </form>
</div>
