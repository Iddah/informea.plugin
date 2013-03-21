<?php
if (!class_exists('DB_Base')) {
    class DB_Base {
        public $actioned = FALSE;
        public $success = FALSE;
        public $errors = array();
        public $insert_id = NULL;

        /**
         * Do the security check for forms and echo unauthorized message if not correct
         * @param $nonce_field Nonce field, see Wordpress nonce definition, http://codex.wordpress.org/WordPress_Nonces
         * @return TRUE if security is OK
         */
        function _security_check($nonce_field) {
            if (!check_admin_referer($nonce_field)) {
                echo('<p>You are not authorized to access this page</p>');
                return FALSE;
            }
            return TRUE;
        }

        /**
         * Insert a record inside ai_activity_log table.
         * @param string $operation Possible values for operation (insert, update or delete)
         * @param string $section Section affected by the user, for example: vocabulary, treaty, decision etc. Do not invent new sections if some already exist. Look first.
         * @param string $username User that created the action (WordPress username)
         * @param string $description Description of the operation, for example: "Added tags a, b, c to article 'Article 2', paragraph 4". Send something meaningful and readable.
         * @param string $link Link to online version of the affected entity (if available).
         * @return boolean TRUE if successs, FALSE otherwise
         */
        function add_activity_log($operation, $section, $description, $username = NULL, $link = NULL) {
            global $wpdb;
            global $current_user;

            if ($username !== NULL) {
                $user = $username;
            } else {
                $user = $current_user->user_login;
            }
            $wpdb->insert('ai_activity_log', array(
                    'operation' => $operation,
                    'section' => $section,
                    'username' => $user,
                    'description' => $description,
                    'url' => $link
                )
            );
        }

        /**
         * Retrieve request POST parameter
         * @param name of the parameter
         * @return parameter value or empty string if not set
         */
        function get_value($name, $strip_slashes = FALSE) {
            $ret = isset($_POST[$name]) ? $_POST[$name] : NULL;
            if ($strip_slashes) {
                $ret = stripslashes($ret);
            }
            return $ret;
        }

        /**
         * Echo the request parameter
         * @param name of the parameter
         * @return Nothing
         */
        function get_value_e($name, $strip_slashes = FALSE) {
            echo $this->get_value($name, $strip_slashes);
        }
    }
}



