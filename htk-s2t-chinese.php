<?php
/*
Plugin Name: Hacktoolkit Simplified Chinese to Traditional Chinese
Plugin URI: https://www.hacktoolkit.com/htk-wordpress-plugins
Description: Translates Simplified Chinese to Traditional Chinese, and vice versa
Author: Hacktoolkit
Version: 0.1
Author URI: https://www.hacktoolkit.com

*/

class HtkS2TChinese {
    function HtkS2TChinese() {
        // https://codex.wordpress.org/Function_Reference/add_shortcode
        add_shortcode('htk_s2t_chinese', array($this, 'insert_h2t_chinese_button'));
    }

    function insert_h2t_chinese_button($atts) {

        $html = <<<EOF
<script type="text/javascript">
$(function() {
    var s2tWidget = '<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-s2t"><a href="javascript:void(0);">繁/简</a></li>';

    var englishWidget = $('li.lang-item-en.menu-item-67-en');
    if (englishWidget) {
        englishWidget.parent().append(s2tWidget);
    }
});

    var isTraditional = true;

    var HTK_S2T_CHINESE_IS_TRADITIONAL = 'HTK_S2T_CHINESE_IS_TRADITIONAL';

    function toggleChinese() {
        if (isTraditional) {
            $('#content').t2s();
            isTraditional = false;
            $.cookie(HTK_S2T_CHINESE_IS_TRADITIONAL, '0', { path: '/' });
        } else {
            $('#content').s2t();
            isTraditional = true;
            $.cookie(HTK_S2T_CHINESE_IS_TRADITIONAL, '1', { path: '/' });
        }
    }

    // on initial page load, toggle to the saved state
    if ($.cookie(HTK_S2T_CHINESE_IS_TRADITIONAL) === '1') {
        // do nothing, already traditional
    } else if ($.cookie(HTK_S2T_CHINESE_IS_TRADITIONAL) === '0') {
        // saved state was simplified, so toggle once
        toggleChinese();
    } else {
        // cookie is unset, so set it to traditional
        $.cookie(HTK_S2T_CHINESE_IS_TRADITIONAL, '1', { path: '/' });
    }

    $('#navigation').on('click', '.menu-item-s2t', toggleChinese);
    $('#top-nav').on('click', '.menu-item-s2t', toggleChinese);
</script>
EOF;
        echo $html;
    }
}

$obj = new HtkS2TChinese();

?>
