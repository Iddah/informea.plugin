<?php

if (!class_exists('imea_index_page')) {

    function filter_where($where = '') {
        $where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
        return $where;
    }

    class imea_index_page extends imea_page_base_page {

        function get_slider_news() {
            global $wpdb;
            global $post;
            $ret = array();

            $base_url = get_bloginfo('template_directory');
            $pictures = $wpdb->get_results('SELECT * FROM imea_pictures WHERE is_slider = 1 ORDER BY rand() ');
            $cp = count($pictures);
            $cat = get_category_by_slug('news');
            $c = count($pictures);

            // TODO : FIX THIS AS IT INFLUENCES OTHER QUERIES !!!!!! -- add_filter( 'posts_where', 'filter_where' );
            $posts = query_posts("showposts=$c&orderby=rand&meta_key=syndication_feed_id&cat={$cat->cat_ID}");

            $wpq = new WP_Query(array('post_date' => 'DATE(NOW())', 'post_type' => 'post', 'orderby' => 'post_date',
                'posts_per_page' => $c, 'order' => 'DESC'));
            $posts = array();
            while ($wpq->have_posts()) {
                $wpq->the_post();
                $posts[] = $post;
            }

            for ($i = 0; $i < $c; $i++) {
                $pic = $pictures[$i];
                $ob = new StdClass();
                $ob->image_url = get_bloginfo('url') . '/wp-content/uploads/pictures/slide_images/' . $pic->filename;
                $ob->image_copyright = $pic->copyright;
                $ob->image_title = $pic->title;
                $ob->has_content = FALSE;
                if (isset($posts[$i])) {
                    $ob->has_content = TRUE;
                    $post = $posts[$i];
                    $ob->title = subwords($post->post_title, 15);
                    $ob->date = mysql2date('j F, Y', $post->post_date);
                    $ob->url = get_permalink($post->ID);
                }
                $ret[] = $ob;
            }
            return $ret;
        }
    }
}