if (!class_exists('imea_admin_database')) {
    require_once(dirname(__FILE__) . '/formvalidator.php');
    /**
     * Manage database operations for the administration interface.
     */
    class imea_admin_database {
        public $actioned = FALSE;
        public $success = FALSE;
        public $errors = array();
        public $insert_id = NULL;

        /**
         * Access voc_source
         * @return Rows from the table
         */
        function get_voc_source() {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM voc_source ORDER BY name");
        }

        /**
         * Access voc_concept
         * @return Rows from the table
         */
        function get_voc_concept() {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM voc_concept WHERE id_source = 9 ORDER BY term");
        }

        /**
         * Retrive the list of terms filtering the one sent as parameter.
         * @param $id of term to be filtered out
         * @return Rows from the table voc_concept
         */
        function get_voc_concept_lists($term) {
            global $wpdb;
            $sql = $wpdb->prepare('SELECT * FROM voc_concept WHERE id_source = 9 AND id <> %d ORDER BY term', $term);
            return $wpdb->get_results($sql);
        }

        /**
         * Get the term object from the table
         * @param $id ID of the term
         * @return Row from the table or NULL if not found
         */
        function get_concept($id) {
            global $wpdb;
            $sql = $wpdb->prepare('SELECT * FROM voc_concept WHERE id = %d', $id);
            return $wpdb->get_row($sql);
        }

        /**
         * Retrieve the terms related to this term.
         * @param $term ID of the term
         * @return List with IDs of the related terms
         */
        function get_related_terms($term) {
            global $wpdb;
            return $wpdb->get_col($wpdb->prepare("SELECT a.target_term FROM voc_relation a
			INNER JOIN voc_relation_type b ON a.relation = b.id
			WHERE a.id_concept = %d AND b.identification = 'related'", $term));
        }

        /**
         * Retrieve the terms narrower to this term.
         * @param $term ID of the term
         * @return List with IDs of the narrower terms
         */
        function get_narrower_terms($term) {
            global $wpdb;
            return $wpdb->get_col($wpdb->prepare("SELECT a.target_term FROM voc_relation a
			INNER JOIN voc_relation_type b ON a.relation = b.id
			WHERE a.id_concept = %d AND b.identification = 'narrower'", $term));
        }

        /**
         * Retrieve the terms broader to this term.
         * @param $term ID of the term
         * @return List with IDs of the broader terms
         */
        function get_broader_terms($term) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT a.target_term FROM voc_relation a INNER JOIN voc_relation_type b ON a.relation = b.id WHERE a.id_concept = ${term} AND b.identification = 'broader'");
            return $wpdb->get_col($sql);
        }


        /**
         * Access ai_treaty
         * @return Rows from the table
         */
        function get_treaties() {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM ai_treaty WHERE enabled=1 ORDER BY short_title");
        }

        /**
         * Access ai_country
         * @return Rows from the table
         */
        function get_countries() {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM ai_country ORDER BY name");
        }

        /**
         * Access ai_treaty
         * @return Rows from the table
         */
        function get_all_treaties() {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM ai_treaty ORDER BY short_title");
        }

        /**
         * Access ai_treaty
         * @return a row from the table
         */
        function get_treaty($id_treaty) {
            global $wpdb;
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM ai_treaty WHERE id = %d", $id_treaty));
        }

        /**
         * Access ai_treaty
         * @return primary treaty for a certain organization
         */
        function get_primary_treaty($id_organization) {
            global $wpdb;
            return $wpdb->get_var($wpdb->prepare('SELECT id FROM ai_treaty WHERE ai_treaty.primary = 1 AND id_organization = %d;', $id_organization));
        }

        /**
         * Access ai_treaty_vocabulary
         * @param $id_treaty ID of the treaty
         * @return all keywords associated with a treaty
         */
        function get_keywords_for_treaty($id_treaty) {
            global $wpdb;
            return $wpdb->get_col($wpdb->prepare('SELECT id_concept FROM ai_treaty_vocabulary WHERE id_treaty = %d;', $id_treaty));
        }

        /**
         * Access ai_treaty_article
         * @return id of the coresponding treaty
         */
        function get_treaty_id_from_article_id($id_treaty_article) {
            global $wpdb;
            return $wpdb->get_var($wpdb->prepare("SELECT id_treaty FROM ai_treaty_article WHERE id = %d", $id_treaty_article));
        }

        /**
         * Access ai_treaty_article
         * @return Rows from the table
         */
        function get_treaty_article() {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM ai_treaty_article ORDER BY title");
        }

        /**
         * Access ai_treaty_article
         * @return a row from the table
         */
        function get_treaty_article_row($id_treaty_article) {
            global $wpdb;
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM ai_treaty_article WHERE id = %d", $id_treaty_article));
        }

        /**
         * Access ai_treaty_article_vocabulary
         * @param $id_treaty_article ID of the article
         * @return all keywords associated with a article
         */
        function get_keywords_for_treaty_article($id_treaty_article) {
            global $wpdb;
            return $wpdb->get_col($wpdb->prepare('SELECT id_concept FROM ai_treaty_article_vocabulary WHERE id_treaty_article = %d', $id_treaty_article));
        }

        /**
         * Access ai_treaty_article joined with ai_treaty
         * @param $id_treaty ID of the treaty or NULL if for all treaties
         * @return Rows from the table
         */
        function get_treaty_article_in_treaty($id_treaty) {
            global $wpdb;
            if ($id_treaty == NULL) {
                $sql = $wpdb->prepare("SELECT a.id, a.order, a.title, b.short_title AS treaty_title
									FROM ai_treaty_article a
									INNER JOIN ai_treaty b ON a.id_treaty = b.id
									WHERE b.`enabled` = 1
									ORDER BY b.short_title, a.order, a.title");
            } else {
                $sql = $wpdb->prepare("SELECT a.id, a.order, a.title, b.short_title AS treaty_title
									FROM ai_treaty_article a
									INNER JOIN ai_treaty b ON a.id_treaty = b.id
									WHERE b.id = %d AND b.`enabled` = 1
									ORDER BY b.short_title, a.order, a.title", $id_treaty);
            }
            return $wpdb->get_results($sql);
        }


        /**
         * Access ai_treaty_article_paragraph
         * @param $id_treaty_article id of the article
         * @return order value for a new article paragraph
         */
        function get_next_treaty_article_paragraph_order($id_treaty_article) {
            global $wpdb;
            $result = $wpdb->get_var($wpdb->prepare("SELECT MAX(ai_treaty_article_paragraph.order) FROM ai_treaty_article_paragraph WHERE id_treaty_article = %d;", $id_treaty_article));
            if (!$result) {
                $result = 0;
            }

            return $result + 1;
        }

        /**
         * Access ai_treaty_article_paragraph to get the paragraph with a specific order
         * @param $id_treaty_article id of the article
         * @param $order order of the article in the treaty
         * @return order value for a new article
         */
        function get_treaty_article_paragraph_with_order($id_treaty_article, $order) {
            global $wpdb;
            return $wpdb->get_var($wpdb->prepare("SELECT id FROM ai_treaty_article_paragraph WHERE id_treaty_article = %d AND ai_treaty_article_paragraph.order = %d;", $id_treaty_article, $order));
        }

        /**
         * Access ai_treaty_article joined with ai_treaty and ai_treaty_article_paragraph
         * @param $id_treaty id of the treaty or NULL for all treaties
         * @param $id_treaty_article id of the article or NULL for all articles
         * @return Rows from the table
         */
        function get_treaty_article_paragraph_in_treaty($id_treaty, $id_treaty_article) {
            global $wpdb;
            if ($id_treaty_article !== NULL) {
                $sql = $wpdb->prepare("SELECT ai_treaty_article_paragraph.id, ai_treaty_article_paragraph.order, " .
                    "	ai_treaty_article.order as article_order, ai_treaty_article.title as article_title," .
                    "	ai_treaty.short_title as treaty_title " .
                    "FROM ai_treaty_article " .
                    "	JOIN ai_treaty ON ai_treaty_article.id_treaty = ai_treaty.id " .
                    "	JOIN ai_treaty_article_paragraph ON ai_treaty_article.id = ai_treaty_article_paragraph.id_treaty_article " .
                    "	WHERE ai_treaty_article.id = %d " .
                    "ORDER BY ai_treaty.short_title, ai_treaty_article.order, ai_treaty_article.title, ai_treaty_article_paragraph.order", $id_treaty_article);
            } else {
                if ($id_treaty !== NULL) {
                    $sql = $wpdb->prepare("SELECT ai_treaty_article_paragraph.id, ai_treaty_article_paragraph.order, " .
                        "	ai_treaty_article.order as article_order, ai_treaty_article.title as article_title," .
                        "	ai_treaty.short_title as treaty_title " .
                        "FROM ai_treaty_article " .
                        "	JOIN ai_treaty ON ai_treaty_article.id_treaty = ai_treaty.id " .
                        "	JOIN ai_treaty_article_paragraph ON ai_treaty_article.id = ai_treaty_article_paragraph.id_treaty_article " .
                        "	WHERE ai_treaty.id = %d " .
                        "ORDER BY ai_treaty.short_title, ai_treaty_article.order, ai_treaty_article.title, ai_treaty_article_paragraph.order", $id_treaty);
                } else {
                    $sql = $wpdb->prepare("SELECT ai_treaty_article_paragraph.id, ai_treaty_article_paragraph.order, " .
                        "	ai_treaty_article.order as article_order, ai_treaty_article.title as article_title," .
                        "	ai_treaty.short_title as treaty_title " .
                        "FROM ai_treaty_article " .
                        "	JOIN ai_treaty ON ai_treaty_article.id_treaty = ai_treaty.id " .
                        "	JOIN ai_treaty_article_paragraph ON ai_treaty_article.id = ai_treaty_article_paragraph.id_treaty_article " .
                        "ORDER BY ai_treaty.short_title, ai_treaty_article.order, ai_treaty_article.title, ai_treaty_article_paragraph.order");
                }
            }
            return $wpdb->get_results($sql);
        }

        /**
         * Access ai_treaty_article_paragraph
         * @return a row from the table
         */
        function get_treaty_article_paragraph_row($id_treaty_article_paragraph) {
            global $wpdb;
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM ai_treaty_article_paragraph WHERE id = %d", $id_treaty_article_paragraph));
        }

        /**
         * Swap the given paragraph with previous or next from the same treaty article.
         * @param id_paragraph ID of the paragraph
         * @param up If TRUE, move this $id_paragraph up (swap with previous). If FALSE, mov this $id_paragraph down (swap with next).
         */
        function swap_treaty_paragraphs($id_paragraph, $up = TRUE) {
            global $wpdb;
            @mysql_query("BEGIN", $wpdb->dbh);

            $current = $wpdb->get_row($wpdb->prepare("SELECT * FROM ai_treaty_article_paragraph WHERE id = %d", $id_paragraph));
            $other_order = ($up == TRUE) ? $current->order - 1 : $current->order + 1;
            $sql = $wpdb->prepare("SELECT * FROM ai_treaty_article_paragraph WHERE id_treaty_article = %d AND `order` = %d", $current->id_treaty_article, $other_order);
            $other = $wpdb->get_row($sql);

            if ($other !== NULL) {
                $success = $wpdb->update('ai_treaty_article_paragraph', array('order' => $other->order, 'indent' => $other->indent), array('id' => $current->id));
                if ($success != 1) {
                    imea_log("Error updating row:" . $wpdb->last_query);
                }
                $success = $wpdb->update('ai_treaty_article_paragraph', array('order' => $current->order, 'indent' => $current->indent), array('id' => $other->id));
                if ($success != 1) {
                    imea_log("Error updating row:" . $wpdb->last_query);
                }
                @mysql_query("COMMIT", $wpdb->dbh);
            } else {
                imea_log("[swap_treaty_paragraphs] Cannot find the other article before this one, ID={$current->id}, other order = {$other_order} ($sql)", true);
                @mysql_query("ROLLBACK", $wpdb->dbh);
            }
        }

        /**
         * Access ai_treaty_article_paragraph_vocabulary
         * @param $id_treaty_article_paragraph ID of the article
         * @return all keywords associated with a paragraph
         */
        function get_keywords_for_treaty_article_paragraph($id_treaty_article_paragraph) {
            global $wpdb;
            return $wpdb->get_col($wpdb->prepare('SELECT id_concept FROM ai_treaty_article_paragraph_vocabulary WHERE id_treaty_article_paragraph = %d;', $id_treaty_article_paragraph));
        }

        /**
         * Access ai_decision
         * @return Rows from the table
         */
        function get_decision() {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM ai_decision ORDER BY short_title");
        }

        function get_decision_tags($id_decision) {
            global $wpdb;
            if (!empty($id_decision)) {
                return $wpdb->get_col("SELECT id_concept FROM ai_decision_vocabulary WHERE id_decision = $id_decision");
            }
            return array();
        }


        /**
         * Save tags for decision
         */
        function tag_decision() {
            if ($this->_security_check('informea-admin_decision_tag_decision')) {
                global $wpdb;
                $this->success = TRUE;
                @mysql_query("BEGIN", $wpdb->dbh);
                $id_decision = get_request_value('id_decision');
                if ($id_decision !== NULL) {
                    $this->actioned = TRUE;
                    try {
                        $wpdb->query("DELETE FROM ai_decision_vocabulary WHERE id_decision = $id_decision");
                        if (isset($_POST['keywords'])) {
                            foreach ($_POST['keywords'] as $keyword) {
                                $keywords_result = $wpdb->insert('ai_decision_vocabulary', array(
                                        'id_decision' => $id_decision,
                                        'id_concept' => intval($keyword),
                                    )
                                );
                                if ($keywords_result == FALSE) {
                                    break;
                                }
                            }
                            $this->success = $keywords_result !== FALSE;
                        }
                        if ($this->success) {
                            @mysql_query("COMMIT", $wpdb->dbh);
                            // Log the action
                            $decision = $wpdb->get_row("SELECT * FROM ai_decision WHERE id = $id_decision");
                            $url = sprintf('%s/treaties/%d/decisions?showall=true#decision-%d', get_bloginfo('url'), $decision->id_treaty, $id_decision);
                            $this->add_activity_log('update', 'decision', "Tagged decision <strong>{$decision->number} - {$decision->short_title}</strong>", null, $url);
                        } else {
                            $this->errors = array('DB' => $wpdb->last_error);
                            @mysql_query("ROLLBACK", $wpdb->dbh);
                        }
                    } catch (Exception $e) {
                        $this->success = FALSE;
                        @mysql_query("ROLLBACK", $wpdb->dbh);
                        return FALSE;
                    }
                }
                return $this->success;
            }
        }


        /**
         * Access ai_decision_paragraph
         * @return order value for a new decision paragraph
         */
        function get_next_decision_paragraph_order($id_decision) {
            global $wpdb;
            $result = $wpdb->get_var($wpdb->prepare("SELECT MAX(ai_decision_paragraph.order) FROM ai_decision_paragraph WHERE id_decision = %d;", $id_decision));
            if (!$result) {
                $result = 0;
            }

            return $result + 1;
        }

        /**
         * Access ai_decision_paragraph
         * @return list of paragraphs for a decision
         */
        function get_decision_paragraphs($id_decision) {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM ai_decision_paragraph WHERE id_decision = $id_decision");
        }

        /**
         * Access ai_decision_paragraph & ai_decision_paragraph_vocabulary
         * @return a paragraph
         */
        function get_decision_paragraph_by_id($id_paragraph) {
            global $wpdb;
            $ret = $wpdb->get_results("SELECT * FROM ai_decision_paragraph WHERE id = $id_paragraph");
            $ret[0]->keywords = $wpdb->get_col("SELECT id_concept FROM ai_decision_paragraph_vocabulary WHERE id_decision_paragraph = $id_paragraph");
            return $ret[0];
        }

        /**
         * Validate the validate_treaty_edit_article form
         * @return TRUE If form successfully validated
         */
        function validate_treaty_edit_article() {
            $this->actioned = TRUE;
            if ($this->_security_check('informea-admin_treaty_edit_article')) {
                $val = new FormValidator();
                $val->addValidation("id_treaty_article", "req", "Please select an article");
                /// $val->addValidation("title", "req", "Please fill in the title");
                $valid = $val->ValidateForm();
                if (!$valid) {
                    $this->errors = $val->GetErrors();
                }
                return $valid;
            }
            return FALSE;
        }

        /**
         * Update existing article from the database
         * @return TRUE if successfully updated
         */
        function treaty_edit_article() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            @mysql_query("BEGIN", $wpdb->dbh);
            try {
                $id_treaty_article = intval($_POST['id_treaty_article']);
                $title = stripslashes(trim($_POST['title']));
                $data = array(
                    'title' => $title,
                );
                if (isset($_POST['official_order'])) {
                    $data['official_order'] = stripslashes(trim($_POST['official_order']));
                }
                if (isset($_POST['content'])) {
                    $data['content'] = stripslashes(trim($_POST['content']));
                }
                $data['rec_updated_author'] = $user;
                $data['rec_updated'] = date('Y-m-d H:i:s', strtotime("now"));
                $article_result = $wpdb->update('ai_treaty_article', $data, array('id' => $id_treaty_article));

                $keywords_result = TRUE;
                if ($article_result !== FALSE) {
                    $keywords_result = $wpdb->query($wpdb->prepare("DELETE FROM ai_treaty_article_vocabulary WHERE id_treaty_article = %d", $id_treaty_article));

                    if ($keywords_result !== FALSE) {
                        if (isset($_POST['keywords'])) {
                            foreach ($_POST['keywords'] as $keyword) {
                                $keywords_result = $wpdb->insert('ai_treaty_article_vocabulary',
                                    array(
                                        'id_treaty_article' => $id_treaty_article,
                                        'id_concept' => intval($keyword),
                                    )
                                );
                                if ($keywords_result == FALSE) {
                                    break;
                                }
                            }
                        }
                    }
                }

                $this->success = ($article_result !== FALSE) and ($keywords_result !== FALSE);
                if ($this->success) {
                    @mysql_query("COMMIT", $wpdb->dbh);
                    // Log the action
                    $id_treaty = $wpdb->get_col("SELECT id_treaty FROM ai_treaty_article WHERE id = $id_treaty_article");
                    $id_treaty = $id_treaty[0];
                    $t = $this->get_treaty($id_treaty);
                    $url = sprintf('%s/treaties/%d?id_treaty_article=%d#article_%d', get_bloginfo('url'), $id_treaty, $id_treaty_article, $id_treaty_article);
                    $this->add_activity_log('update', 'treaty', "Updated article <strong>{$data['official_order']} {$title}</strong> from treaty <strong>{$t->short_title}</strong>", null, $url);
                } else {
                    $this->errors = array('DB' => $wpdb->last_error);
                    @mysql_query("ROLLBACK", $wpdb->dbh);
                }
                return $this->success;
            } catch (Exception $e) {
                $this->success = FALSE;
                @mysql_query("ROLLBACK", $wpdb->dbh);
                return FALSE;
            }
        }

        /**
         * Validate the validate_treaty_add_article_paragraph form
         * @return TRUE If form successfully validated
         */
        function validate_treaty_add_article_paragraph() {
            $this->actioned = TRUE;
            if ($this->_security_check('informea-admin_treaty_add_article_paragraph')) {
                $val = new FormValidator();
                $val->addValidation("id_treaty_article", "req", "Please select an article");
                $val->addValidation("indent", "req", "Please fill in the indent");
                $val->addValidation("indent", "num", "Indent must be a number");
                $val->addValidation("content", "req", "Please fill in the content");
                $valid = $val->ValidateForm();
                if (!$valid) {
                    $this->errors = $val->GetErrors();
                }
                return $valid;
            }
            return FALSE;
        }

        /**
         * Insert new article paragraph into the database
         * @return TRUE if successfully added
         */
        function treaty_add_article_paragraph() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            $wpdb->real_escape = true;
            @mysql_query("BEGIN", $wpdb->dbh);
            try {
                $id_treaty_article = intval($_POST['id_treaty_article']);
                $order = intval($_POST['order']);
                $data = array(
                    'id_treaty_article' => $id_treaty_article,
                    'indent' => intval($_POST['indent']),
                    'order' => $order,
                    'content' => stripslashes(trim($_POST['content'])),
                    'rec_created' => date('Y-m-d H:i:s', strtotime("now")),
                    'rec_author' => $user
                );
                if (isset($_POST['official_order'])) {
                    $data['official_order'] = stripslashes(trim($_POST['official_order']));
                }

                $up_to_order = $order;
                while ($this->get_treaty_article_paragraph_with_order($id_treaty_article, $up_to_order)) {
                    $up_to_order += 1;
                }
                for ($c_order = $up_to_order; $c_order >= $order; $c_order -= 1) {
                    $wpdb->update('ai_treaty_article_paragraph', array('order' => $c_order + 1), array('id_treaty_article' => $id_treaty_article, 'order' => $c_order));
                }

                $paragraph_success = $wpdb->insert('ai_treaty_article_paragraph', $data);
                $this->insert_id = $wpdb->insert_id;

                $keywords_success = TRUE;
                if (($paragraph_success !== FALSE) and isset($_POST['keywords'])) {
                    foreach ($_POST['keywords'] as $keyword) {
                        $keywords_success = $wpdb->insert('ai_treaty_article_paragraph_vocabulary', array(
                                'id_treaty_article_paragraph' => $this->insert_id,
                                'id_concept' => intval($keyword),
                            )
                        );
                        if ($keywords_success == FALSE) {
                            break;
                        }
                    }
                }

                $this->success = ($paragraph_success !== FALSE) and ($keywords_success !== FALSE);
                if ($this->success) {
                    @mysql_query("COMMIT", $wpdb->dbh);
                    // Log the action
                    $treaty_article = $wpdb->get_row("SELECT * FROM ai_treaty_article WHERE id = $id_treaty_article");
                    $t = $this->get_treaty($treaty_article->id_treaty);
                    $url = sprintf('%s/treaties/%d?id_treaty_article=%d#article_%d_paragraph_%d', get_bloginfo('url'), $treaty_article->id_treaty, $id_treaty_article, $id_treaty_article, $this->insert_id);
                    $this->add_activity_log('insert', 'treaty', "Added new paragraph into treaty <strong>{$t->short_title}</strong>, article <strong>{$treaty_article->official_order} {$treaty_article->title}</strong>", null, $url);
                } else {
                    $this->errors = array('DB' => $wpdb->last_error);
                    @mysql_query("ROLLBACK", $wpdb->dbh);
                }
                return $this->success;
            } catch (Exception $e) {
                $this->success = FALSE;
                @mysql_query("ROLLBACK", $wpdb->dbh);
                return FALSE;
            }
        }

        /**
         * Validate the validate_treaty_edit_article_paragraph form
         * @return TRUE If form successfully validated
         */
        function validate_treaty_edit_article_paragraph() {
            $this->actioned = TRUE;
            if ($this->_security_check('informea-admin_treaty_edit_article_paragraph')) {
                $val = new FormValidator();
                $val->addValidation("id_treaty_article_paragraph", "req", "Please select a paragraph");
                $val->addValidation("indent", "req", "Please fill in the indent");
                $val->addValidation("indent", "num", "Indent must be a number");
                $val->addValidation("content", "req", "Please fill in the content");
                $valid = $val->ValidateForm();
                if (!$valid) {
                    $this->errors = $val->GetErrors();
                }
                return $valid;
            }
            return FALSE;
        }

        /**
         * Update an existing paragraph from the database
         * @return TRUE if successfully added
         */
        function treaty_edit_article_paragraph() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            @mysql_query("BEGIN", $wpdb->dbh);
            try {
                $id_treaty_article_paragraph = intval($_POST['id_treaty_article_paragraph']);

                $data = array(
                    'indent' => intval($_POST['indent']),
                    'content' => stripslashes(trim($_POST['content'])),
                );
                if (isset($_POST['official_order'])) {
                    $data['official_order'] = stripslashes(trim($_POST['official_order']));
                }
                $data['rec_updated_author'] = $user;
                $data['rec_updated'] = date('Y-m-d H:i:s', strtotime("now"));
                $paragraph_result = $wpdb->update('ai_treaty_article_paragraph', $data, array('id' => $id_treaty_article_paragraph));

                $keywords_result = TRUE;
                if ($paragraph_result !== FALSE) {
                    $keywords_result = $wpdb->query($wpdb->prepare("DELETE FROM ai_treaty_article_paragraph_vocabulary WHERE id_treaty_article_paragraph = %d", $id_treaty_article_paragraph));

                    if ($keywords_result !== FALSE) {
                        if (isset($_POST['keywords'])) {
                            foreach ($_POST['keywords'] as $keyword) {
                                $keywords_result = $wpdb->insert('ai_treaty_article_paragraph_vocabulary',
                                    array(
                                        'id_treaty_article_paragraph' => $id_treaty_article_paragraph,
                                        'id_concept' => intval($keyword),
                                    )
                                );
                                if ($keywords_result == FALSE) {
                                    break;
                                }
                            }
                        }
                    }
                }

                $this->success = ($paragraph_result !== FALSE) and ($keywords_result !== FALSE);
                if ($this->success) {
                    @mysql_query("COMMIT", $wpdb->dbh);
                    // Log the action
                    $treaty_article = $wpdb->get_row("SELECT a.* FROM ai_treaty_article a INNER JOIN ai_treaty_article_paragraph b ON b.id_treaty_article = a.id WHERE b.id = $id_treaty_article_paragraph");
                    $t = $this->get_treaty($treaty_article->id_treaty);
                    $url = sprintf('%s/treaties/%d?id_treaty_article=%d#article_%d_paragraph_%d', get_bloginfo('url'), $treaty_article->id_treaty, $treaty_article->id, $treaty_article->id, $id_treaty_article_paragraph);
                    $this->add_activity_log('update', 'treaty', "Updated paragraph from treaty <strong>{$t->short_title}</strong>, article <strong>{$treaty_article->official_order} {$treaty_article->title}</strong>. Current paragraph content: " . subwords($data['content'], 30), null, $url);
                } else {
                    $this->errors = array('DB' => $wpdb->last_error);
                    @mysql_query("ROLLBACK", $wpdb->dbh);
                }
                return $this->success;
            } catch (Exception $e) {
                $this->success = FALSE;
                @mysql_query("ROLLBACK", $wpdb->dbh);
                return FALSE;
            }
        }


        /**
         * Validate the validate_decision_add_decision_paragraph form
         * @return TRUE If form successfully validated
         */
        function validate_decision_add_decision_paragraph() {
            $this->actioned = TRUE;
            if ($this->_security_check('informea-admin_decision_add_decision_paragraph')) {
                $val = new FormValidator();
                $val->addValidation("id_decision", "req", "Please fill in the decision");
                $val->addValidation("indent", "req", "Please fill in the indent");
                $val->addValidation("indent", "num", "Indent must be a number");
                $val->addValidation("title", "req", "Please fill in the title");
                $val->addValidation("content", "req", "Please fill in the content");
                $valid = $val->ValidateForm();
                if (!$valid) {
                    $this->errors = $val->GetErrors();
                }
                return $valid;
            }
            return FALSE;
        }

        /**
         * Insert new decision_paragraph into the database
         * @return TRUE if successfully added
         */
        function decision_add_decision_paragraph() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            @mysql_query("BEGIN", $wpdb->dbh);
            try {
                $order = $this->get_next_decision_paragraph_order(get_request_int('id_decision'));
                $data = array(
                    'id_decision' => get_request_int('id_decision'),
                    'indent' => get_request_int('indent'),
                    'order' => intval($order),
                    'title' => get_request_value('title'),
                    'content' => get_request_value('content'),
                    'rec_created' => date('Y-m-d H:i:s', strtotime("now")),
                    'rec_author' => $user
                );
                if (get_request_boolean('official_order')) {
                    $data['official_order'] = get_request_value('official_order');
                }
                $paragraph_success = $wpdb->insert('ai_decision_paragraph', $data);
                $keywords_success = TRUE;
                if (($paragraph_success !== FALSE) and isset($_POST['keywords'])) {
                    $this->insert_id = $wpdb->insert_id;
                    foreach ($_POST['keywords'] as $keyword) {
                        $keywords_success = $wpdb->insert('ai_decision_paragraph_vocabulary', array(
                                'id_decision_paragraph' => $this->insert_id,
                                'id_concept' => intval($keyword),
                            )
                        );
                        if ($keywords_success == FALSE) {
                            break;
                        }
                    }
                }

                $this->success = ($paragraph_success !== FALSE) and ($keywords_success !== FALSE);
                if ($this->success) {
                    @mysql_query("COMMIT", $wpdb->dbh);
                } else {
                    $this->errors = array('DB' => $wpdb->last_error);
                    @mysql_query("ROLLBACK", $wpdb->dbh);
                }
                return $this->success;
            } catch (Exception $e) {
                $this->success = FALSE;
                @mysql_query("ROLLBACK", $wpdb->dbh);
                return FALSE;
            }
        }


        /**
         * Construct href for a treaty
         * @param id of the treaty
         * @return relative URL of the treaty
         */
        function get_treaty_url($id_treaty) {
            return sprintf('%s/treaties/%s', get_bloginfo('url'), $id_treaty);
        }

        /**
         * Construct href for an article in the treaty page
         * @param id of the treaty
         * @param id of the article
         * @return relative URL of the treaty
         */
        function get_article_url_in_treaty($id_treaty, $id_treaty_article) {
            return sprintf('%s/treaties/%s?id_treaty_article=%s#article_%d',
                get_bloginfo('url'), $id_treaty, $id_treaty_article, $id_treaty_article);
        }

        /**
         * Construct href for a treaty
         * @param id of the treaty
         * @param id of the article
         * @param id of the paragraph
         * @return relative URL of the treaty
         */
        function get_paragraph_url_in_treaty($id_treaty, $id_treaty_article, $id_treaty_article_paragraph) {
            return sprintf('%s/treaties/%s?id_treaty_article=%s#article_%d_paragraph_%d',
                get_bloginfo('url'), $id_treaty, $id_treaty_article, $id_treaty_article, $id_treaty_article_paragraph);
        }

        /**
         * Construct href for a decision
         * @param id of the decision
         * @return relative URL of the treaty
         */
        function get_decision_url($id_decision) {
            return sprintf('%s/decisions?id_decision=%s', get_bloginfo('url'), $id_decision);
        }


        /**
         * Retrieve request POST parameter
         * @param name of the parameter
         * @return parameter value or empty string if not set
         */
        function get_value($name, $strip_slashes = FALSE) {
            $ret = isset($_POST[$name]) ? $_POST[$name] : NULL;
            if ($strip_slashes) {
                $ret = stripslashes($ret);
            }
            return $ret;
        }

        /**
         * Echo the request parameter
         * @param name of the parameter
         * @return Nothing
         */
        function get_value_e($name, $strip_slashes = FALSE) {
            echo $this->get_value($name, $strip_slashes);
        }

        /**
         * //TODO: Replace with one from imea.functions.php
         * Retrieve request parameter also from GET
         * @param name of the parameter
         * @return parameter value or empty string if not set
         */
        function get_request_value($name) {
            if (isset($_POST[$name])) {
                return $_POST[$name];
            }
            if (isset($_GET[$name])) {
                return $_GET[$name];
            }
            return NULL;
        }

        /**
         * Do the security check for forms and echo unauthorized message if not correct
         * @param $nonce_field Nonce field, see Wordpress nonce definition, http://codex.wordpress.org/WordPress_Nonces
         * @return TRUE if security is OK
         */
        function _security_check($nonce_field) {
            if (!check_admin_referer($nonce_field)) {
                echo('<p>You are not authorized to access this page</p>');
                return FALSE;
            }
            return TRUE;
        }

        /**
         * Retrieve action from request (act parameter)
         * @return action value or NULL
         */
        function get_action() {
            if (isset($_POST['act'])) {
                return $_POST['act'];
            }
        }

        /**
         * Insert a record inside ai_activity_log table.
         * @param string $operation Possible values for operation (insert, update or delete)
         * @param string $section Section affected by the user, for example: vocabulary, treaty, decision etc. Do not invent new sections if some already exist. Look first.
         * @param string $username User that created the action (WordPress username)
         * @param string $description Description of the operation, for example: "Added tags a, b, c to article 'Article 2', paragraph 4". Send something meaningful and readable.
         * @param string $link Link to online version of the affected entity (if available).
         * @return boolean TRUE if successs, FALSE otherwise
         */
        function add_activity_log($operation, $section, $description, $username = NULL, $link = NULL) {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            if ($username !== NULL) {
                $user = $username;
            }
            $this->success = $wpdb->insert('ai_activity_log', array(
                    'operation' => $operation,
                    'section' => $section,
                    'username' => $user,
                    'description' => $description,
                    'url' => $link
                )
            );
            if ($this->success) {
                $this->insert_id = $wpdb->insert_id;
            } else {
                $this->success = FALSE;
                $this->errors = array('DB' => $wpdb->last_error);
            }
            return $this->success;
        }

        function get_activity_log($order = 'rec_created', $ascension = 'DESC', $limit = 100) {
            global $wpdb;
            $sql = "SELECT * FROM ai_activity_log ORDER BY $order $ascension ";
            if ($limit != 'all') {
                $sql .= " LIMIT $limit";
            }
            return $wpdb->get_results($sql);
        }


        /**
         * Take all terms A 'narrower' B and check that B 'broader' A. if not, fix it.
         */
        function misc_fix_voc_relationships_do() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            $sql = "SELECT a.id_concept, b.term AS id_concept_term, a.relation, d.identification, a.target_term, c.term AS target_term_term FROM voc_relation a
					INNER JOIN voc_concept b ON a.id_concept = b.id
					INNER JOIN voc_concept c ON a.target_term = c.id
					INNER JOIN voc_relation_type d ON a.relation = d.id
					WHERE a.relation = 2";
            $terms = $wpdb->get_results($sql);
            foreach ($terms as $term) {
                $sql = "SELECT * FROM voc_relation WHERE id_concept = {$term->target_term} AND target_term = {$term->id_concept} AND relation = 1";
                $c = $wpdb->get_results($sql);
                if (!count($c)) {
                    $wpdb->query(
                        $wpdb->prepare(
                            "INSERT INTO voc_relation (id_concept, target_term, relation, rec_created, rec_author, rec_updated, rec_updated_author) VALUES ( %d, %d, %d, %s, %s, %s, %s )",
                            array($term->target_term, $term->id_concept, 1, date('Y-m-d H:i:s', strtotime("now")), $user, date('Y-m-d H:i:s', strtotime("now")), $user)
                        )
                    );
                    $url = sprintf('%s/terms/%d', get_bloginfo('url'), $term->target_term);
                    $this->add_activity_log('insert', 'vocabulary', "Missing link fixed. Term <strong>{$term->target_term_term} ({$term->target_term})</strong> has not broader term set to <strong>{$term->id_concept_term}({$term->id_concept})</strong><br />", null, $url);
                }
            }
        }


        function unlink_term_treaty() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            $this->actioned = TRUE;
            $this->success = FALSE;

            $id_term = get_request_value('id_term');
            $id_treaty = get_request_value('id_treaty');
            $term = get_request_value('term');
            $treaty = get_request_value('treaty');

            $wpdb->query("DELETE FROM ai_treaty_vocabulary WHERE id_treaty=$id_treaty AND id_concept=$id_term");

            $url = sprintf('%s/terms/%d', get_bloginfo('url'), $id_term);
            $this->add_activity_log('update', 'treaty', "Unassigned term <strong>$term</strong> from treaty <strong>$treaty</strong>", $user, $url);
        }

        function unlink_term_article() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            $this->actioned = TRUE;
            $this->success = FALSE;

            $id_term = get_request_value('id_term');
            $id_article = get_request_value('id_article');
            $term = get_request_value('term');
            $treaty = get_request_value('treaty');
            $article = get_request_value('article');

            $wpdb->query("DELETE FROM ai_treaty_article_vocabulary WHERE id_treaty_article=$id_article AND id_concept=$id_term");

            $url = sprintf('%s/terms/%d', get_bloginfo('url'), $id_term);
            $this->add_activity_log('update', 'treaty', "Unassigned term <strong>$term</strong> from article <strong>$article</strong> of <strong>$treaty</strong>", $user, $url);
        }

        function unlink_term_paragraph() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            $this->actioned = TRUE;
            $this->success = FALSE;

            $id_term = get_request_value('id_term');
            $id_paragraph = get_request_value('id_paragraph');
            $term = get_request_value('term');
            $treaty = get_request_value('treaty');
            $article = get_request_value('article');
            $paragraph = get_request_value('paragraph');

            $wpdb->query("DELETE FROM ai_treaty_article_paragraph_vocabulary WHERE id_treaty_article_paragraph=$id_paragraph AND id_concept=$id_term");

            $url = sprintf('%s/terms/%d', get_bloginfo('url'), $id_term);
            $this->add_activity_log('update', 'treaty', "Unassigned term <strong>$term</strong> from paragraph <strong>$paragraph</strong>, article <strong>$article</strong> of <strong>$treaty</strong>", $user, $url);
        }

        function unlink_term_decision() {
            global $wpdb;
            global $current_user;
            $user = $current_user->user_login;
            $this->actioned = TRUE;
            $this->success = FALSE;

            $id_term = get_request_value('id_term');
            $id_decision = get_request_value('id_decision');
            $term = get_request_value('term');
            $decision = get_request_value('decision');

            $wpdb->query("DELETE FROM ai_decision_vocabulary WHERE id_decision=$id_decision AND id_concept=$id_term");

            $url = sprintf('%s/terms/%d', get_bloginfo('url'), $id_term);
            $this->add_activity_log('update', 'decision', "Unassigned term <strong>$term</strong> from decision <strong>$decision</strong>", $user, $url);
        }
    }
}
?>
