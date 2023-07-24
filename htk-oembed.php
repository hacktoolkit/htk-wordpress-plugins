<?php
/*
Plugin Name: Hacktoolkit Oembed
Plugin URI: https://www.hacktoolkit.com/htk-wordpress-plugins
Description: Embeds 3rd party website content via Oembed. Usages: <code>[htk_oembed]</code>.
Author: Hacktoolkit
Version: 0.1
Author URI: https://www.hacktoolkit.com

*/

class HtkOembed {

    function HtkOembed() {
        // https://codex.wordpress.org/Function_Reference/add_shortcode
        add_shortcode('htk_oembed', array($this, 'display_oembed'));
    }

    // htk_oembed
    function display_oembed($atts) {
        $url = $atts['url'];

        $ch= curl_init();
        curl_setopt_array($ch,array(
            CURLOPT_URL=> $url,
            CURLOPT_HEADER=>1,
            CURLOPT_RETURNTRANSFER=>1
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        list($headers, $content) = explode("\r\n\r\n", $response, 2);
        return $content;
    }

}

$obj = new HtkOembed();

?>
