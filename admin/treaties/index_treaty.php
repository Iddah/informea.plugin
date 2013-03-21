<?php

$treatiesOb = new imea_treaties_page();
?>
<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Manage treaties</h2>

    <p>
        On this page you can add new articles and new paragraphs to an article.
    </p>

    <h3>Actions</h3>
    <ul>
        <li>
            <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_treaty">Add
                new treaty</a></li>
        <li>
            <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_article">Edit
                an article</a></li>
        <li>
            <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article_paragraph">Add
                new paragraph to an article</a></li>
        <li>
            <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_article_paragraph">Edit
                a paragraph</a></li>
    </ul>
    <p>
    <ul>
        <li><a href="#organizations">View organizations</a></li>
        <li>
            <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_organization">Add
                new organization</a></li>
    </ul>
    </p>

    <h2>Treaty</h2>

    <p>
        <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_treaty"
           class="button">Add new treaty</a>
    </p>
    <table class="widefat fixed">
        <thead>
        <tr>
            <th width="40px">Edit</th>
            <th width="100px">Articles</th>
            <th width="50px">Enabled</th>
            <th width="70px">Logo</th>
            <th>Short title</th>
            <th>Secretariat</th>
            <th width="80px">Region</th>
            <th width="50px">Primary</th>
            <th>Theme</th>
            <th>Secondary theme</th>
            <th>OData name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($treatiesOb->get_all_treaties_organizations() as $ob) { ?>
            <tr>
                <td>
                    <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_treaty&id=<?php echo $ob->id; ?>">Edit</a>
                </td>
                <td>
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article&id_treaty=<?php echo $ob->id; ?>"
                       class="button">Add article</a>
                </td>
                <td><?php echo $ob->enabled == 1 ? 'Yes' : 'No'; ?></td>
                <td>
                    <?php if (!empty($ob->logo_medium)) { ?>
                        <img src="<?php echo $ob->logo_medium; ?>"/>
                    <?php } ?>
                </td>
                <td><?php echo $ob->short_title; ?></td>
                <td><a href="" title="Click to edit this secretariat"><?php echo $ob->secretariat; ?></a></td>
                <td><?php echo $ob->region; ?></td>
                <td><?php echo $ob->primary == 1 ? 'Yes' : 'No'; ?></td>
                <td><?php echo $ob->theme; ?></td>
                <td><?php echo $ob->theme_secondary; ?></td>
                <td><?php echo $ob->odata_name; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>


    <h2>Secretariat / Organization</h2>
    <a name="organizations"></a>
    <table class="widefat fixed">
        <thead>
        <tr>
            <th width="40px">Edit</th>
            <th>Name</th>
            <th>Depository</th>
            <th>URL</th>
            <th>City</th>
            <th>Address</th>
            <th>Country</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($treatiesOb->get_organizations() as $org) { ?>
            <tr>
                <td>
                    <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_organization&id=<?php echo $org->id; ?>">Edit</a>
                </td>
                <td><?php echo $org->name; ?></td>
                <td><?php echo $org->depository; ?></td>
                <td width="50px"><a target="_blank" href="<?php echo $org->url; ?>">visit</a></td>
                <td><?php echo $org->city; ?></td>
                <td><?php echo $org->address; ?></td>
                <td><?php echo $org->country; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
