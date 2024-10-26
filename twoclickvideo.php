<?php

/**
* Plugin Name:       2-Klick Video for WPBakery Page Builder
* Description:       Dieses Plugin fügt WPBakery Page Builder den Block "2-Klick Video" hinzu. In Verbindung mit Cookiebot lassen sich so Videos von z.B. Youtube oder Vimeo ganz einfach als Zwei-Klick-Lösung in Ihre Seite einbinden. Damit bleiben Sie mit ihrer Seite DSGVO-konform.
* Version:           0.0.2
* Requires at least: 5.5
* Requires PHP:      7.0
* Author:            Unit08
* Author URI:        https://unit08.de/
* License:           GPL v3
* License URI:       https://www.gnu.org/licenses/gpl-3.0.html
*/

function twocv_vcContent($atts)
{
    extract(
        shortcode_atts(
            array(
                'title' => 'title',
                'videolink' => 'videolink',
                'privacypage' => 'privacypage'
            ),
            vc_map_get_attributes('twoclickvideo', $atts)
        )
    );

    $video_div =
        '<div class="wpb_video_widget wpb_content_element vc_clearfix vc_video-aspect-ratio-169 vc_video-el-width-100 vc_video-align-left">
        <div class="wpb_wrapper">
        <div class="wpb_video_wrapper cookieconsent-optin-marketing"><iframe title="' . esc_html($title) . '" data-cookieconsent="marketing" data-cookieblock-src="' . esc_url($videolink) . '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" width="800" height="450" frameborder="0"></iframe></div>
        <div class="cookieconsent-optout-marketing">
        <p>Ich möchte eingebundene Inhalte von YouTube auf dieser Webseite sehen.</p>
        <p>Bitte <a href="javascript:Cookiebot.renew()">akzeptieren Sie die Marketing Cookies</a> um das Video angezeigt zu bekommen.</p>
        <p>Hierbei werden personenbezogene Daten (IP-Adresse o.ä.) an den Anbieter und somit ggf. auch in Drittstaaten übermittelt, in welchen kein mit der EU vergleichbares Datenschutzniveau garantiert werden kann. Weitere Informationen zum Datenschutz bei Google finden Sie unter <a href="https://policies.google.com/privacy?hl=de" target="_blank">Datenschutzerklärung &amp; Nutzungsbedingungen Google</a> sowie in unserer Datenschutzerklärung unter <a href="' . esc_url(get_permalink($privacypage)) . '">' . esc_url(get_permalink($privacypage)) . '</a>.</p>
        <p>Durch Aktivierung des Drittdienstes erteilen Sie eine Einwilligung i.S.d. Artt. 6 Abs. 1 lit. a, 49 Abs. 1 lit a DSGVO und § 25 Abs. 1 TTDSG. Diese Einwilligung kann jederzeit mit Wirkung für die Zukunft <a href="javascript:Cookiebot.withdraw();">hier</a> widerrufen werden.</p>
        </div>
        </div>
    </div>';

    return do_shortcode($video_div);
}

add_shortcode('twoclickvideo', 'twocv_vcContent');
add_action('vc_before_init', 'twocv_vcElement', 12);

function twocv_vcElement()
{

    if (!function_exists('vc_map')) {
        // Error message maybe?
        return;
    }

    $policyPageId = (int) get_option('wp_page_for_privacy_policy');

    vc_map(
        array(
            "name" => __("2-Klick Video"),
            "base" => "twoclickvideo",
            "class" => "",
            "category" => __("Content"),
            "icon" => plugin_dir_url(__FILE__) . "/img/icon-128x128.png",
            "params"   => array(
                array(
                    "type"        => "textfield",
                    "heading"     => __("Titel"),
                    "param_name"  => "title",
                    "admin_label" => true,
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __("Videolink"),
                    "param_name"  => "videolink",
                    "admin_label" => true,
                    'description' => __('z.B. https://www.youtube.com/embed/eBGIQ7ZuuiU')
                ),
                array(
                    'type'        => 'dropdown',
                    'heading'     => __('Datenschutzseite'),
                    'param_name'  => 'privacypage',
                    'admin_label' => true,
                    'value'       => twocv_vcPagesDropdown(),
                    'save_always' => true,
                    'std'         => $policyPageId, // Your default value
                    'description' => __('Datenschutzseite auswählen um den Link zu generieren')
                )
            )
        )
    );
}

function twocv_vcPagesDropdown()
{
    $pages = get_posts([
        'post_type' => 'page',
        'posts_per_page' => -1,
    ]);

    $formattedPages = [];

    foreach ($pages as $page) {
        $formattedPages[esc_attr($page->post_title)] = esc_attr($page->ID);
    }
    return $formattedPages;
}
