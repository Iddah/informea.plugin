<?php


abstract class InformeaBaseSearchRenderer {

    abstract function render($results);

    function no_results() {
        return '<p>' . __('No results found', 'informea') . '</p>';
    }

    /**
     * Render top-level treaty
     * @param object $treaty Single treaty tree object
     * @return string Rendered output
     */
    function render_treaty($treaty) {
        $ret = '<li class="treaty">';
        $tooltips = array(_('Click to see treaty content', 'informea'), _('Convention logo', 'informea'), _('Click to open treaty text into a new window', 'informea'));

        $css = count($treaty->articles) || count($treaty->decisions) ? 'toggle-result' : 'ajax-expand';

        $ret .= sprintf('<a id="arrow-treaty-%s" class="%s arrow closed arrow left" title="%s" href="javascript:void(0);"></a>', $treaty->id, $css, $tooltips[0]);
        $ret .= sprintf('<img title="%s" src="%s" />', $tooltips[1], $treaty->logo_medium);
        $ret .= sprintf('<a id="expand-treaty-%s" class="%s title left" title="%s" href="javascript:void(0);">%s</a>', $treaty->id, $css, $tooltips[0], $treaty->short_title);
        $ret .= sprintf('<a href="%s" class="external title" target="_blank" title="%s"></a>', get_bloginfo('url') . '/treaties/' . $treaty->id, $tooltips[2]);
        $ret .= '<div class="clear"></div>';
        $ret .= sprintf('<div id="result-treaty-%s" class="content hidden">', $treaty->id);
        $ret .= $this->render_articles($treaty);
        $ret .= $this->render_decisions($treaty);
        $ret .= '<div class="clear"></div>';
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
        $ret = '<span>';
        $ret .= sprintf('<h2>%s</h2>', _('Articles', 'informea'));
        $ret .= '<ul class="articles">';
        $tooltips = array(_('Click to see article content', 'informea'));
        foreach($treaty->articles as $article) {
            $ret .= '<li>';
            $css = count($article->paragraphs) ? 'toggle-result' : 'ajax-expand';
            $ret .= sprintf('<a id="arrow-treaty_article-%s" class="%s arrow closed left" title="%s" href="javascript:void(0);"></a>', $article->id, $css, $tooltips[0]);
            $ret .= sprintf('<a id="expand-treaty_article-%s" class="%s title left" title="%s" href="javascript:void(0);">%s %s</a>', $article->id, $css, $tooltips[0], $article->number, $article->title);
            $url = sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s', get_bloginfo('url'), $treaty->id, $article->id, $article->id);
            $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[2]);
            $ret .= '<div class="clear"></div>';
            $ret .= sprintf('<div id="result-treaty_article-%s" class="content hidden">', $article->id);
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
                $ret .= sprintf('<a id="arrow-treaty_article_paragraph-%s" class="ajax-expand arrow closed left" href="javascript:void(0);">%s</a>', $paragraph->id, $title);
                $url = sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s_paragraph_%s', get_bloginfo('url'), $article->id_treaty, $article->id, $article->id, $paragraph->id);
                $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[1]);
                $ret .= '<div class="clear"></div>';
                $ret .= sprintf('<div id="result-treaty_article_paragraph-%s" class="content hidden"></div>', $paragraph->id);
                $ret .= '</li>';
            }
            $ret .= '</ul>';
        } else {
        }
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
                $ret .= sprintf('<a id="arrow-decision_paragraph-%s" class="ajax-expand arrow closed" href="javascript:void(0);">%s</a>', $paragraph->id, $title);
                $ret .= sprintf('<div id="result-decision_paragraph-%s" class="content hidden"></div>', $paragraph->id);
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
                $url = sprintf('%s/download?entity=decision_document&id=%s', get_bloginfo('url'), $document->id);
                $ret .= sprintf('<a id="arrow-decision_document-%s" class="ajax-expand arrow closed left" href="javascript:void(0);"></a>', $document->id);
                $ret .= sprintf('<a id="expand-decision_document-%s" href="javascript:void(0);" class="ajax-expand left title">%s</a>', $document->id, $title);
                $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[0]);
                $ret .= '<div class="clear"></div>';
                $ret .= sprintf('<div id="result-decision_document-%s" class="content hidden"></div>', $document->id);
                $ret .= '<div class="clear"></div>';
                $ret .= '</li>';
            }
            $ret .= '</ul>';
        } else {
        }
        return $ret;
    }

}


class InformeaSearchRendererTab1 extends InformeaBaseSearchRenderer {


