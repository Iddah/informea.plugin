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


        /** Rewrite the permalinks so we can have pretty URLS when browsing portal for treaties/decisions etc */
        function create_rewrite_rules($rules) {
            $imea_rules = array();

            $imea_rules['(ro|es|fr)?/?rss?'] = 'index.php?spagename=rss-feed'; // http://informea/rss
            $imea_rules['(ro|es|fr)?/?events/rss?'] = 'index.php?pagename=download&id=events&entity=rss'; // http://informea/events/rss
            $imea_rules['(ro|es|fr)?/?highlights/rss?'] = 'index.php?pagename=download&id=highlights&entity=rss'; // http://informea/highlights/rss

            $imea_rules['(ro|es|fr)?/?events/(\d*)?/?$'] = 'index.php?pagename=events&fe_page=$matches[2]'; // http://informea/events/

            $imea_rules['(ro|es|fr)?/?countries/(map|parties|treaties|grid)?$'] = 'index.php?pagename=countries&expand=$matches[2]'; // http://informea/countries/grid
            $imea_rules['(ro|es|fr)?/?countries/?([A-z]{2})/?(membership|nfp|reports|map|plans|peblds|ecolex-legislation|ecolex-caselaw)?$'] = 'index.php?pagename=countries&id=$matches[2]&expand=$matches[3]'; // http://informea/countries/ISO2L
            $imea_rules['(ro|es|fr)?/?countries/(\d*)/?(membership|nfp|reports|map|plans|peblds|ecolex-legislation|ecolex-caselaw)?$'] = 'index.php?pagename=countries&id=$matches[2]&expand=$matches[3]'; // http://informea/countries/id/nfp
            $imea_rules['(ro|es|fr)?/?countries/(\d*)/?(sendmail)?/?(\d*)?/?(\d*)?$'] = 'index.php?pagename=countries&id=$matches[2]&expand=$matches[3]&id_contact=$matches[4]&id_parent=$matches[5]'; // http://informea/countries/id/sendmail/1234/1


            $imea_rules['(ro|es|fr)?/?treaties/grid$'] = 'index.php?pagename=treaties&category=Global&expand=grid'; // /grid suffix
            $imea_rules['(ro|es|fr)?/?treaties/icon$'] = 'index.php?pagename=treaties&category=Global&expand=icon'; // /grid suffix

            // Preserve line below for backward-compatibility with old URLs without tabs
            $imea_rules['(ro|es|fr)?/?treaties/(.*)/decisions/(\d*)'] = 'index.php?pagename=treaties&id=$matches[2]&id_decision=$matches[3]&showall=$matches[4]&expand=decision'; // http://informea/treaties/{odata_name}/decisions/{id}

            // http://informea/treaties/id/decisions/showall#decisions-id
            $imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(treaty|decisions|print|nfp|coverage)?/?(showall)?$'] = 'index.php?pagename=treaties&id=$matches[2]&expand=$matches[3]&showall=$matches[4]';
            // http://informea/treaties/id/sendmail/1234/1
            $imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(sendmail)?/?(\d*)?/?(\d*)?$'] = 'index.php?pagename=treaties&id=$matches[2]&expand=$matches[3]&id_contact=$matches[4]&id_parent=$matches[5]';
            $imea_rules['(ro|es|fr)?/?treaties/(.*)/sendmail/(\d*)/(\d*)?$'] = 'index.php?pagename=treaties&id=$matches[2]&expand=sendmail&id_contact=$matches[3]&id_parent=$matches[4]'; // http://informea/treaties/odata_name/sendmail/1234/1
            $imea_rules['(ro|es|fr)?/?treaties/(\d*)/?(\d*)?$'] = 'index.php?pagename=treaties&id_treaty=$matches[2]&id_treaty_article=$matches[3]'; // http://informea/treaties/id/id_treaty_article#article_id_paragraph_id

            //URL: /treaties/region/global, /treaties/region/Europe ...
            $imea_rules['(ro|es|fr)?/?treaties/region/(.*)/icon$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=icon'; // /icon suffix
            $imea_rules['(ro|es|fr)?/?treaties/region/(.*)/grid$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=grid'; // /grid suffix
            $imea_rules['(ro|es|fr)?/?treaties/region/(.*)$'] = 'index.php?pagename=treaties&category=$matches[2]&expand=icon'; // no suffix

            $imea_rules['(ro|es|fr)?/?treaties/(.*)/(general|treaty|decisions|print|nfp|coverage)?/?(showall)?$'] = 'index.php?pagename=treaties&id=$matches[2]&expand=$matches[3]&showall=$matches[4]'; // http://informea/treaties/id/decisions/showall#decisions-id
            $imea_rules['(ro|es|fr)?/?treaties/(.*)?$'] = 'index.php?pagename=treaties&id=$matches[2]'; // http://informea/treaties/{treaty} - identify by slug (we use odata_name)

            $imea_rules['(ro|es|fr)?/decisions$'] = 'index.php?pagename=decisions&expand=treaty'; // http://informea/decisions/treaty/
            $imea_rules['(ro|es|fr)?/decisions/terms/?$'] = 'index.php?pagename=decisions&expand=term'; // http://informea/decisions/term/

            $imea_rules['(ro|es|fr)?/?terms/(hierarchical|alphabetical|list)?$'] = 'index.php?pagename=terms&expand=$matches[2]'; // http://informea/terms/theme
            $imea_rules['(ro|es|fr)?/?terms/(\d*)/?(treaties|decisions|ecolex)?$'] = 'index.php?pagename=terms&id=$matches[2]&expand=$matches[3]'; // http://informea/terms/id/treaties

            $imea_rules['(ro|es|fr)?/?highlights/(.*)/(\d*)/?$'] = 'index.php?pagename=highlights&topic=$matches[2]&h_page=$matches[3]'; // http://informea/highlights/category_name & http://informea/highlights/topic/page
            $imea_rules['(ro|es|fr)?/?highlights/(\d*)/?$'] = 'index.php?pagename=highlights&h_page=$matches[2]'; // http://informea/highlights/category_name & http://informea/highlights/topic/page
            $imea_rules['(ro|es|fr)?/?highlights/(.*)$'] = 'index.php?pagename=highlights&topic=$matches[2]'; // http://informea/highlights/category_name & http://informea/highlights/topic

            //var_dump($rules);
            //var_dump($imea_rules + $rules);
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

            $vars[] = 'topic';

            $vars[] = 'entity';
            $vars[] = 'category';
            $vars[] = 'id_decision';
            $vars[] = 'id';
            $vars[] = 'page';
            $vars[] = 'fe_page';
            $vars[] = 'h_page';
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

    /** Rewrite URLs for custom INFORMEA permalinks */
    add_filter('query_vars', array('imeasite', 'query_vars'));
    add_filter('rewrite_rules_array', array('imeasite', 'create_rewrite_rules'));
    add_filter('body_class', array('imeasite', 'custom_body_class'));

    /* Filters for mobile devices */
    add_filter('template', 'serve_mobile');
    add_filter('option_template', 'serve_mobile');
    add_filter('option_stylesheet', 'serve_mobile');
}
