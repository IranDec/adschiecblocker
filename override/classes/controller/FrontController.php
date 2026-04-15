<?php
/**
 * Override FrontController to remove Google Fonts
 */

class FrontController extends FrontControllerCore
{
    public function display()
    {
        $blockFonts = Configuration::get('ADSCHI_BLOCK_GOOGLE_FONTS');
        $blockGA = Configuration::get('ADSCHI_BLOCK_GOOGLE_ANALYTICS');
        $blockGTM = Configuration::get('ADSCHI_BLOCK_TAG_MANAGER');
        $blockFA = Configuration::get('ADSCHI_BLOCK_FONT_AWESOME');
        $localFA = Configuration::get('ADSCHI_LOCAL_FONT_AWESOME');
        $localFAUrl = Configuration::get('ADSCHI_LOCAL_FONT_AWESOME_URL');

        if ($blockFonts || $blockGA || $blockGTM || $blockFA || $localFA) {
            ob_start();
            parent::display();
            $html = ob_get_clean();

            if ($blockFonts) {
                // Remove google fonts links
                $html = preg_replace('/<link[^>]*href=["\'](https?:)?\/\/fonts\.(googleapis|gstatic)\.com[^>]*>/i', '', $html);
                $html = preg_replace('/<style[^>]*>.*?@import url\(["\']?(https?:)?\/\/fonts\.googleapis\.com.*?<\/style>/is', '', $html);
            }

            if ($blockGA) {
                // Remove Google Analytics scripts
                $html = preg_replace('/<script[^>]*src=["\'](https?:)?\/\/(www\.)?google-analytics\.com\/analytics\.js["\'][^>]*>.*?<\/script>/is', '', $html);
                $html = preg_replace('/<script[^>]*>.*?gtag\([\'"]config[\'"].*?<\/script>/is', '', $html);
                $html = preg_replace('/<script[^>]*>.*?(www\.)?google-analytics\.com.*?<\/script>/is', '', $html);
            }

            if ($blockGTM) {
                // Remove Google Tag Manager scripts and iframes
                $html = preg_replace('/<script[^>]*src=["\'](https?:)?\/\/(www\.)?googletagmanager\.com\/gtm\.js[^>]*>.*?<\/script>/is', '', $html);
                $html = preg_replace('/<script[^>]*>.*?(www\.)?googletagmanager\.com.*?<\/script>/is', '', $html);
                $html = preg_replace('/<noscript><iframe[^>]*src=["\'](https?:)?\/\/(www\.)?googletagmanager\.com\/ns\.html[^>]*>.*?<\/iframe><\/noscript>/is', '', $html);
            }

            if ($localFA && !empty($localFAUrl)) {
                // Replace external Font Awesome with local URL
                $html = preg_replace('/<link[^>]*href=["\'][^"\']*font-awesome(\.min)?\.css[^"\']*["\'][^>]*>/i', '<link rel="stylesheet" href="' . htmlspecialchars($localFAUrl, ENT_QUOTES, 'UTF-8') . '" />', $html);
                $html = preg_replace('/@import url\(["\']?[^"\']*font-awesome(\.min)?\.css[^"\']*["\']?\);/i', '@import url("' . htmlspecialchars($localFAUrl, ENT_QUOTES, 'UTF-8') . '");', $html);
            } elseif ($blockFA) {
                // Completely remove Font Awesome
                $html = preg_replace('/<link[^>]*href=["\'][^"\']*font-awesome(\.min)?\.css[^"\']*["\'][^>]*>/i', '', $html);
                $html = preg_replace('/<style[^>]*>.*?@import url\(["\']?[^"\']*font-awesome(\.min)?\.css[^"\']*["\']?\).*?<\/style>/is', '', $html);
            }

            echo $html;
        } else {
            parent::display();
        }
    }
}
