<?php
/*
 * One result row
 */
abstract class AbstractSearchResult2 {
    public $id;
    public $entity_type;
    public $db;
    public $direct_hit = false; /* ture if hit on treaty description, false if hit on its articles/paragraphs */
    public $context;

    public function __construct($id, $entity_type, $context) {
        $this->entity_type = $entity_type;
        $this->id = $id;
        $this->context = $context;
    }

    abstract function get_title($tab);

    abstract function get_item_url();

    abstract function get_description($tab);

    abstract function get_content($tab);

    abstract function get_icon($tab);

    public function setDbObject($db) {
        $this->db = $db;
    }

    protected function format_date_title($date_str, $custom_output = '%d/%m/%Y') {
        $t = strptime($date_str, '%Y-%m-%d %H:%M:%S');
        return strftime($custom_output, mktime($t['tm_hour'], $t['tm_min'], $t['tm_sec'], $t['tm_mon'] + 1, $t['tm_mday'], $t['tm_year'] + 1900));
    }


    function get_link($label, $rel_url, $title = null, $css = null, $target = null) {
        return $label;
        if (!empty($rel_url)) {
            $url = strpos($rel_url, 'http://') >= 0 ? $rel_url : get_bloginfo('url') . $rel_url;
            $css = !empty($css) ? " class=\"$css\"" : '';
            $title = !empty($title) ? " title=\"$title\"" : '';
            $target = !empty($target) ? " target=\"$target\"" : '';
            return "<a href=\"$url\"$title$css$target>$label</a>";
        } else {
            return $label;
        }
    }

    public function is_dirty_search() {
        return $this->context->is_dirty_search();
    }

    public function plural($sentence, $sentence_plural, $count) {
        if ($count == 1) {
            return sprintf($sentence, $count);
        } else {
            return sprintf($sentence_plural, $count);
        }
    }

    function collapsible_content($uid, $title, $content, $target = null) {
        $url = !empty($target) ? $target->view_item_link() : $this->view_item_link();
        $script = "$(this).siblings('div').toggle();";
        $ret = '';
        $ret .= sprintf('<a href="javascript:void(0);" onclick="%s"><i class="icon icon-plus-sign"></a></i>', $script);
        $ret .= sprintf('<a href="javascript:void(0);" onclick="%s">%s</a>%s', $script, $title , $url);
        $ret .= '<div class="hidden">' . $content . '</div>';
        return $ret;
    }

    function html_img($rel_url) {
        return '<img class="middle" src="' . $rel_url . '" />';
    }

    function view_item_link() {
        return sprintf('<a href="%s" class="external" target="_blank" title="%s">&rarr;</a>',
            $this->get_item_url(), __('Open item page in a new window', 'informea'));
    }
}


class MeetingSearchResult2 extends AbstractSearchResult2 {
    public $status_decoder;

    public function __construct($id, $context, $db = null, $direct_hit = true) {
        parent::__construct($id, 'meeting', $context);
        $this->status_decoder = new imea_meetings_page();
        $this->db = $db;
        $this->direct_hit = $direct_hit;
    }

    public function get_title($tab) {
        $d = $this->format_date_title($this->db->start);
        $label = "$d - {$this->db->title}";
        return $label;
        // return $this->get_link($label, $this->db->event_url, "Click to view the event on {$this->db->treaty} website", 'link', '_blank');
    }

    public function get_item_url() {
        return $this->db->event_url;
    }

    public function get_description($tab) {
        return '<strong>' . show_event_interval($this->db) . '</strong> ' . $this->db->title;
    }

    public function get_content($tab) {
        $ret = '';
        if ($this->db->city) {
            if ($this->db->location) {
                $ret .= $this->db->location . ', ';
            }
            $ret = $ret . $this->db->city;
        }
        $status = $this->status_decoder->decode_status($this->db->status);
        if (!empty($status)) {
            $ret .= " ($status)";
        }
        if (empty($ret)) {
            $ret .= ' No additional details available';
        }
        return $ret;
    }

