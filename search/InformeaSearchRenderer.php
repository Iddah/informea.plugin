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
        $css = count($treaty->articles) || count($treaty->decisions) ? 'toggle-result' : 'ajax-expand';
        $ret .= sprintf('<a data-role="treaty" data-id="%s" data-toggle="treaty-%s" href="javascript:void(0);" class="%s">', $treaty->id, $treaty->id, $css);
        $ret .= sprintf('<i class="icon icon-plus-sign"></i>');
        $ret .= sprintf('<span class="thumbnail %s middle"></span>', $treaty->odata_name);
        $ret .= sprintf('%s</a>', $treaty->short_title);
        $ret .= sprintf('<div id="treaty-%s" class="hidden">', $treaty->id);
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
        if (empty($treaty->articles)) {
            return '';
        }
        $ret = sprintf('<h3>%s</h3>', __('Articles', 'informea'));
        $ret .= '<ul class="articles">';
        foreach ($treaty->articles as $article) {
            $url = sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s', get_bloginfo('url'), $treaty->odata_name, $article->id, $article->id);
            $ret .= '<li>';
            $css = count($article->paragraphs) ? 'toggle-result' : 'ajax-expand';
            $label = !empty($article->official_order) ? sprintf('%s %s', $article->official_order, $article->title) : $article->title;
            $ret .= sprintf('<a data-role="treaty_article" data-id="%s" data-toggle="treaty-article-%s" class="%s" href="javascript:void(0);"><i class="icon-plus-sign"></i>%s</a>', $article->id, $article->id, $css, $label);
            $ret .= sprintf('<a href="%s" target="_blank"><i class="icon icon-arrow-right"></i></a>', $url);
            $ret .= sprintf('<div id="treaty-article-%s" class="hidden">', $article->id);
            $ret .= $this->render_treaty_paragraphs($article);
            $ret .= '</div>';
            $ret .= '<li>';
        }
        $ret .= '</ul>';
        return $ret;
    }


    function render_treaty_paragraphs($article) {
        $ret = '';
        if (count($article->paragraphs) > 0) {
            $ret .= '<h3>Paragraphs</h3>';
            $ret .= '<ul class="paragraphs">';
            foreach ($article->paragraphs as $paragraph) {
                $treaty = CacheManager::load_treaty($article->id_treaty);
                $url = sprintf('%s/treaties/%s?id_treaty_article=%s#article_%s_paragraph_%s', get_bloginfo('url'), $treaty->odata_name, $article->id, $article->id, $paragraph->id);
                $ret .= '<li>';
                $ret .= sprintf('<div class="highlight">%s <a href="%s" target="_blank"><i class="icon icon-arrow-right"></i></a></div>', $paragraph->content, $url);
                $ret .= '</li>';
            }
            $ret .= '</ul>';
        } else {
        }
        return $ret;
    }

    function render_decision_paragraphs($decision) {
        $ret = '';
        if (count($decision->paragraphs) > 0) {
            $ret .= '<h3>Paragraphs</h3>';
            $ret .= '<ul class="paragraphs">';
            foreach ($decision->paragraphs as $paragraph) {
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
        if (count($decision->documents) > 0) {
            $ret .= '<h3>Documents</h3>';
            $ret .= '<ul class="documents">';
            foreach ($decision->documents as $document) {
                $ret .= '<li>';
                $title = $document->filename;
                $url = sprintf('%s/download?entity=decision_document&id=%s', get_bloginfo('url'), $document->id);
                $ret .= sprintf('<a data-id="%s" data-toggle="decision-document-%s" data-role="decision_document" class="ajax-expand" href="javascript:void(0);"><i class="icon icon-plus-sign"></i>%s</a>', $document->id, $document->id, $title);
                $ret .= sprintf('<a href="%s"><i class="icon icon-download"></i></a>', $url);
                $ret .= sprintf('<div id="decision-document-%s" class="hidden"></div>', $document->id);
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
        if (empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul id="search_results" class="search-results tab1">';
        foreach ($results as $row) {
            $ret .= call_user_func(array($this, 'render_' . $row->entity_type), $row);
        }
        $ret .= '</ul>';
        return $ret;
    }


    function render_decisions($treaty) {
        return '';
    }

    function render_decision($decision) {
        $treaty = CacheManager::load_treaty($decision->id_treaty);
        $url = sprintf('%s/treaties/%s/decisions?showall=true#decision-%s', get_bloginfo('url'), $treaty->odata_name, $decision->id);
        $css = (count($decision->paragraphs) > 0 || count($decision->documents) > 0) ? 'toggle-result' : 'ajax-expand';
        $label = sprintf('%s, <strong>%s</strong> %s - %s', mysql2date('j F Y', $decision->published), $decision->treaty_title, $decision->number, $decision->short_title);
        $ret = '<li>';
        $logo = sprintf('<span class="thumbnail middle %s pull-left tab1"></span>', $treaty->odata_name);
        $ret .= sprintf('<a data-id="%s" data-toggle="decision-%s" data-role="decision" href="javascript:void(0);" class="%s"><i class="icon-plus-sign pull-left middle"></i>%s %s</a>', $decision->id, $decision->id, $css, $logo, $label);
        $ret .= sprintf('<a href="%s" target="_blank"><i class="icon icon-arrow-right"></i></a>', $url);
        $ret .= sprintf('<div id="decision-%s" class="content hidden">', $decision->id);
        $ret .= $this->render_decision_paragraphs($decision);
        $ret .= $this->render_decision_documents($decision);
        $ret .= '</div>';
        $ret .= '<div class="clear"></div>';
        $ret .= '</li>';
        return $ret;
    }

    function render_event($row) {
        $ret = '';
        $tooltips = array(__('Convention logo', 'informea'), __('Click to open decision into treaty context', 'informea'));
        $ret .= '<li class="event">';
        $ret .= sprintf('<a id="arrow-event-%s" href="javascript:void(0);" class="arrow left"></a>', $row->id);
        $ret .= sprintf('<div class="logo-medium left" title="%s"><img src="%s" /></div>', $tooltips[0], $row->logo_medium);
        $ret .= '<div class="title left">';
        $ret .= empty($row->event_url) ? subwords($row->title) : sprintf('<a href="%s" target="_blank" title="%s" class="left">%s</a>', $row->event_url, $row->title, subwords($row->title));
        $ret .= sprintf('&nbsp; - %s', format_mysql_date($row->start));
        $ret .= '</div>';
        $ret .= '</li>';
        return $ret;
    }
}


class InformeaSearchRendererTab1Ajax extends InformeaSearchRendererTab1 {

    function render($results) {
        $ret = '';
        foreach ($results as $row) {
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
        if (empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab2">';
        foreach ($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }

    function render_decisions($treaty) {
        if (empty($treaty->decisions)) {
            return '';
        }
        $ret = '<h3>Decisions</h3>';
        $ret .= '<ul>';
        foreach ($treaty->decisions as $decision) {
            $label = sprintf('%s - %s', $decision->number, subwords($decision->short_title, 10));
            $url = sprintf('%s/treaties/%s/decisions?showall=true#decision_%s', get_bloginfo('url'), $treaty->odata_name, $decision->id);
            $css = (count($decision->paragraphs) > 0 || count($decision->documents) > 0) ? 'toggle-result' : 'ajax-expand';
            $ret .= '<li>';
            $ret .= sprintf('<a data-toggle="decision-%s" data-id="%s" data-role="decision" href="javascript:void(0);" class="%s arrow closed left"><i class="icon icon-plus-sign"></i>%s</a>', $decision->id, $decision->id, $css, $label);
            $ret .= sprintf('<a href="%s" target="_blank"><i class="icon icon-arrow-right"></i></a>', $url);
            $ret .= sprintf('<div id="decision-%s" class="hidden">', $decision->id);
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
        if (empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab3">';
        foreach ($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }
}

class InformeaSearchRendererTab4 extends InformeaSearchRendererTab2 {

    function render($results) {
        if (empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab4">';
        foreach ($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }
}

class InformeaSearchRendererTab5 extends InformeaSearchRendererTab2 {

    function render($results) {
        if (empty($results)) {
            return $this->no_results();
        }
        $ret = '<ul class="search-results tab5">';
        foreach ($results as $treaty) {
            $ret .= $this->render_treaty($treaty);
        }
        $ret .= '</ul>';
        return $ret;
    }
}