<?php
/*
Plugin Name: Hacktoolkit Embed Content
Plugin URI: https://www.hacktoolkit.com/htk-wordpress-plugins
Description: Generates a navigation menu and embed it within a post or another page. Usages: <code>[htk_menu]</code>, <code>[htk_page_menu]</code>, <code>[htk_post POST_ID]</code>, <code>[htk_posts category_name=CATEGORY_NAME]</code>, <code>[htk_post_list category_name=CATEGORY_NAME]</code>.
Author: Hacktoolkit
Version: 0.2
Author URI: https://www.hacktoolkit.com

*/

class HtkEmbedContent {

    function HtkEmbedContent() {
        // https://codex.wordpress.org/Function_Reference/add_shortcode
        add_shortcode('htk_menu', array($this, 'display_menu'));
        add_shortcode('htk_page_menu', array($this, 'display_page_menu'));
        add_shortcode('htk_post', array($this, 'display_post'));
        add_shortcode('htk_posts', array($this, 'display_posts'));
        add_shortcode('htk_post_list', array($this, 'display_post_list'));
    }

    // htk_page_menu
    function display_page_menu($atts) {
        return wp_page_menu();
    }

    // htk_menu
    function display_menu($atts) {
        $args = array(
            'echo' => false,
            'depth' => 0,
        );
        $menu_html = wp_nav_menu($args);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $menu_html);
        $dom = new DOMXpath($doc);
        $elements = $dom->query("//li[contains(@class, 'current-menu-item')]");
        $html = '';
        if (!is_null($elements)) {
            foreach ($elements as $element) {
                $nodes = $element->childNodes;
                $is_first = true;
                foreach ($nodes as $node) {
                    if ($is_first) {
                        // skip
                        $is_first = false;
                        continue;
                    } else {
                        $html .= $doc->saveHTML($node);
                    }
                }
                break;
            }
        }
        return $html;
    }

    function display_menu2($atts) {
        // https://codex.wordpress.org/Function_Reference/shortcode_atts
        $pairs = array(
            'show_children' => true,
            'sort_by_title' => false,
        );
        $a = shortcode_atts($pairs, $atts, 'htk_menu');
        $show_children = $a['show_children'];
        $sort_by_title = $a['sort_by_title'];
        $pages = $this->_get_pages($show_children, $sort_by_title);
        return '<ul class="htk-embed-menu">'.$pages.'</ul>';
    }

    // htk_post
    function display_post($atts) {
        $atts['limit'] = 1;
        //return '';
        return $this->display_posts($atts);

    }

    // [htk_posts category_name=CATEGORY_NAME]
    function display_posts($atts) {
        $category_name = isset($atts['category_name'])? $atts['category_name'] : '';
        $limit = isset($atts['limit'])? $atts['limit'] : -1;
        $offset = isset($atts['offset'])? $atts['offset'] : 0;

        $raw_posts = $this->_get_posts_by_category_name($category_name, $limit, $offset);
        $count = 0;
        $posts = array();
        foreach ($raw_posts as $post) {
            if ($limit > 0 && $count >= $limit) {
                break;
            }
            $posts[] = '<p>'.do_shortcode($post->post_content).'</p>';
            ++$count;
        }
        $html = implode('', $posts);
        return $html;
    }

    // [htk_post_list category_name=CATEGORY_NAME]
    function display_post_list($atts) {
        $category_name = isset($atts['category_name'])? $atts['category_name'] : '';
        $limit = isset($atts['limit'])? $atts['limit'] : -1;
        $offset = isset($atts['offset'])? $atts['offset'] : 0;

        $raw_posts = $this->_get_posts_by_category_name($category_name, $limit, $offset);
        $count = 0;
        $posts_list = array();
        foreach ($raw_posts as $post) {
            if ($limit > 0 && $count >= $limit) {
                break;
            }
            $posts_list[] = '<li><a href="'.get_permalink($post).'">'.$post->post_title.'</a></li>';
            ++$count;
        }
        $html = '<ul class="htk-embed-posts-list">'.implode('', $posts_list).'</ul>';
        return $html;
    }

    // helpers
    function _get_pages($show_children=true, $sort_by_title=false, $depth=0) {
        // https://developer.wordpress.org/reference/functions/wp_list_pages/
        global $post;

        $child_of = $show_children ? $post->ID : $post->post_parent;
        $sort_column = $sort_by_title ? 'post_title' : 'menu_order, post_title';

        $args = array(
            'child_of' => $child_of,
            'sort_column' => $sort_column,
            'title_li' => null,
            'echo' => false,
            'depth' => $depth,
        );
        $pages = wp_list_pages($args);
        return $pages;
    }

    function _get_posts_by_category_name($category_name, $limit, $offset) {
        // https://developer.wordpress.org/reference/functions/get_posts/
        $args = array(
            'category_name' => $category_name,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
            'numberposts' => $limit,
            'offset' => $offset,
        );
        $posts = get_posts($args);
        return $posts;
    }
}

$obj = new HtkEmbedContent();

?>