    function get_icon($tab) {
        global $wpdb;
        $treaty = $wpdb->get_row($wpdb->prepare('SELECT * FROM ai_treaty WHERE id=%d', $this->db->id_treaty));
        return $this->html_img($treaty->logo_medium);
    }
}


class TreatySearchResult2 extends AbstractSearchResult2 {
    public $order = null;
    public $articles = array( /* TreatyArticleSearchResult2 */);
    public $decisions = array( /* DecisionSearchResult2 */);

    private $from_tagging = false;

    public function set_from_tagging($bool_val) {
        $this->from_tagging = $bool_val;
    }

    public function __construct($id, $context, $db = null, $direct_hit = false) {
        parent::__construct($id, 'treaty', $context);
        $this->db = $db;
        $this->order = $db->order;
        $this->direct_hit = $direct_hit;
    }


    function get_title($tab) {
        $d = $this->format_date_title($this->db->start, '%Y');
        return $this->db->short_title;
    }

    public function get_item_url() {
        return sprintf('%s/treaties/%s', get_bloginfo('url'), $this->db->odata_name);
    }


    public function get_description($tab) {
        if ($tab != 1) {
            return $this->db->long_title;
        }
    }

    public function get_content($tab) {
        if ($this->from_tagging && empty($this->articles) && empty($this->decisions)) {
            return '<span class="tagged text-grey">This treaty was tagged with InforMEA keywords</span>';
        }
        $ret = '';
        if ($tab == 1 || $tab == 2 || $tab == 3) {
            $ca = count($this->articles);
            if ($ca > 0) {
                $ret .= sprintf('<strong>%s</strong>', $this->plural('Article:', 'Articles (%d):', $ca));
                $ret .= '<ul class="articles">';
                foreach ($this->articles as $article) {
                    $uid = slugify($article->id . ' ' . $article->entity_type);
                    $ret .= '<li>';
                    $ret .= $this->collapsible_content($uid, $article->get_title($tab), $article->get_content($tab), $article);
                    $ret .= '</li>';
                }
                $ret .= '</ul>';
            }
        }
        // TODO: Sort decisions alphabetically before displaying them
        if ($tab == 2 || $tab == 4) {
            $cd = count($this->decisions);
            if ($cd > 0) {
                $ret .= $this->plural('Decision:', 'Decisions (%d):', $cd);
                $ret .= '<ul class="matching-decisions">';
                foreach ($this->decisions as $decision) {
                    $uid = slugify($decision->id . ' ' . $decision->entity_type);
                    $ret .= '<li>' . $this->collapsible_content($uid, $decision->get_title($tab), $decision->get_content($tab), $decision) . '</li>';
                }
                $ret .= '</ul>';
            }
        }
        return $ret;
    }

    function get_icon($tab) {
        global $wpdb;
        $treaty = $wpdb->get_row($wpdb->prepare('SELECT * FROM ai_treaty WHERE id=%d', $this->db->id));
        return $this->html_img($treaty->logo_medium);
    }
}


class TreatyArticleSearchResult2 extends AbstractSearchResult2 {
    public $treaty;
    public $paragraphs = array();

    private $from_tagging = false;

    public function set_from_tagging($bool_val) {
        $this->from_tagging = $bool_val;
    }


    public function __construct($id, $context, $db = null, $direct_hit = false) {
        parent::__construct($id, 'treaty_article', $context);
        $this->db = $db;
        $this->direct_hit = $direct_hit;
    }


    function get_title($tab) {
        $label = (!empty($this->db->official_order) ? $this->db->official_order . ' - ' : '') . $this->db->title;
        $url = "/treaties/{$this->db->id_treaty}?id_treaty_article={$this->db->id}#article_{$this->db->id}";
        return $label;
        // return $this->get_link($label, $url, 'This article has a match on it. Click to open', 'link treaty-tipsy');
    }

