<?php
$id_treaty = get_request_value('id_treaty');
$id_treaty_article = get_request_value('id_treaty_article');
$id_treaty_article_paragraph = get_request_value('id_treaty_article_paragraph');
if ($id_treaty_article_paragraph) {
    $paragraph_row = $page_data->get_treaty_article_paragraph_row($id_treaty_article_paragraph);
    $id_treaty_article = $paragraph_row->id_treaty_article;
    $id_treaty = $page_data->get_treaty_id_from_article_id($id_treaty_article);
    $order = $paragraph_row->order;
} else {
    $paragraph_row = NULL;
}

if (($page_data->get_value('select_paragraph') and $paragraph_row !== NULL)
    or ($page_data->get_value('submit') and $page_data->success)
    or (!$page_data->get_value('select_paragraph') and !$page_data->get_value('submit') and $paragraph_row !== NULL)
) {
    $official_order = $paragraph_row->official_order;
    $indent = $paragraph_row->indent;
    $content = $paragraph_row->content;
    $keywords = $page_data->get_keywords_for_treaty_article_paragraph($id_treaty_article_paragraph);
} else {
    if ($page_data->get_value('submit') and !$page_data->success) {
        $official_order = $page_data->get_value('official_order');
        $indent = $page_data->get_value('indent');
        $content = $page_data->get_value('content');
        $keywords = $page_data->get_value('keywords');
    }
}

include (dirname(__FILE__) . '/../tinymce.inc.php');
?>
<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
        &raquo;
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_article_paragraph">Edit
            article paragraph</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Edit article paragraph</h2>

    <?php if (!$id_treaty_article_paragraph) { ?>
        <p>Select the paragraph you want to edit:</p>
    <?php } else { ?>
        <p>
            View the selected <a
                href="<?php echo $page_data->get_paragraph_url_in_treaty($id_treaty, $id_treaty_article, $id_treaty_article_paragraph); ?>">paragraph</a>.
        </p>
        <p>
            Change this paragraph.
            Please enter the details below and press the
            <b><?php esc_attr_e('Submit changes'); ?></b> button.
        </p>
    <?php } ?>

    <?php if ($page_data->actioned) { ?>
        <div class="updated settings-error" id="setting-error-settings_updated">
            <?php if ($page_data->success) { ?>
                <p><strong>Paragraph was successfully changed!</strong></p>
            <?php } ?>
            <?php if (!$page_data->success) { ?>
                <p><strong>Error changing paragraph!</strong>
                <ul>
                    <?php foreach ($page_data->errors as $inpname => $inp_err) {
                        echo "<li>$inpname : $inp_err</li>";
                    } ?>
                </ul>
                </p>
            <?php } ?>
        </div>
    <?php } ?>

    <form action="" method="post">
        <table>
            <tr>
                <td><label for="id_treaty_article_paragraph">Paragraph *</label></td>
                <td>
                    <select id="id_treaty_article_paragraph" name="id_treaty_article_paragraph">
                        <option value="">-- Please select --</option>
                        <?php foreach ($page_data->get_treaty_article_paragraph_in_treaty($id_treaty, $id_treaty_article) as $row) {
                            $checked = ($id_treaty_article_paragraph == $row->id) ? ' selected="selected"' : '';
                            $at = subwords($row->article_title, 10);
                            echo "<option value='{$row->id}'$checked>{$row->treaty_title} - {$at}, paragraph {$row->order}</option>";
                        } ?>
                    </select>
                    <input name="select_paragraph" type="submit" class="button-primary"
                           value="<?php esc_attr_e('select'); ?>"/>
                </td>
            </tr>
        </table>
        <p>
            * - Required field(s)
        </p>
    </form>

    <?php if ($id_treaty_article_paragraph) { ?>
    <form action="" method="post">
        <?php wp_nonce_field('informea-admin_treaty_edit_article_paragraph'); ?>
        <input type="hidden" name="page" value="informea_treaty"/>
        <input type="hidden" name="act" value="treaty_edit_article_paragraph"/>
        <input type="hidden" name="id_treaty_article_paragraph" value="<?php echo $id_treaty_article_paragraph; ?>"/>

        <table>
            <tr>
                <td><label for="order">Internal order (readonly)*</label></td>
                <td>
                    <input type="text" size="3" id="order" name="order" disabled="disabled"
                           value="<?php echo $order; ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="official_order">Order</label></td>
                <td>
                    <input type="text" size="10" id="official_order" name="official_order"
                           value="<?php echo $official_order; ?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="indent">Indent *</label></td>
                <td>
                    <select id="indent" name="indent">
                        <?php
                        foreach (array(1, 2, 3, 4) as $i) {
                            $checked = (intval($indent) == $i) ? ' selected="selected"' : '';
                            echo "<option value='{$i}'$checked>{$i}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="content">Content *</label>
                </td>
                <td>
                    <textarea rows="5" cols="40" id="content" name="content"
                        ><?php echo $content;?></textarea>
                </td>
            </tr>

            <tr>
                <td><label for="keywords">Keywords</label></td>
                <td>
                    <br/>
                    <em class="error">Use <strong>(Ctrl, Shift) + Click</strong> to select/deselect multiple item(s) and
                        range of terms</em>
                    <br/>
                    <select id="keywords" name="keywords[]" size="12" multiple="multiple"
                            style="height: 25em;">
                        <?php
                        foreach ($page_data->get_voc_concept() as $row) {
                            $checked = (is_array($keywords) and in_array($row->id, $keywords)) ? ' selected="selected"' : '';
                            echo "<option value='{$row->id}'$checked>{$row->term}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>

        </table>
        <p>
            * - Required field(s)
        </p>

        <p class="submit">
            <input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Submit changes'); ?>"/>
        </p>
        <?php } ?>
    </form>
    <?php if ($id_treaty_article_paragraph) { ?>
    <form action="" method="post">
        <?php wp_nonce_field('treaty_delete_paragraph'); ?>
        <input type="hidden" name="page" value="informea_treaty"/>
        <input type="hidden" name="act" value="treaty_delete_paragraph"/>
        <input type="hidden" name="id_paragraph" value="<?php echo $id_treaty_article_paragraph; ?>"/>
        <input name="delete" type="submit" class="button" value="<?php esc_attr_e('Delete'); ?>"
               onclick="return confirm('Delete cannot be undone. Are you sure?');"/>
        <?php } ?>
</div>
