<?php
$id = get_request_int('id');
$org = $page_data->get_organization($id);
$countryOb = new imea_countries_page();
?>
<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
        &raquo;
        Edit organization
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Edit organization</h2>

    <p>
        Please enter the details below and press the <b>Add treaty</b> button

    <p>
        <?php if ($page_data->actioned) { ?>

    <div class="updated settings-error" id="setting-error-settings_updated">
        <?php if ($page_data->success) { ?>
            <p><strong>Organization was successfully updated!</p>
        <?php } ?>
        <?php if (!$page_data->success) { ?>
        <p><strong>Error updating organization!</strong>
        <ul>
            <?php
            foreach ($page_data->errors as $inpname => $inp_err) {
                echo "<li>$inpname : $inp_err</li>";
            } ?>
        </ul>
    </p>
    <?php } ?>
</div>
<?php } ?>

<form action="" method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('informea-admin_treaty_edit_organization'); ?>
    <input type="hidden" name="page" value="informea_treaty"/>
    <input type="hidden" name="act" value="treaty_edit_organization"/>
    <input type="hidden" name="id" value="<?php echo $id; ?>"/>

    <table>
        <tr>
            <td><label for="short_title">Name *</label></td>
            <td>
                <input type="text" size="60" id="name" name="name"
                       value="<?php echo $org->name; ?>"/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="description">Description</label>
            </td>
            <td>
                <textarea rows="5" cols="40" id="description" name="description"
                    ><?php echo $org->description;?></textarea>
            </td>
        </tr>
        <tr>
            <td><label for="address">Address</label></td>
            <td>
                <input type="text" size="60" id="address" name="address"
                       value="<?php echo $org->address; ?>"/>
            </td>
        </tr>
        <tr>
            <td><label for="city">City</label></td>
            <td>
                <input type="text" size="60" id="city" name="city"
                       value="<?php echo $org->city; ?>"/>
            </td>
        </tr>
        <tr>
            <td><label for="id_country">Country</label></td>
            <td>
                <select id="id_country" name="id_country">
                    <option value="">-- Please select --</option>
                    <?php
                    foreach ($countryOb->get_countries() as $c) {
                        $selected = $c->id == $org->id_country ? ' selected="selected"' : '';
                        ?>
                        <option value="<?php echo $c->id; ?>"<?php echo $selected; ?>><?php echo $c->name; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="url">URL</label></td>
            <td>
                <input type="text" size="60" id="url" name="url"
                       value="<?php echo $org->url; ?>"/>
            </td>
        </tr>
        <tr>
            <td><label for="depository">Depository</label></td>
            <td>
                <input type="text" size="60" id="depository" name="depository"
                       value="<?php echo $org->depository; ?>"/>
            </td>
        </tr>
    </table>
    <p>
        * - Required field(s)
    </p>

    <p class="submit">
        <input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Update'); ?>"/>
    </p>
</form>
</div>
