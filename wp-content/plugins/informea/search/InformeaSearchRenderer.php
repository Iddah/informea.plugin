<?php


abstract class InformeaBaseSearchRenderer {

    abstract function render($results);
}


class InformeaSearchRendererTab1 extends InformeaBaseSearchRenderer {


    function render($results) {
        $ret = '<ul class="search_results tab1">';
        $ret .= $this->render_ajax($results);
        $ret .= '</ul>';
        return $ret;
    }

    function render_ajax($results) {
        $ret = '';
        foreach($results as $row) {
            $ret .= $this->render_row($row);
        }
        return $ret;
    }


    function render_row($row) {
        $ret = '<li>';
        $ret .= '<a class="toggle-result closed" href="javascript:void(0);"></a>';
        call_user_func('this::render_' . $row->entity_type, $row);
        $ret .= '</li>';
        return $ret;
    }

    function render_treaty($row) {
        $ret = '<div class="logo_medium">';
        $ret .= sprintf('<img src="%s" />', $row->logo_medium);
        $ret .= '</div>';
        return $ret;
    }

    function render_decision($row) {

    }

    function render_event($row) {

    }
}


class InformeaSearchRendererTab2 extends InformeaBaseSearchRenderer {

    /**
     * Render results
     * @param array $results Results as array of treaties populated with data (object tree)
     * @return string Rendered result
     */
    function render($results) {
        $ret = '<ul class="search-results tab2">';
        foreach($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }

    /**
     * Render top-level treaty
     * @param object $treaty Single treaty tree object
     * @return string Rendered output
     */
    function render_treaty($treaty) {
        $ret = '<li>';
        $tooltips = array(_('Click to see treaty content', 'informea'), _('Convention logo', 'informea'), _('Click to open treaty text into a new window', 'informea'));
        $ret .= sprintf('<a id="arrow-result-treaty%s" class="toggle-result arrow closed left" title="%s" href="javascript:void(0);"></a>', $treaty->id, $tooltips[0]);

        $ret .= sprintf('<div class="logo-medium left" title="%s"><img src="%s" /></div>', $tooltips[1], $treaty->logo_medium);
        $ret .= sprintf('<a id="expand-title-treaty%s" class="toggle-result title left" title="%s" href="javascript:void(0);">%s</a>', $treaty->id, $tooltips[0], $treaty->short_title);
        $ret .= sprintf('<a href="%s" class="external title" target="_blank" title="%s"></a>', get_bloginfo('url') . '/treaties/' . $treaty->id, $tooltips[2]);
        $ret .= '<div class="clear"></div>';
        $ret .= sprintf('<div id="result-treaty%s" class="content hidden">', $treaty->id);
        $ret .= $this->render_articles($treaty);
        $ret .= $this->render_decisions($treaty);
        $ret .= '</div>';
        $ret .= '</li>';
        return $ret;
    }

    /**
     * Render articles for a treaty
     * @param object $treaty Single treaty tree object (with 'treaties' property)
     * @return string Rendered output
     */
    function render_articles($treaty) {
        if(empty($treaty->articles)) {
            return '';
        }
        $ret = '<span class="articles">';
        $ret .= sprintf('<h2>%s</h2>', _('Articles', 'informea'));
        $ret .= '<ul>';
        $tooltips = array(_('Click to see article content', 'informea'));
        foreach($treaty->articles as $article) {
            $ret .= '<li>';
            $ret .= sprintf('<a id="arrow-result-article%s" class="toggle-result arrow closed left" title="%s" href="javascript:void(0);"></a>', $article->id, $tooltips[0]);
            $ret .= sprintf('<a id="expand-title-article%s" class="toggle-result title left" title="%s" href="javascript:void(0);">%s %s</a>',
                $article->id, $tooltips[0], $article->number, $article->title);
            $url = sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s', get_bloginfo('url'), $treaty->id, $article->id, $article->id);
            $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[2]);
            $ret .= '<div class="clear"></div>';
            $ret .= sprintf('<div id="result-article%s" class="content hidden">', $article->id);
            $ret .= $this->render_treaty_paragraphs($article);
            $ret .= '</div>';
            $ret .= '<li>';
        }
        $ret .= '</ul>';
        $ret .= '<div class="clear"></div>';
        $ret .= '</span>';
        return $ret;
    }


