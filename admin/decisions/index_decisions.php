<?php
$id_treaty = get_request_int('id_treaty');
$treatyOb = new imea_treaties_page();
$decisionOb = new imea_decisions_page();
?>
<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions">Manage decisions</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Manage decisions</h2>

    <p>
        On this page you can manage decisions
    </p>

    <h3>Actions</h3>
    <ul>
        <li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_add">Add
                new decision</a></li>
        <li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_delete">Delete
                decision</a></li>
        <li>
            <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_edit_decision">Tag
                decisions paragraphs</a></li>
        <li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_order">Order
                decisions</a></li>
    </ul>

    <h2>Decision Listing</h2>

    <form action="">
        <input type="hidden" name="page" value="informea_decisions"/>
        <select id="id_treaty" name="id_treaty" value="">
            <option value="">-- Please select treaty --</option>
            <?php
            foreach ($decisionOb->get_treaties_w_decisions() as $treaty) {
                $selected = $treaty->id == $id_treaty ? ' selected="selected"' : '';
                ?>
                <option
                    value="<?php echo $treaty->id; ?>"<?php echo $selected; ?>><?php echo $treaty->short_title; ?></option>
            <?php } ?>
        </select>
        <input type="submit" name="submit" value="Submit"/>
    </form>
    <?php if (!empty($id_treaty)) { ?>
        <br/>
        <table class="widefat fixed">
            <thead>
            <tr>
                <th width="40px">Edit</th>
                <th width="120px">Number</th>
                <th width="120px">Type</th>
                <th width="120px">Status</th>
                <th>Title</th>
                <th>Meeting</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($decisionOb->get_decisions_for_treaty($id_treaty) as $decision) { ?>
                <tr>
                    <td>
                        <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_edit&id_treaty=<?php echo $decision->id_treaty; ?>&id_decision=<?php echo $decision->id; ?>">Edit</a>
                    </td>
                    <td><?php echo $decision->number; ?></td>
                    <td><?php echo ucwords($decision->type); ?></td>
                    <td><?php echo ucwords($decision->status); ?></td>
                    <td><?php echo $decision->short_title; ?></td>
                    <td><?php echo $decision->cop_title; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
