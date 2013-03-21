<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('wp-ajax-response');

wp_register_style('jquery-ui-darkness', plugins_url('/informea/admin/css/ui-darkness/jquery-ui-1.7.3.custom.css'));
wp_enqueue_style('jquery-ui-darkness');

$id_decision = get_request_int('id_decision');
$id_treaty = get_request_int('id_treaty');
$id_organization = get_request_int('id_organization');
$id_meeting = get_request_int('id_meeting');
$treatyOb = new imea_treaties_page();
$decisionOb = new imea_decisions_page();
$eventsOb = new imea_events_page();
$languages = array('en' => 'English', 'fr' => 'French', 'ar' => 'Arabic', 'es' => 'Spanish', 'ru' => 'Russian', 'zh' => 'Chinese');

$decision = $decisionOb->get_decision($id_decision);
$documents = $decisionOb->get_decision_documents($id_decision);
$keywords = $page_data->get_decision_tags($id_decision);
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        <?php if(empty($id_treaty)) { ?>
        jQuery('#id_treaty').change(function () {
            jQuery('#add_decision').submit();
        });
        <?php } ?>
        jQuery('#published').datepicker({dateFormat: 'yy-mm-dd', maxDate: "today"});
        jQuery('#updated').datepicker({dateFormat: 'yy-mm-dd', maxDate: "today"});
    });
</script>
<style type="text/css">
    #id_meeting {
        max-width: 400px;
    }
</style>
<div id="breadcrumb">
    You are here:
    <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions">Manage decisions</a>
    &raquo;
    Edit decision
