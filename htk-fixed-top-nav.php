<?php
/*
Plugin Name: Hacktoolkit Fixed Top Nav
Plugin URI: https://www.hacktoolkit.com/htk-wordpress-plugins
Description: Sets the top nav fixed position style
Author: Hacktoolkit
Version: 0.1
Author URI: https://www.hacktoolkit.com

*/

class HtkFixedTopNav {

	function HtkFixedTopNav() {
        // https://codex.wordpress.org/Function_Reference/add_shortcode
        add_shortcode('htk_fixed_top_nav', array($this, 'insert_fixed_top_nav_css'));
	}

    function insert_fixed_top_nav_css($atts) {
        // https://codex.wordpress.org/Function_Reference/show_admin_bar
        $WP_ADMIN_BAR_HEIGHT = 32;
        $DEFAULT_TOP_NAV_HEIGHT = 31;
        $topnav_height = isset($atts['height'])? intval($atts['height']) : $DEFAULT_TOP_NAV_HEIGHT;
        $top = is_admin_bar_showing() ? $WP_ADMIN_BAR_HEIGHT : 0;
        // TODO: how is #wrapper accomodating for wp_admin_bar ?
        //$wrapper_margin_top = $top + $topnav_height;
        $wrapper_margin_top = $topnav_height;

        $style = <<<EOF
<!-- HTK Fixed Top Nav -->
<style type="text/css">
#top {
  position: fixed;
  top: ${top}px;
  height: 31px;
  width: 100%;
  z-index: 1000000;
  background-color: rgba(255, 255, 255, 0.9);
}

@media only screen and (min-width: 768px) {
  #wrapper {
    /* offset for fixed top nav */
    margin-top: ${wrapper_margin_top}px;
  }
}
</style>
<!-- / HTK Fixed Top Nav -->
EOF;
        echo $style;
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

$obj = new HtkFixedTopNav();

?>