    function render_treaty_paragraphs($article) {
        $ret = '';
        if(count($article->paragraphs) > 0) {
            $tooltips = array(_('Click to see paragraph content', 'informea'), _('Click to see paragraph inside treaty text, into a new window', 'informea'));
            $ret .= '<h3>Paragraphs</h3>';
            $ret .= '<ul class="paragraphs">';
            foreach($article->paragraphs as $paragraph) {
                $ret .= '<li>';
                $title = !empty($paragraph->official_order) ? $paragraph->official_order : $paragraph->order;
                $ret .= sprintf('<a id="ajaxexpand-treaty_article_paragraph-%s" class="ajax-expand arrow closed left" href="javascript:void(0);">%s</a>', $paragraph->id, $title);
                $url = sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s_paragraph_%s', get_bloginfo('url'), $article->id_treaty, $article->id, $article->id, $paragraph->id);
                $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[1]);
                $ret .= '<div class="clear"></div>';
                $ret .= sprintf('<div id="result-treaty_article_paragraph-%s" class="highlight content hidden"></div>', $paragraph->id);
                $ret .= '</li>';
            }
            $ret .= '</ul>';
        } else {
        }
        return $ret;
    }

    function render_decisions($treaty) {
        if(empty($treaty->decisions)) {
            return '';
        }
        $ret = '<h2>Decisions</h2>';
        $ret .= '<ul class="decisions">';
        $tooltips = array(_('Click to see decision in context of the treaty', 'informea'));
        foreach($treaty->decisions as $decision) {
            $ret .= '<li>';
            $cssClass = (count($decision->paragraphs) > 0 || count($decision->documents) > 0) ? 'toggle-result arrow closed' : '';
            $ret .= sprintf('<a id="toggle-result-decision%s" href="javascript:void(0);" class="%s left">%s - %s</a>' , $decision->id, $cssClass, $decision->number, $decision->short_title);
            $url = sprintf('%s/treaties/%s/decisions?showall=true#decision-%s', get_bloginfo('url'), $decision->id_treaty, $decision->id);
            $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[0]);
            $ret .= sprintf('<div id="result-decision%s" class="content hidden">', $decision->id);
            $ret .= $this->render_decision_paragraphs($decision);
            $ret .= $this->render_decision_documents($decision);
            $ret .= '</div>';
            $ret .= '<li>';
        }
        $ret .= '</ul>';
        return $ret;
    }

    function render_decision_paragraphs($decision) {
        $ret = '';
        if(count($decision->paragraphs) > 0) {
            $ret .= '<h3>Paragraphs</h3>';
            $ret .= '<ul class="paragraphs">';
            foreach($decision->paragraphs as $paragraph) {
                $ret .= '<li>';
                $title = !empty($paragraph->official_order) ? $paragraph->official_order : $paragraph->order + 1;
                $ret .= sprintf('<a id="ajaxexpand-decision_paragraph-%s" class="ajax-expand arrow closed" href="javascript:void(0);">%s</a>', $paragraph->id, $title);
                $ret .= sprintf('<div id="result-decision_paragraph-%s" class="highlight content hidden"></div>', $paragraph->id);
                $ret .= '</li>';
            }
            $ret .= '</ul>';
        } else {
        }
        return $ret;
    }

    function render_decision_documents($decision) {
        $ret = '';
        if(count($decision->documents) > 0) {
            $ret .= '<h3>Documents</h3>';
            $ret .= '<ul class="documents">';
            foreach($decision->documents as $document) {
                $ret .= '<li>';
                $title = $document->filename;
                $ret .= sprintf('<a id="ajaxexpand-decision_document-%s" class="ajax-expand arrow closed" href="javascript:void(0);">%s</a>', $document->id, $title);
                $ret .= sprintf('<div id="result-decision_document-%s" class="highlight content hidden"></div>', $document->id);
                $ret .= '</li>';
            }
            $ret .= '</ul>';
        } else {
        }
        return $ret;
    }
}
