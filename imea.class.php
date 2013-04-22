<?php
if (!class_exists('imeasite')) {
    /**
     * Plugin class.
     */
    class imeasite {
        function install() {
            imea_log("Activating plugin ...");
        }

        /**
         * Uninstall the plugin, at the moment doesn't seem to work the Wordpress itself.
         * Throws error while trying to uninstall (FTP page).
         * Use uninstall.php method.
         */
        function uninstall() {
            imea_log("WARN! Uninstalling plugin is *NOT IMPLEMENTED* -> Use uninstall.php");
        }

        function add_home_image_link($nav) {
            $base_url = get_bloginfo('url');
            $theme_url = get_bloginfo('template_directory');
            return "<li><a href='" . $base_url . "' class='home'>Home</a></li>" . $nav;
        }


        /**
         * Fill the breadcrumb for exceptional pages (not treaties/decisions/countries/terms/events - handled by their respective classes).
         * 'index' post_name has no need of breadcrumbtrail.
         */
        function breadcrumbtrail() {
            global $post;
            if ($post) {
                switch ($post->post_name) {
                    /* About sub-pages */
                    case 'introduction':
                    case 'process-and-governance':
                    case 'api-specifications':
                    case 'api-specifications':
                    case 'multimedia':
                    case 'new-members':
                        return sprintf(" &raquo; %s &raquo; <span class='current'>%s</span>", __('About', 'informea'), $post->post_title);
                        break;
                    /* Treaties pages */
                    case 'treaties':
                        global $page_data;
                        if (empty($page_data->treaty)) {
                            $region = $page_data->category;
                            switch ($region) {
                                case '':
                                    $region = 'Global';
                                    break;
                                default:
                                    $region = urldecode($page_data->category) . ' region';
                            }
                            return sprintf(" &raquo; %s &raquo; <span class='current'>%s</span>", $post->post_title, $region);
                        } else {
                            global $expand;
                            $s = ucfirst($expand);
                            switch ($expand) {
                                case 'treaty':
                                    $s = 'Treaty text';
                                    break;
                                case 'decisions':
                                    $s = 'Decisions';
                                    break;
                                case 'nfp':
                                    $s = 'Focal points';
                                    break;
                                case 'coverage':
                                    $s = 'Map and Membership';
                                    break;
                            }
                            return sprintf(' &raquo; <a href="/treaties"><span>%s</span></a> &raquo; <span class="current">%s</span> &raquo; <span class="current">%s</span>',
                                __('Treaties', 'informea'), $page_data->treaty->short_title, $s);
                        }
                        break;
                    /* Countries pages */
                    case 'countries':
                        global $page_data;
                        if (empty($page_data->country)) {
                            return sprintf(" &raquo; <span class='current'>%s</span>", $post->post_title);
                        } else {
                            global $expand;
                            $s = ucfirst($expand);
                            switch ($expand) {
                                case 'map':
                                    $s = __('Map &amp; sites', 'informea');
                                    break;
                                case 'nfp':
                                    $s = __('Focal points', 'informea');
                                    break;
                                case 'ecolex/legislation':
                                    $s = __('Ecolex legislation', 'informea');
                                    break;
                                case 'ecolex/caselaw':
                                    $s = __('Ecolex case law', 'informea');
                                    break;
                            }
                            return sprintf(' &raquo; <a href="/countries"><span>%s</span></a> &raquo; %s &raquo; <span class="current">%s</span>',
                                __('Countries', 'informea'), $page_data->country->name, $s);
                        }
                        break;
                    /* Terms pages */
                    case 'terms':
                        global $page_data;
                        if (empty($page_data->term)) {
                            return sprintf(" &raquo; <span class='current'>%s</span>", $post->post_title);
                        } else {
                            global $tab;
                            $s = ucfirst($tab);
                            switch ($tab) {
                                //
                            }
                            return sprintf(' &raquo; <a href="/terms"><span>%s</span></a> &raquo; %s &raquo; <span class="current">%s</span>',
                                __('Terms', 'informea'), $page_data->term->term, $s);
                        }
                        break;
                    /* Highlights pages */
                    case 'highlights':
                        global $query_category, $page_data;
                        if ($page_data->is_search()) {
                            return sprintf(' &raquo; <a href="/highlights">%s</a> &raquo; <span class="current">%s</span>', __('Highlights', 'informea'), __('Search results', 'informea'));
                        } else {
                            if (empty($query_category)) {
                                return sprintf(' &raquo; <span class="current">%s</span>', __('Highlights', 'informea'));
                            } else {
                                return sprintf(' &raquo; <a href="/highlights">%s</a> &raquo; <span class="current">%s</span>', __('Highlights', 'informea'), $query_category->title);
                            }
                        }
                        break;
                    /* Events pages */
                    case 'events':
                        global $page_data;
                        if ($page_data->empty_search()) {
                            return sprintf(' &raquo; <span class="current">%s</span>', __('Events', 'informea'));
                        } else {
                            return sprintf(' &raquo; <a href="/events">%s</a> &raquo; <span class="current">%s</span>', __('Events', 'informea'), __('Search results', 'informea'));
                        }
                        break;
                    case 'search':
                        return " &raquo; MEA Explorer &raquo; <span class='current'>Search results</span>";
                        break;
                    default:
                        return sprintf(" &raquo; <span class='current'>%s</span>", $post->post_title, $region);
                }
            } else { // Error page seem not to have $post
                return ' &raquo; <span class="current">' . __('Error') . '</span>';
            }
        }


        /** Rewrite the permalinks so we can have pretty URLS when browsing portal for treaties/decisions etc */
        function create_rewrite_rules($rules) {
            $imea_rules = array();

            $imea_rules['(ro|es|fr)?/?rss?'] = 'index.php?pagename=rss-feeds&lng=$matches[1]'; // http://informea/rss

            $imea_rules['(ro|es|fr)?/?events/(list|calendar)?$'] = 'index.php?pagename=events&expand=$matches[2]&lng=$matches[1]'; // http://informea/events/list
            $imea_rules['(ro|es|fr)?/?events/rss?'] = 'index.php?pagename=events&show_rss=True&lng=$matches[1]'; // http://informea/events/rss

            $imea_rules['(ro|es|fr)?/?countries/(grid|alphabetical|map)?$'] = 'index.php?pagename=countries&mode=$matches[2]&lng=$matches[1]'; // http://informea/countries/grid
            $imea_rules['(ro|es|fr)?/?countries/(\d*)/?(membership|nfp|reports|map|plans|peblds|ecolex/legislation|ecolex/caselaw)?$'] = 'index.php?pagename=countries&id_country=$matches[2]&expand=$matches[3]&lng=$matches[1]'; // http://informea/countries/id/nfp
            $imea_rules['(ro|es|fr)?/?countries/(\d*)/?(sendmail)?/?(\d*)?/?(\d*)?$'] = 'index.php?pagename=countries&id_country=$matches[2]&expand=$matches[3]&id_contact=$matches[4]&id_parent=$matches[5]&lng=$matches[1]'; // http://informea/countries/id/sendmail/1234/1


            $imea_rules['(ro|es|fr)?/?treaties/grid$'] = 'index.php?pagename=treaties&category=Global&expand=grid&lng=$matches[1]'; // /grid suffix
            $imea_rules['(ro|es|fr)?/?treaties/icon$'] = 'index.php?pagename=treaties&category=Global&expand=icon&lng=$matches[1]'; // /grid suffix

            // Preserve line below for backward-compatibility with old URLs without tabs
            $imea_rules['(ro|es|fr)?/?treaties/(.*)/decisions/(\d*)'] = 'index.php?pagename=treaties&id=$matches[2]&id_decision=$matches[3]&showall=$matches[4]&lng=$matches[1]&expand=decision'; // http://informea/treaties/{odata_name}/decisions/{id}
            $imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(sendmail)?/?(\d*)?/?(\d*)?$'] = 'index.php?pagename=treaties&id=$matches[2]&expand=$matches[3]&id_contact=$matches[4]&id_parent=$matches[5]&lng=$matches[1]'; // http://informea/treaties/id/sendmail/1234/1
            $imea_rules['(ro|es|fr)?/?treaties/(.*)/sendmail/(\d*)/(\d*)?$'] = 'index.php?pagename=treaties&id=$matches[2]&expand=sendmail&id_contact=$matches[3]&id_parent=$matches[4]&lng=$matches[1]'; // http://informea/treaties/odata_name/sendmail/1234/1
            $imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(\d*)?$'] = 'index.php?pagename=treaties&id_treaty=$matches[2]&id_treaty_article=$matches[3]&lng=$matches[1]'; // http://informea/treaties/id/id_treaty_article#article_id_paragraph_id
            $imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(treaty|decisions|print|nfp|coverage)?/?(showall)?$'] = 'index.php?pagename=treaties&id_treaty=$matches[2]&expand=$matches[3]&showall=$matches[4]&lng=$matches[1]'; // http://informea/treaties/id/decisions/showall#decisions-id

            //URL: /treaties/region/global, /treaties/region/Europe ...
            $imea_rules['(ro|es|fr)?/?treaties/region/(.*)/icon$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=icon&lng=$matches[1]'; // /icon suffix
            $imea_rules['(ro|es|fr)?/?treaties/region/(.*)/grid$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=grid&lng=$matches[1]'; // /grid suffix
            $imea_rules['(ro|es|fr)?/?treaties/region/(.*)$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=icon&lng=$matches[1]'; // no suffix

            $imea_rules['(ro|es|fr)?/?treaties/(.*)/(general|treaty|decisions|print|nfp|coverage)?/?(showall)?$'] = 'index.php?pagename=treaties&id=$matches[2]&expand=$matches[3]&showall=$matches[4]&lng=$matches[1]'; // http://informea/treaties/id/decisions/showall#decisions-id
            $imea_rules['(ro|es|fr)?/?treaties/(.*)?$'] = 'index.php?pagename=treaties&id=$matches[2]&lng=$matches[1]'; // http://informea/treaties/{treaty} - identify by slug (we use odata_name)

            $imea_rules['(ro|es|fr)?/decisions/treaty/?$'] = 'index.php?pagename=decisions&expand=treaty&lng=$matches[1]'; // http://informea/decisions/treaty/
            $imea_rules['(ro|es|fr)?/decisions/term/?$'] = 'index.php?pagename=decisions&expand=term&lng=$matches[1]'; // http://informea/decisions/term/

            $imea_rules['(ro|es|fr)?/?terms/(theme|alphabet|list)?$'] = 'index.php?pagename=terms&expand=$matches[2]&lng=$matches[1]'; // http://informea/terms/theme
            $imea_rules['(ro|es|fr)?/?terms/(\d*)/?(treaties|decisions|ecolex)?$'] = 'index.php?pagename=terms&id_term=$matches[2]&tab=$matches[3]&lng=$matches[1]'; // http://informea/terms/id/treaties

            $imea_rules['(ro|es|fr)?/?highlights/rss?'] = 'index.php?pagename=highlights&show_rss=True&lng=$matches[1]'; // http://informea/highlights/rss
            $imea_rules['(ro|es|fr)?/?highlights/(.*)/(\d*)$'] = 'index.php?pagename=highlights&highlight_category=$matches[2]&page=$matches[3]&lng=$matches[1]'; // http://informea/highlights/category_name & http://informea/highlights/category_name/page (pagination)
            $imea_rules['(ro|es|fr)?/?highlights/(.*)$'] = 'index.php?pagename=highlights&highlight_category=$matches[2]&lng=$matches[1]'; // http://informea/highlights/category_name & http://informea/highlights/category_name

            //var_dump($rules);
            return $imea_rules + $rules;
        }

        /** Append custom parameters to Wordpress list of known query variables ($wp_rewrite->query_vars) */
        function query_vars($vars) {
            $vars[] = 'action';
            $vars[] = 'treaty';
            $vars[] = 'id_treaty';
            $vars[] = 'id_treaty_article';
            $vars[] = 'expand';
            $vars[] = 'tab';
            $vars[] = 'id_term';
            $vars[] = 'id_country';
            $vars[] = 'showall';

            $vars[] = 'mode';

            $vars[] = 's_column';
            $vars[] = 's_order';

            $vars[] = 'lng';
            $vars[] = 'pagename';
            $vars[] = 'id_contact';
            $vars[] = 'id_parent';

            $vars[] = 'highlight_category';
            $vars[] = 'highlight_id';

            $vars[] = 'show_rss';
            $vars[] = 'category';
            $vars[] = 'id_decision';
            $vars[] = 'id';
            return $vars;
        }

        function custom_body_class($classes) {
            global $wp_query;
            if (is_page()) {
                $page_id = $wp_query->get_queried_object_id();
                $post = get_page($page_id);
                $classes[] = 'imea-' . $post->post_name;
            }
            return $classes;
        }
    }

    $mobile_detect = new Mobile_Detect();
    function serve_mobile($theme) {
        global $mobile_detect;

        //return 'mobile';
        // return $theme;
        // TODO - enable this when we have special theme
        //if($mobile_detect->isMobile()) {
        //	$theme = 'mobile';
        //}
        return $theme;
    }

    if (FALSE) {
        /** Add filter to intercept menu creation and inject the Home link */
        add_filter('wp_nav_menu_items', array('imeasite', 'add_home_image_link'));
    }

    /** Add filter to intercept breadcrumbtrail and handle special pages (About/News) to correctly set breacrumbtrail */
    add_filter('breadcrumbtrail', array('imeasite', 'breadcrumbtrail'));

    /** Rewrite URLs for custom INFORMEA permalinks */
    add_filter('query_vars', array('imeasite', 'query_vars'));
    add_filter('rewrite_rules_array', array('imeasite', 'create_rewrite_rules'));
    add_filter('body_class', array('imeasite', 'custom_body_class'));

    /* Filters for mobile devices */
    add_filter('template', 'serve_mobile');
    add_filter('option_template', 'serve_mobile');
    add_filter('option_stylesheet', 'serve_mobile');
}
