<?php

function move_up_treaty_paragraph() {
    if (isset($_POST['action']) && $_POST['action'] == 'up') {
        $db = new imea_admin_database();
        $id_paragraph = intval($_POST['id_paragraph']);
        $db->swap_treaty_paragraphs($id_paragraph);
    }
}

function move_down_treaty_paragraph() {
    if (isset($_POST['action']) && $_POST['action'] == 'down') {
        $db = new imea_admin_database();
        $id_paragraph = intval($_POST['id_paragraph']);
        $db->swap_treaty_paragraphs($id_paragraph, FALSE);
    }
}

add_action('move_up_treaty_paragraph', 'move_up_treaty_paragraph');
add_action('move_down_treaty_paragraph', 'move_down_treaty_paragraph');
