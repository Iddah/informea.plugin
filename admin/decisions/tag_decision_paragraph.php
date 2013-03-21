<?php
$id_treaty = get_request_value('id_treaty');
$id_decision = get_request_int('id_decision');
$decision = $page_data->get_decision($id_decision);
$paragraphs = $page_data->get_decision_paragraphs($id_decision);
$documents = $page_data->get_decision_documents($id_decision);
?>
<style type="text/css">
    #tdialog {
        display: none;
    }

    #tdialog_pc {
        border: 1px solid #C0C0C0;
        margin: 5px;
        padding: 5px;
        background-color: #F0F0F0;
    }

    #decision_content {
        font-size: 14px;
        line-height: 17px;
    }

    .tagged-paragraph {
        background-color: #FFC259;
    }
</style>
<script type="text/javascript">
    var imagePath = "<?php bloginfo('template_directory') ?>/images/";
    var id_decision = <?php echo $decision->id; ?>;
    var edit_decision_ajaxurl = ajaxurl + '?action=tag_decision_paragraph&_ajax_nonce=<?php echo wp_create_nonce("edit_decision_tags"); ?>';
    var delete_decision_paragraph_ajaxurl = ajaxurl + '?action=delete_decision_paragraph_ajaxurl&_wpnonce=<?php echo wp_create_nonce("delete_decision_tags"); ?>';
</script>

<script src="<?php bloginfo('template_directory'); ?>/scripts/jquery-min.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/ui.js" type="text/javascript"></script>

<script src="<?php bloginfo('template_directory'); ?>/scripts/dhtmlxtree/dhtmlxcommon.js"
        type="text/javascript"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/dhtmlxmenu/dhtmlxmenu.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/dhtmlxmenu/ext/dhtmlxmenu_ext.js"
        type="text/javascript"></script>
<script type="text/javascript" src="http://informea.org/wp-content/themes/informea/scripts/tipsy.js"></script>
<script src="<?php bloginfo('url'); ?>/wp-content/plugins/informea/admin/decisions/tag_decision_paragraph.js"
        type="text/javascript"></script>

<link rel="stylesheet" type="text/css"
      href="<?php bloginfo('template_directory'); ?>/scripts/dhtmlxmenu/skins/dhtmlxmenu_dhx_skyblue.css"/>
<link rel='stylesheet' href='<?php bloginfo('template_directory'); ?>/ui.css' type='text/css' media='screen'/>
<link rel="stylesheet" type="text/css" media="screen" href="http://informea.org/wp-content/themes/informea/tipsy.css"/>


<div id="tdialog">
    <h2>Tag this paragraph</h2>
    Paragraph to tag:
    <div id="tdialog_pc"></div>
    <br/>

    <form action="">
        <label for="keywords">Select tags from list below (use CTRL+click to select multiple tags):</label>
        <select id="keywords" name="keywords[]" size="12" multiple="multiple" style="height: 15em;">
            <?php
            $keywords = array();
            $terms = new Thesaurus();
            foreach ($terms->get_voc_concept() as $row) {
                $checked = (is_array($keywords) and in_array($row->id, $keywords)) ? ' selected="selected"' : '';
                echo "<option value='{$row->id}'$checked>{$row->term}</option>";
            }
            ?>
        </select>
        <input type="hidden" id="tdialog_para" name="tdialog_para" value=""/>
    </form>
</div>

<div class="wrap">
    <div id="breadcrumb">
        You are here:
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions">Manage decisions</a>
        &raquo;
        <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_edit_decision">Tag
            decision paragraphs</a>
    </div>
    <div id="icon-tools" class="icon32"><br></div>
    <h2>Tag decision paragraphs</h2>

    <p>
        On this page you can tag paragraphs of a decision. <span
            style="color:red">To tag entire decisions, please go <strong><a
                    href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_edit_tags">here</a></strong></span>.
    </p>

    <form action="" method="get" id="edit_form">
        <input type="hidden" name="page" value="informea_decisions"/>
        <input type="hidden" name="act" value="decision_edit_decision"/>

        <label for="id_treaty">Treaty *</label>
        <select id="id_treaty" name="id_treaty" onchange="document.getElementById('edit_form').submit();">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_treaties_w_decisions() as $row) {
                $selected = ($id_treaty == $row->id) ? ' selected="selected"' : '';
                echo "<option value='{$row->id}'$selected>{$row->short_title}</option>";
            }
            ?>
        </select>

        <div class="clear"></div>
        <label for="id_decision">Decision *</label>
        <select id="id_decision" name="id_decision" onchange="document.getElementById('edit_form').submit();">
            <option value="">-- Please select --</option>
            <?php
            foreach ($page_data->get_decisions_for_treaty($id_treaty) as $row) {
                $label = subwords($row->number . ' - ' . $row->short_title, 20);
                $selected = ($id_decision == $row->id) ? ' selected="selected"' : '';
                echo "<option value='{$row->id}'$selected>{$label}</option>";
            }?>
        </select>

        <br/>
        <?php if (!empty($decision)) { ?>
            <h2>Decision overview</h2>
            <table class="wp-list-table widefat fixed">
                <thead>
                <tr>
                    <th width="15%">Key</th>
                    <th>Value</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Title</td>
                    <td><?php echo $decision->short_title; ?></td>
                </tr>
                <tr>
                    <td>Type, Status</td>
                    <td>
                        <?php echo decode_decision_type($decision->type); ?>
                        , <?php echo decode_decision_status($decision->status); ?>
                    </td>
                </tr>
                <tr>
                    <td>Number</td>
                    <td><?php echo $decision->number; ?></td>
                </tr>
                <?php if (!empty($decision->event_title)) { ?>
                    <tr>
                        <td>Meeting</td>
                        <td><?php echo $decision->event_title; ?></td>
                    </tr>
                <?php } ?>
                <?php if (!empty($documents)) { ?>
                    <tr>
                        <td>Associated documents</td>
                        <td>
                            <?php
                            foreach ($documents as $doc) {
                                echo $page_data->document_icon_img($doc) . ' &nbsp;';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <h2>Decision content</h2>
            <?php
            $content = $page_data->get_decision_content_decorated($decision, $documents);
            if ($content != null) {
                ?>
                <span style="color:red">Warning: <strong>PLEASE DO NOT OVERLAP THE TAGGED PARAGRAPHS!</strong> (It will break all the tagging process for this decision)</span>
                <br/>
                <span><strong>Tip:</strong> Select the text with mouse then right-click to tag</span>
                <p id="decision_content">
                    <?php echo $content; ?>
                </p>
            <?php } else { ?>
                <span style="color:red;">
			The content for this decision could not be found because:
			<ul>
                <li>* There is no associated document in english for this decision</li>
                <li>* The file might contain scanned image - that contains no text - check the file</li>
                <li>* Something\'s wrong with the system</li>
            </ul>
			You could report this to the developers for investigations.</span>
            <?php } ?>
        <?php } ?>
    </form>
</div>