    public function get_item_url() {
        return sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s',
            get_bloginfo('url'), $this->treaty->odata_name, $this->db->id, $this->db->id);
    }

    public function get_description($tab) {
        return 'NOT IMPLEMENTED';
    } // Not needed
    function get_icon($tab) {
    }

    public function get_content($tab) {
        $ret = '';
        $cp = count($this->paragraphs);
        if ($cp <= 0) {
            if (!$this->from_tagging) {
                $highlight = $this->context->solr_invoker->highlight($this->id, $this->entity_type);
                $ret .= sprintf('<div class="article">%s</div>', $highlight);
            } else {
                $ret .= sprintf('<div class="article">%s</div>', subwords(strip_tags($this->db->content), 100));
            }
        } else {
            $ret .= sprintf('<strong>%s</strong>', $this->plural('Paragraph: ', 'Paragraphs: ', $cp));
            $ret .= '<ul class="paragraphs">';
            foreach ($this->paragraphs as $paragraph) {
                if ($this->is_dirty_search()) {
                    $uid = slugify($paragraph->id . ' ' . $paragraph->entity_type);
                    $ret .= sprintf('<li>%s</li>',
                        $this->collapsible_content($uid, $paragraph->get_title($tab, $this),
                        $paragraph->get_content($tab), $paragraph)
                    );
                } else {
                    $ret .= sprintf('<li>%s %s %s</li>', $paragraph->db->official_order,
                                $paragraph->get_content($tab), $paragraph->view_item_link());
                }
            }
            $ret .= '</ul>';
        }
        return $ret;
    }
}


class TreatyParagraphSearchResult2 extends AbstractSearchResult2 {
    private $from_tagging = false;


    public function __construct($id, $context, $db = null, $direct_hit = false) {
        parent::__construct($id, 'treaty_article_paragraph', $context);
        $this->db = $db;
        $this->direct_hit = $direct_hit;
    }

    public function set_from_tagging($bool_val) {
        $this->from_tagging = $bool_val;
    }

    public function get_description($tab) {
        return 'NOT IMPLEMENTED';
    } // Not needed
    function get_icon($tab) {
    }

    function get_title($tab) {
        $label = !empty($this->db->official_order) ? $this->db->official_order : $this->db->order;
        return $label;
    }


    public function get_item_url() {
        $id = $this->db->id_treaty_article;
        $article = imea_request_cache::get_treaty_article($id);
        $treaty = imea_request_cache::get_treaty($article->id_treaty);
        return sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s_paragraph_%s', get_bloginfo('url'), $treaty->odata_name, $id, $article->id, $this->id);
    }


    public function get_content($tab) {
        if (!$this->from_tagging) {
            $highlight = $this->context->solr_invoker->highlight($this->id, $this->entity_type);
            return $highlight;
        } else {
            return $this->db->content;
        }
    }
}


class DecisionSearchResult2 extends AbstractSearchResult2 {

    public $documents = array();
    public $paragraphs = array();
    private $from_tagging = false;

    public function set_from_tagging($bool_val) {
        $this->from_tagging = $bool_val;
    }

    public function __construct($id, $context, $db = null, $direct_hit = false) {
        parent::__construct($id, 'decision', $context);
        $this->db = $db;
        $this->direct_hit = $direct_hit;
    }


    public function get_description($tab) {
    }

    function get_icon($tab) {
        if ($tab == 1) {
            global $wpdb;
            $treaty = $wpdb->get_row($wpdb->prepare('SELECT * FROM ai_treaty WHERE id=%d', $this->db->id_treaty));
            return $this->html_img($treaty->logo_medium);
        }
        return '';
    }


    function get_title($tab) {
        if ($tab == 1) {
            $d = $this->format_date_title($this->db->published, '%e %b, %Y');
            $label = "$d - {$this->db->type} " . $this->db->number . ' - ' . (!empty($this->db->long_title) ? $this->db->long_title : $this->db->short_title);
            ;
        } else {
            if ($tab == 2) {
                $label = $this->db->number . ' - ' . (!empty($this->db->long_title) ? $this->db->long_title : $this->db->short_title);
                ;
            } else {
                $label = "{$this->db->type} " . $this->db->number . ' - ' . (!empty($this->db->long_title) ? $this->db->long_title : $this->db->short_title);
                ;
            }
        }
        return $label;
        // return parent::get_link($label, "/treaties/{$this->db->id_treaty}/decisions?showall=true#decision-{$this->id}", 'Click to view the decision', 'link');
    }