    function render($results) {
        if(empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul id="search_results" class="search-results tab1">';
        foreach($results as $row) {
            $ret .= call_user_func(array($this, 'render_' . $row->entity_type), $row);
        }
        return $ret;
        $ret .= '</ul>';
        return $ret;
    }


    function render_decisions($treaty) {
        return '';
    }

    function render_decision($decision) {
        $tooltips = array(_('Click to see decision content', 'informea'), _('Convention logo', 'informea'), _('Click to open decision into treaty context', 'informea'));
        $ret .= '<li class="decision">';
        $css = (count($decision->paragraphs) > 0 || count($decision->documents) > 0) ? 'toggle-result' : 'ajax-expand';
        $ret .= sprintf('<a id="arrow-decision-%s" href="javascript:void(0);" class="%s arrow closed left"></a>' , $decision->id, $css);
        $ret .= sprintf('<div class="logo-medium left" title="%s"><img src="%s" /></div>', $tooltips[1], $decision->logo_medium);
        $url = sprintf('%s/treaties/%s/decisions?showall=true#decision-%s', get_bloginfo('url'), $decision->id_treaty, $decision->id);
        $ret .= sprintf('<a id="expand-decision-%s" href="javascript:void(0);" class="%s left title" title="%s">%s - %s</a>' , $decision->id, $css, $decision->short_title, $decision->number, subwords($decision->short_title, 13));
        $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[0]);
        $ret .= '<div class="clear"></div>';
        $ret .= sprintf('<div id="result-decision-%s" class="content hidden">', $decision->id);
        $ret .= $this->render_decision_paragraphs($decision);
        $ret .= $this->render_decision_documents($decision);
        $ret .= '</div>';
        $ret .= '<div class="clear"></div>';
        $ret .= '</li>';
        return $ret;
    }

    function render_event($row) {
        $tooltips = array(_('Convention logo', 'informea'), _('Click to open decision into treaty context', 'informea'));
        $ret .= '<li class="event">';
        $ret .= sprintf('<a id="arrow-event-%s" href="javascript:void(0);" class="arrow left"></a>', $row->id);
        $ret .= sprintf('<div class="logo-medium left" title="%s"><img src="%s" /></div>', $tooltips[0], $row->logo_medium);
        $ret .= '<div class="title left">';
        $ret .= empty($row->event_url) ? subwords($row->title) : sprintf('<a href="%s" target="_blank" title="%s" class="left">%s</a>', $row->event_url, $row->title, subwords($row->title));
        $ret .= sprintf('&nbsp; - %s', format_mysql_date($row->start));
        $ret .= '</div>';
        $ret .= '<div class="clear"></div>';
        $ret .= '</li>';
        return $ret;
    }
}


class InformeaSearchRendererTab1Ajax extends InformeaSearchRendererTab1 {

    function render($results) {
        $ret = '';
        foreach($results as $row) {
            $ret .= call_user_func(array($this, 'render_' . $row->entity_type), $row);
        }
        return $ret;
    }
}

class InformeaSearchRendererTab2 extends InformeaBaseSearchRenderer {

    /**
     * Render results
     * @param array $results Results as array of treaties populated with data (object tree)
     * @return string Rendered result
     */
    function render($results) {
        if(empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab2">';
        foreach($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }

    function render_decisions($treaty) {
        if(empty($treaty->decisions)) {
            return '';
        }
        $ret = '<h2>Decisions</h2>';
        $ret .= '<ul>';
        $tooltips = array(_('Click to see decision in context of the treaty', 'informea'));
        foreach($treaty->decisions as $decision) {
            $ret .= '<li>';
            $css = (count($decision->paragraphs) > 0 || count($decision->documents) > 0) ? 'toggle-result' : 'ajax-expand';
            $ret .= sprintf('<a id="arrow-decision-%s" href="javascript:void(0);" class="%s arrow closed left"></a>' , $decision->id, $css);
            $ret .= sprintf('<a id="expand-decision-%s" href="javascript:void(0);" class="%s left title" title="%s">%s - %s</a>' , $decision->id, $css, $decision->short_title, $decision->number, subwords($decision->short_title, 10));
            $url = sprintf('%s/treaties/%s/decisions?showall=true#decision-%s', get_bloginfo('url'), $decision->id_treaty, $decision->id);
            $ret .= sprintf('<a href="%s" class="external title left" target="_blank" title="%s"></a>', $url, $tooltips[0]);
            $ret .= '<div class="clear"></div>';
            $ret .= sprintf('<div id="result-decision-%s" class="content hidden">', $decision->id);
            $ret .= $this->render_decision_paragraphs($decision);
            $ret .= $this->render_decision_documents($decision);
            $ret .= '</div>';
            $ret .= '<li>';
        }
        $ret .= '</ul>';
        return $ret;
    }
}


class InformeaSearchRendererTab3 extends InformeaSearchRendererTab2 {

    function render($results) {
        if(empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab3">';
        foreach($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }

}

class InformeaSearchRendererTab4 extends InformeaSearchRendererTab2 {

    function render($results) {
        if(empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab4">';
        foreach($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }
}

class InformeaSearchRendererTab5 extends InformeaSearchRendererTab2 {

    function render($results) {
        if(empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab5">';
        foreach($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }

}