<?php
/**
 * Override Tools to block external requests.
 */

class Tools extends ToolsCore
{
    /**
     * Override file_get_contents to block external calls if configured.
     */
    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 5, $fallback = false)
    {
        $block_all = Configuration::get('ADSCHI_BLOCK_ALL_EXTERNAL');
        $block_ps_api = Configuration::get('ADSCHI_BLOCK_PRESTASHOP_API');
        $block_updates = Configuration::get('ADSCHI_BLOCK_UPDATES');
        $block_license = Configuration::get('ADSCHI_BLOCK_THEME_LICENSE');

        if (Configuration::get('ADSCHI_BLOCK_EXTERNAL') || $block_all || $block_ps_api || $block_updates || $block_license) {
            // Check if it's a URL
            if (preg_match('/^https?:\/\//i', $url)) {
                $host = parse_url($url, PHP_URL_HOST);
                if ($host) {
                    // Block PrestaShop API
                    if ($block_ps_api && (stripos($host, 'prestashop.com') !== false || stripos($host, 'api.prestashop.com') !== false || stripos($host, 'addons.prestashop.com') !== false)) {
                        return false;
                    }

                    // Block Module/Theme Updates Check
                    if ($block_updates && (stripos($url, 'api.prestashop.com/xml/upgrades.xml') !== false || stripos($url, 'update') !== false || stripos($host, 'api.prestashop.com') !== false)) {
                        return false;
                    }

                    // Block Theme/Module License Checks
                    if ($block_license && (stripos($url, 'license') !== false || stripos($url, 'verify') !== false || stripos($url, 'auth') !== false || stripos($url, 'validate') !== false)) {
                        return false;
                    }

                    if ($block_all) {
                        // Strict Block All
                        $allowed_strict = array(
                            Tools::getHttpHost(false, false),
                            'localhost',
                            '127.0.0.1'
                        );
                        $is_allowed = false;
                        foreach ($allowed_strict as $allowed_host) {
                            if (!empty($allowed_host) && stripos($host, $allowed_host) !== false) {
                                $is_allowed = true;
                                break;
                            }
                        }
                        if (!$is_allowed) {
                            return false;
                        }
                    } elseif (Configuration::get('ADSCHI_BLOCK_EXTERNAL')) {
                        // Regular Block External with whitelist/blacklist
                        $whitelist_raw = Configuration::get('ADSCHI_CUSTOM_WHITELIST');
                        $whitelist = array_filter(array_map('trim', explode("\n", $whitelist_raw)));

                        // Always allow localhost/own domain
                        $whitelist[] = Tools::getHttpHost(false, false);
                        $whitelist[] = 'localhost';
                        $whitelist[] = '127.0.0.1';

                        // Always allow SEO and Google tools (Analytics, Tag Manager, etc.)
                        $seo_whitelist = array(
                            'google-analytics.com',
                            'analytics.google.com',
                            'googletagmanager.com',
                            'google.com',
                            'gstatic.com',
                            'googleapis.com',
                            'search.google.com',
                            'google.ir'
                        );
                        $whitelist = array_merge($whitelist, $seo_whitelist);

                        $is_whitelisted = false;
                        foreach ($whitelist as $allowed_host) {
                            if (!empty($allowed_host) && stripos($host, $allowed_host) !== false) {
                                $is_whitelisted = true;
                                break;
                            }
                        }

                        // Check Blacklist
                        $blacklist_raw = Configuration::get('ADSCHI_CUSTOM_BLACKLIST');
                        $blacklist = array_filter(array_map('trim', explode("\n", $blacklist_raw)));

                        $is_blacklisted = false;
                        foreach ($blacklist as $blocked_host) {
                            if (!empty($blocked_host) && stripos($host, $blocked_host) !== false) {
                                $is_blacklisted = true;
                                break;
                            }
                        }

                        // Blacklist overrides whitelist. If blocked, return empty string or false.
                        if ($is_blacklisted || !$is_whitelisted) {
                            return false;
                        }
                    }
                }
            }
        }

        return parent::file_get_contents($url, $use_include_path, $stream_context, $curl_timeout, $fallback);
    }

    /**
     * Override addonsRequest to prevent calling PrestaShop Addons API
     * which causes significant slowdowns when servers can't reach it.
     */
    public static function addonsRequest($request, $params = array())
    {
        if (Configuration::get('ADSCHI_BLOCK_EXTERNAL') || Configuration::get('ADSCHI_BLOCK_PRESTASHOP_API') || Configuration::get('ADSCHI_BLOCK_ALL_EXTERNAL')) {
            return false;
        }

        return parent::addonsRequest($request, $params);
    }
}
