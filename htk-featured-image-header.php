<?php
/*
Plugin Name: Hacktoolkit Featured Image Header
Plugin URI: https://www.hacktoolkit.com/htk-wordpress-plugins
Description: Sets featured image of a post or page as the header backgrond image when one exists.  Usage: <code>[htk_featured_image_header]</code>.
Author: Hacktoolkit
Version: 0.1
Author URI: https://www.hacktoolkit.com

*/

class HtkFeaturedImageHeader {

	function HtkFeaturedImageHeader() {
        // https://codex.wordpress.org/Function_Reference/add_shortcode
        add_shortcode('htk_featured_image_header', array($this, 'insert_featured_image_header_css'));
	}

    function insert_featured_image_header_css($atts) {
        // https://woocommerce.com/2013/08/how-to-create-a-unique-wordpress-website-15-top-hacks-for-canvas/#gist6205071
        if (has_post_thumbnail()) {
            $DEFAULT_HEIGHT = 300;
            // the current post has a thumbnail
            $page_id = get_the_ID();
            // https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
            // 'thumbnail', 'medium', 'medium_large', 'large', 'custom-size'
            $image_size = 'original';
            $featured_image_tag = get_the_post_thumbnail(null, 'post-thumbnail', '');
            $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), $image_size );
            $image_url = sizeof($featured_image > 0) ? $featured_image[0] : '';
            $height = isset($atts['height'])? intval($atts['height']) : $DEFAULT_HEIGHT;
            $style = <<<EOF
<!-- HTK Featured Image Header -->
<style type="text/css">
.page-id-${page_id} #header {
    max-width: 100% !important;
    height: ${height}px;
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
    background-color: transparent;
    background-image: url("${image_url}");
}
</style>
<!-- / HTK Featured Image Header -->
EOF;
            echo $style;
        } else {
            // the current post lacks a thumbnail
        }
    }

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
}

$obj = new HtkFeaturedImageHeader();

?>
