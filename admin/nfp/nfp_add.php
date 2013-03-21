<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp">Manage focal points</a>
        &raquo;
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp&act=add_nfp">Add new focal
            point</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Add new focal point</h2>

    <p>
        From this page you can add new national focal points
    </p>
    <?php if ($page_data->actioned) { ?>
        <div class="updated settings-error" id="setting-error-settings_updated">
            <?php if ($page_data->success) { ?>
                <p>
                    <strong>The contact was added successfully</strong>
                </p>
            <?php } else { ?>
                <p><?php var_dump($page_data->errors); ?></p>
            <?php } ?>
        </div>
    <?php } ?>
    <form action="" method="post" id="edit_form">
        <?php wp_nonce_field('add_nfp'); ?>
        <table>
            <tr>
                <td><label for="prefix">Prefix</label></td>
                <td><input type="text" id="prefix" name="prefix" value="<?php echo get_request_value('prefix'); ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="first_name">First name</label></td>
                <td><input type="text" id="first_name" name="first_name"
                           value="<?php echo get_request_value('first_name'); ?>"/></td>
            </tr>
            <tr>
                <td><label for="last_name">Last name</label></td>
                <td><input type="text" id="last_name" name="last_name"
                           value="<?php echo get_request_value('last_name'); ?>"/></td>
            </tr>
            <tr>
                <td><label for="position">Position</label></td>
                <td><input type="text" id="position" name="position" size="40"
                           value="<?php echo get_request_value('position'); ?>"/></td>
            </tr>
            <tr>
                <td><label for="institution">Institution</label></td>
                <td><input type="text" id="institution" name="institution" size="40"
                           value="<?php echo get_request_value('institution'); ?>"/></td>
            </tr>
            <tr>
                <td><label for="department">Department</label></td>
                <td><input type="text" id="department" name="department" size="40"
                           value="<?php echo get_request_value('department'); ?>"/></td>
            </tr>
            <tr>
                <td><label for="address">Address</label></td>
                <td><textarea type="text" id="address" name="address" size="40" rows="5"
                              cols="30"><?php echo get_request_value('address'); ?></textarea></td>
            </tr>
            <tr>
                <td><label for="email">E-mail</label></td>
                <td><input type="text" id="email" name="email" value="<?php echo get_request_value('email'); ?>"/></td>
            </tr>
            <tr>
                <td><label for="telephone">Telephone</label></td>
                <td><input type="text" id="telephone" name="telephone"
                           value="<?php echo get_request_value('telephone'); ?>"/></td>
            </tr>
            <tr>
                <td><label for="fax">Fax</label></td>
                <td><input type="text" id="fax" name="fax" size="40" value="<?php echo get_request_value('fax'); ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="is_primary">Primary NFP?</label></td>
                <td><input type="checkbox" id="is_primary"
                           name="is_primary" <?php echo get_request_boolean('is_primary') ? 'checked="checked"' : ''; ?> />
                </td>
            </tr>
            <tr>
                <td><label for="id_country">Country</label></td>
                <td>
                    <select id="id_country" name="id_country">
                        <option value="">-- Please select --</option>
                        <?php
                        foreach ($page_data->get_countries() as $c) {
                            $selected = (get_request_value('id_country') == $c->id) ? ' selected="selected"' : '';
                            echo "<option value='{$c->id}'$selected>{$c->name}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="treaty">Treaties</label></td>
                <td>
                    <?php
                    $treaties = get_request_value('treaty', array(), false);
                    foreach ($page_data->get_treaties() as $idx => $treaty) {
                        $checked = in_array($treaty->id, $treaties) ? 'checked="checked"' : '';
                        if ($idx % 4 == 0) {
                            echo '<br />';
                        }
                        ?>
                        <input type="checkbox" id="treaty-<?php echo $treaty->id; ?>" name="treaty[]"
                               value="<?php echo $treaty->id; ?>" <?php echo $checked; ?> />
                        <label for="treaty-<?php echo $treaty->id; ?>"><?php echo $treaty->short_title; ?></label>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        </table>
        <input type="submit" class="button-primary" name="actioned" value="Create focal point"/>
    </form>
</div>