    public function get_item_url() {
        return get_bloginfo('url') . "/treaties/{$this->db->odata_name}/decisions?showall=true#decision-{$this->id}";
    }


    public function get_content($tab) {
        $ret = '';
        $cd = count($this->documents);
        if ($cd > 0) {
            $ret .= '<div class="matching-documents">';
            $ret .= $this->plural('Found one document:', 'Found %d documents:', $cd);
            $ret .= '<ul class="matching-document">';
            foreach ($this->documents as $doc) {
                $uid = slugify($doc->id . ' ' . $doc->entity_type);
                $ret .= '<li>' . $this->collapsible_content($uid, $doc->get_title($tab), $doc->get_content($tab), $doc) . '</li>';
            }
            $ret .= '</ul>';
            $ret .= '</div>';
        } else {
            if ($this->from_tagging) {
                $cp = count($this->paragraphs);
                if ($cp) {
                    $ret .= sprintf('<strong>%s</strong>', $this->plural('Paragraph: ', 'Paragraphs: ', $cp));
                    $ret .= '<ul class="paragraphs">';
                    foreach ($this->paragraphs as $paragraph) {
                        $ret .= sprintf('<li>%s</li>', $paragraph->content);
                    }
                    $ret .= '</ul>';
                } else {
                    $ret = '<div class="paragraph text-grey">This decision was tagged with InforMEA keywords</div>';
                }
            } else {
                $ret = sprintf('<div class="paragraph">%s</div>',
                    $this->context->solr_invoker->highlight($this->id, $this->entity_type));
            }
        }
        return $ret;
    }
}


class DecisionDocumentSearchResult2 extends AbstractSearchResult2 {

    function get_icon($tab) {
    }

    public function __construct($id, $context, $db = null, $direct_hit = false) {
        parent::__construct($id, 'decision_document', $context);
        $this->db = $db;
        $this->direct_hit = $direct_hit;
    }

    function get_title($tab) {
        $ret = '';
        $label = '';
        if ($this->db->mime == 'doc' || $this->db->mime == 'application/msword') {
            $label = '<img class="middle" src="' . get_bloginfo('template_directory') . '/images/doc.png" />';
        } else {
            if ($this->db->mime == 'pdf' || $this->db->mime == 'application/pdf' || $this->db->mime == 'application/x-pdf') {
                $label = '<img class="middle" src="' . get_bloginfo('template_directory') . '/images/pdf.png" />';
            }
        }
        $label .= $this->db->filename;
        return $label;
        // return $this->get_link($label, $this->db->url, 'Click to open the document', 'link');
    }

    public function get_item_url() {
        return get_bloginfo('url') . '/download?entity=decision_document&id=' . $this->db->id;
    }


    public function get_description($tab) {
        return 'NOT IMPLEMENTED';
    } // Not needed

    public function get_content($tab) {
        $ret = '';
        if (!empty($excerpt)) {
            $para_id = slugify($tab);
            $ret = '<a id=\'link-highlight-' . $para_id . '\' href="javascript:details(\'highlight-' . $para_id . '\');" class="list-item-title-click closed" title="View excerpts from the matching paragraphs">View excerpt</a>';
            $ret .= '<div id="highlight-' . $para_id . '" class="matching-paragraph hidden">' . $excerpt . '</div>';
        } else {
            if ($this->direct_hit) {
                $ret = '<p>Could not determine the paragraph. Try to consult the document directly. You may try also to report this issue to development team</p>';
            }
        }
        return $this->context->solr_invoker->highlight($this->id, $this->entity_type);
    }
}
?>
