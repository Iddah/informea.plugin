<?php
$id_national_plan = get_request_int('id_national_plan');
$id_country = get_request_int('id_country');
$page_meetings = new imea_meetings_page();
$treaties = $page_data->get_treaties();
$countries = $page_data->get_countries();
$meetings = array();
$plan = null;
if (!empty($id_national_plan)) {
    $plan = $page_data->get_national_plan($id_national_plan);
    $meetings = $page_meetings->get_meetings($plan->id_treaty);
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
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_national_plans">Manage national
            plans</a>
        &raquo;
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_national_plans&act=edit_national_plan">Edit
            national plan</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Edit national plan</h2>

    <p class="red">
        <em>Please fill in the details and press Save changes</em>
    </p>
    <?php if ($page_data->actioned) { ?>
        <div class="updated settings-error" id="setting-error-settings_updated">
            <?php if ($page_data->success) { ?>
                <p><strong>National plan was successfully updated!</strong></p>
            <?php } ?>
            <?php if (!$page_data->success) { ?>
                <p><strong>Error updating national plan!</strong>
                <ul>
                    <?php foreach ($page_data->errors as $inpname => $inp_err) {
                        echo "<li>$inpname : $inp_err</li>";
                    } ?>
                </ul>
                </p>
            <?php } ?>
        </div>
    <?php } ?>
    <form action="" method="post" id="edit_form">
        <?php wp_nonce_field('edit_national_plan'); ?>
        <input type="hidden" name="page" value="informea_national_plans"/>
        <input type="hidden" name="act" value="edit_national_plan"/>
        <input type="hidden" name="id_national_plan" value="<?php echo $plan->id; ?>"/>
        <input type="hidden" name="id_treaty" value="<?php echo $plan->id_treaty; ?>"/>
        <table>
            <tr>
                <td><label for="id_treaty">Treaty *</label></td>
                <td>
                    <select id="id_treaty" name="id_treaty" disabled="disabled">
                        <option value="">-- Please select --</option>
                        <?php
                        foreach ($treaties as $row) {
                            $checked = ($plan->id_treaty == $row->id) ? ' selected="selected"' : '';
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
                            $checked = ($plan->id_country == $row->id) ? ' selected="selected"' : '';
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
                        foreach ($meetings as $row) {
                            $checked = ($plan->id_event == $row->id) ? ' selected="selected"' : '';
                            echo "<option value=\"{$row->id}\"$checked>{$row->title}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="type">Type *</label></td>
                <td>
                    <select id="type" name="type">
                        <option value="">-- Please select --</option>
                        <?php
                        foreach ($page_data->get_national_plan_types() as $key => $row) {
                            $checked = ($plan->type == $key) ? ' selected="selected"' : '';
                            echo "<option value=\"{$key}\"$checked>{$row}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="title">Title *</label></td>
                <td>
                    <input type="text" size="60" id="title" name="title" value="<?php echo $plan->title; ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="submission">Submission date *</label></td>
                <td>
                    <input type="text" size="60" id="submission" name="submission"
                           value="<?php echo $plan->submission; ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="document_url">Document URL *</label></td>
                <td>
                    <input type="text" size="60" id="document_url" name="document_url"
                           value="<?php echo $plan->document_url; ?>"/>
                </td>
            </tr>
        </table>
        <p> * - Required field(s) </p>

        <p class="submit">
            <input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Save changes'); ?>"/>
        </p>
    </form>
</div>
