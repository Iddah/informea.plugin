<?php
$pictures = $page_data->get_pictures(0, 0, 1);
$treaties = $page_data->get_treaties();
$copyright = get_request_value('copyright');
$title = get_request_value('title');
$keywords = get_request_value('keywords');
$id_treaty = get_request_value('id_treaty');
?>
<style type="text/css">
    .field {
        float: left;
        width: 100px;
    }

    .clear {
        clear: both;
    }
</style>
<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_pictures">Manage pictures</a>
        &raquo;
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_pictures&act=pict_highlight_image">Manage
            news pictures</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Manage news pictures</h2>

    <?php if ($page_data->actioned) { ?>
        <div class="updated settings-error" id="setting-error-settings_updated">
            <?php
            if ($page_data->success) {
                $copyright = NULL;
                $title = NULL;
                $keywords = NULL;
                $id_treaty = NULL;
                ?>
                <strong>Picture was successfully uploaded!</strong> <a
                    href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_pictures&act=pict_highlight_image">Add
                    another one!</a>
            <?php } else { ?>
                <strong>Error uploading picture!</strong>
                <ul>
                    <?php
                    foreach ($page_data->errors as $inpname => $inp_err) {
                        echo "<li>$inpname : $inp_err</li>";
                    } ?>
                </ul>
            <?php } ?>
        </div>
    <?php } ?>

    <h3>Upload new picture</h3>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="page" value="informea_pictures"/>
        <input type="hidden" name="act" value="pict_highlight_image"/>
        <input type="hidden" name="is_slider" value="0"/>
        <input type="hidden" name="is_highlight_thumbnail" value="0"/>
        <input type="hidden" name="is_highlight_image" value="1"/>

        <div class="field"><strong>Select file</strong>:</div>
        <input type="file" name="picture"/> (required)

        <br class="clear"/>

        <div class="field"><strong>Treaty</strong>:</div>
        <select name="id_treaty">
            <option value="">-- Please select --</option>
            <?php foreach ($treaties as $treaty) { ?>
                <option
                    value="<?php echo $treaty->id; ?>"<?php if ('' . $treaty->id == $id_treaty) {
                    echo ' selected="$copyright"';
                } ?>><?php echo $treaty->short_title; ?></option>
            <?php } ?>
        </select>
        (required)

        <br class="clear"/>
        <br class="clear"/>

        <div class="field">Copyright:</div>
        <input type="text" name="copyright" size="60"<?php if (!empty($copyright)) {
            echo " value=\"$copyright\"";
        } ?> />
        (optional)
        <br class="clear"/>

        <div class="field">Title:</div>
        <input type="text" name="title" size="60"<?php if (!empty($title)) {
            echo " value=\"$title\"";
        } ?> /> (optional)
        <br/>

        <div class="field">Image tags:</div>
        <input type="text" name="keywords" size="60"<?php if (!empty($keywords)) {
            echo " value=\"$keywords\"";
        } ?> />
        (optional, comma separated)
        <br/>
        <br/><input class="button-primary" type="submit" name="upload" value="Upload"/>
        <em class="text-red"><strong>Important</strong>: Image must be exactly <strong>300</strong> pixels wide by
            <strong>300</strong> pixels height!</em>
    </form>

    <br/>

    <?php if (count($pictures) > 0) { ?>
        <h3>Manage existing pictures</h3>
        <form action="" method="post"
              onsubmit="return confirm('Are you sure you want to delete selected picture(s)? This cannot be undone!');">
            <input class="button-primary" type="submit" name="delete" value="Delete selected pictures"/>
            <input type="hidden" name="page" value="informea_pictures"/>
            <input type="hidden" name="act" value="pict_highlight_image"/>
            <br/>
            <br/>
            <table class="widefat wide">
                <thead>
                <tr>
                    <th width="1%">&nbsp;</th>
                    <th>Id</th>
                    <th>Filename</th>
                    <th>Treaty</th>
                    <th>Title</th>
                    <th>Copyright</th>
                    <th>Tags</th>
                    <th>Picture</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count = 0;
                foreach ($pictures as $key => $picture) {
                    ?>
                    <tr<?php echo ($count % 2 == 0) ? ' class="alternate"' : ''; ?>">
					<td><?php echo $key + 1; ?></td>
					<td><input id="picture-<?php echo $picture->id; ?>" type="checkbox" name="picture[]"
                               value="<?php echo $picture->id; ?>"/></td>
					<td><label for="picture-<?php echo $picture->id; ?>"><?php echo $picture->filename; ?></label></td>
					<td><?php echo $picture->treaty_title; ?></td>
					<td><?php echo $picture->title; ?></td>
					<td><?php echo $picture->copyright; ?></td>
					<td><?php echo $picture->keywords; ?></td>
					<td><img
                            src="<?php bloginfo('url'); ?>/wp-content/uploads/pictures/highlight_image/<?php echo $picture->filename; ?>"/>
                    </td>
				</tr>
			<?php
					$count++;
				}
                ?>
                </tbody>
            </table>
            <br/><input class="button-primary" type="submit" name="delete" value="Delete selected pictures"/>
        </form>
    <?php } ?>
</div>
