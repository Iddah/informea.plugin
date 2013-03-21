<?php
$group = get_request_value('group', array('first_name', 'last_name'), false);
?>
<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp">Manage focal points</a>
        &raquo;
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp&act=duplicates">Duplicate focal
            points</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Duplicate focal points</h2>

    <p>
        From this page you can identify focal points. Although there is nothing to do for the moment with them, you can
        still try to synchronize their contact details manually
    </p>

    <form action="" method="get">
        <input type="hidden" name="page" value="informea_nfp"/>
        <input type="hidden" name="act" value="duplicates"/>
        Find records with same:
        <ul>
            <?php $checked = in_array('first_name', $group) ? 'checked="checked"' : ''; ?>
            <li><input type="checkbox" id="first_name" name="group[]"
                       value="first_name" <?php echo $checked; ?> /><label for="first_name">First name</label></li>
            <?php $checked = in_array('last_name', $group) ? 'checked="checked"' : ''; ?>
            <li><input type="checkbox" id="last_name" name="group[]" value="last_name" <?php echo $checked; ?> /><label
                    for="last_name">Last name</label></li>
            <?php $checked = in_array('email', $group) ? 'checked="checked"' : ''; ?>
            <li><input type="checkbox" id="email" name="group[]" value="email" <?php echo $checked; ?> /><label
                    for="email">Email</label></li>
        </ul>
        <input type="submit" name="actioned" class="button-primary" value="Search"/>
    </form>

    <?php
    if (count($duplicates)) {
        echo 'Duplicates found: ' . count($duplicates);
        echo '<table id="contacts" class="widefat fixed">
					<thead>
					<tr>
						<th class="prefix">Prefix</th>
						<th>First name</th>
						<th>Last name</th>
						<th>Position</th>
						<th>E-mail</th>
						<th>Duplicate IDs</th>
					</tr>
					</thead>
					<tbody>';
        foreach ($duplicates as $result) {
            $clones = $page_data->nfp_clones($result);
            ?>
            <tr>
                <td><?php echo $result->prefix; ?></td>
                <td><?php echo $result->first_name; ?></td>
                <td><?php echo $result->last_name; ?></td>
                <td><?php echo $result->position; ?></td>
                <td><?php echo $result->email; ?></td>
                <td>
                    <?php echo count($clones) ?> clone(s):
                    <?php
                    foreach ($clones as $clone) {
                        $t = $page_data->get_contact_treaties($clone);
                        ?>
                        <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_nfp&act=edit_nfp&id_treaty=<?php echo $t[0]; ?>&id_people=<?php echo $clone->id; ?>"><?php echo $clone->id; ?></a>
                    <?php } ?>
                </td>
            </tr>
        <?php
        }
        echo '</tbody></table>';
    } else {
        echo 'No duplicates!';
    }
    ?>
</div>