</div>
<div id="icon-tools" class="icon32"><br></div>
<h2>Edit decision</h2>
<div class="wrap nosubsub">
    <p>
        From this page you add new decisions into database. Fill in details below and add decision into the database
    </p>
    <?php
    include(dirname(__FILE__) . '/../operation.html.php');
    if ($page_data->actioned && $page_data->success) {
        echo '<p><a href="' . get_bloginfo('url') . '/wp-admin/admin.php?page=informea_decisions&act=decision_add">Add new decision</a></p>';
    }
    ?>
    <div class="form-wrap">
        <form id="edit_decision" action="" method="post" enctype="multipart/form-data" class="validate">
            <?php wp_nonce_field('decision_edit'); ?>
            <input type="hidden" name="id_decision" value="<?php echo $decision->id; ?>"/>

            <div id="col-container">
                <?php if (!empty($id_treaty)) { ?>
                    <div id="col-right">

                        <div class="form-field">
                            <div style="float: left">
                                <label for="published">Published *</label>
                                <input type="text" id="published" name="published"
                                       value="<?php echo $decision->published; ?>" style="width: 150px;"/>

                                <p>Date when decision was published by the Convention (YYYY-MM-DD)</p>
                            </div>

                            <div style="float: left; margin-left: 15px;">
                                <label for="updated">Updated</label>
                                <input type="text" id="updated" name="updated" value="<?php echo $decision->updated; ?>"
                                       style="width: 150px;"/>

                                <p>(YYYY-MM-DD)</p>
                            </div>
                        </div>

                        <div class="clear"></div>

                        <div class="form-field">
                            <label for="long_title">Long title</label>
                            <input type="text" id="long_title" name="long_title"
                                   value="<?php echo $decision->long_title; ?>"/>
                        </div>

                        <div class="form-field">
                            <label for="summary">Summary</label>
                            <textarea id="summary" name="summary" rows="5"
                                      cols="20"><?php echo $decision->summary; ?></textarea>
                        </div>

                        <div class="form-field">
                            <label for="body">Text</label>
                            <textarea id="body" name="body" rows="15"
                                      cols="20"><?php echo $decision->body; ?></textarea>
                        </div>

                        <h3>Documents</h3>
                        <em><strong>Note</strong> You cannot edit documents. You can download them from here:</em>
                        <?php if (count($documents)) { ?>
                            <?php
                            foreach ($documents as $i => $doc) {
                                ?>
                                <a href="<?php echo get_bloginfo('url'); ?>/download?entity=decision_document&id=<?php echo $doc->id; ?>"><?php echo $doc->filename; ?></a>
                                <?php
                                if ($i < count($documents) - 1) {
                                    echo ',';
                                }
                            } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div id="col-left">
                    <div class="form-field">
                        <label for="id_organization">Select organization</label>
                        <select id="id_organization" name="id_organization" tabindex="1">
                            <option value="">-- Please select --</option>
                            <?php
                            foreach ($treatyOb->get_organizations() as $row) {
                                $sel = '';
                                if (($page_data->actioned && ($id_organization == $row->id))
                                    || (!$page_data->actioned && ($row->id == $event->id_organization))
                                ) {
                                    $sel = ' selected="selected"';
                                }
                                $sel = $row->id == $id_organization ? ' selected="selected"' : '';
                                ?>
                                <option
                                    value="<?php echo $row->id; ?>"<?php echo $sel; ?>><?php echo $row->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-field form-required">
                        <label for="id_treaty">Select treaty *</label>
                        <select id="id_treaty" name="id_treaty">
                            <option value="">-- Please select --</option>
                            <?php
                            foreach ($treatyOb->get_treaties() as $treaty) {
                                $selected = $treaty->id == $id_treaty ? ' selected="selected"' : '';
                                ?>
                                <option
                                    value="<?php echo $treaty->id; ?>"<?php echo $selected; ?>><?php echo $treaty->short_title; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <?php if (!empty($id_treaty)) { ?>
                        <div class="form-field">
                            <label for="link">Link</label>
                            <input type="text" id="link" name="link" value="<?php echo $decision->link; ?>"/>

                            <p>Link to online version of the decision, on the convention's website</p>
                        </div>

                        <div class="form-field form-required">
                            <label for="short_title">Short title *</label>
                            <input type="text" id="short_title" name="short_title"
                                   value="<?php echo $decision->short_title; ?>"/>
                        </div>

                        <div class="form-field form-required">
                            <div style="float: left">
                                <label for="number">Number *</label>
                                <input type="text" id="number" name="number" value="<?php echo $decision->number; ?>"
                                       style="width: 200px;"/>
                            </div>

                            <div style="float: left">
                                <label for="decision_type">Type *</label>
                                <select id="decision_type" name="decision_type">
                                    <option value="">-- Please select --</option>
                                    <?php
                                    foreach (array('decision', 'resolution', 'recommendation') as $key) {
                                        $selected = $decision->type == $key ? ' selected="selected"' : '';
                                        ?>
                                        <option
                                            value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo ucfirst($key); ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div style="float: left">
                                <label for="status">Status *</label>
                                <select id="status" name="status">
                                    <option value="">-- Please select --</option>
                                    <?php
                                    foreach ($decisionOb->get_allowed_status() as $key) {
                                        $selected = $decision->status == $key ? ' selected="selected"' : '';
                                        ?>
                                        <option
                                            value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo ucfirst($key); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="clear"></div>
                        <div class="form-field">
                            <label for="id_meeting">Meeting</label>
                            <select id="id_meeting" name="id_meeting" tabindex="9">
                                <option value="">-- Please select --</option>
                                <?php foreach ($decisionOb->get_meetings_add_decision() as $label => $group) : ?>
                                    <optgroup label="<?php echo $label; ?>">
                                        <?php foreach ($group as $event) :
                                            $selected = $event->id == $decision->id_meeting ? ' selected="selected"' : '';
                                            if (($page_data->actioned && ($id_meeting == $row->id))
                                                || (!$page_data->actioned && ($event->id == $decision->id_meeting))
                                            ) {
                                                $selected = ' selected="selected"';
                                            }
                                            ?>
                                            <option
                                                value="<?php echo $event->id; ?>"<?php echo $selected; ?>><?php echo $event->title; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>

                            <p>(If meeting is missing, add it <a
                                    href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_events&act=event_add_event">here</a>,
                                then add decision)</p>
                        </div>

                        <div class="form-field">
                            <label for="keywords">Keywords</label>
                            <br/>
                            <em class="error">Use <strong>(Ctrl, Shift) + Click</strong> to select/deselect multiple
                                item(s) and range of terms</em>
                            <br/>
                            <select id="keywords" name="keywords[]" size="12" multiple="multiple" style="height: 25em;">
                                <?php
                                $terms = new Thesaurus(null);
                                foreach ($terms->get_voc_concept() as $row) {
                                    $checked = array_key_exists(intval($row->id), $keywords) ? ' selected="selected"' : '';
                                    echo "<option value='{$row->id}'$checked>{$row->term}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <!--/col-left -->
            </div>
            <!--/col-container -->
            <?php if (!empty($id_treaty)) { ?>
                <p class="submit">
                    <input type="submit" name="actioned" value="Update decision" class="button button-primary"/>
                </p>
            <?php } ?>
        </form>
    </div>
    <!--/form-wrap -->

</div>
