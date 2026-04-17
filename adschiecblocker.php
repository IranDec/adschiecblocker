<?php
/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Mohammad Babaei <adschi.com>
 *  @copyright 2023 Mohammad Babaei
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdschiEcBlocker extends Module
{
    public function __construct()
    {
        $this->name = 'adschiecblocker';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Mohammad Babaei (adschi.com)';
        $this->author_uri = 'https://adschi.com';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Adschi External Connections Blocker');
        $this->description = $this->l('Blocks external HTTP requests and Google Fonts to improve website speed for servers in restricted networks (like Iran).');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $default_whitelist = "google-analytics.com\nanalytics.google.com\ngoogletagmanager.com\ngoogle.com\ngstatic.com\ngoogleapis.com\nsearch.google.com\ngoogle.ir";

        if (!parent::install() ||
            !$this->registerHook('actionDispatcher') ||
            !Configuration::updateValue('ADSCHI_BLOCK_EXTERNAL', 1) ||
            !Configuration::updateValue('ADSCHI_BLOCK_GOOGLE_FONTS', 1) ||
            !Configuration::updateValue('ADSCHI_BLOCK_GOOGLE_ANALYTICS', 0) ||
            !Configuration::updateValue('ADSCHI_BLOCK_TAG_MANAGER', 0) ||
            !Configuration::updateValue('ADSCHI_BLOCK_FONT_AWESOME', 0) ||
            !Configuration::updateValue('ADSCHI_LOCAL_FONT_AWESOME', 0) ||
            !Configuration::updateValue('ADSCHI_LOCAL_FONT_AWESOME_URL', '') ||
            !Configuration::updateValue('ADSCHI_BLOCK_PRESTASHOP_API', 1) ||
            !Configuration::updateValue('ADSCHI_BLOCK_ALL_EXTERNAL', 0) ||
            !Configuration::updateValue('ADSCHI_BLOCK_UPDATES', 1) ||
            !Configuration::updateValue('ADSCHI_BLOCK_THEME_LICENSE', 1) ||
            !Configuration::updateValue('ADSCHI_CUSTOM_WHITELIST', $default_whitelist) ||
            !Configuration::updateValue('ADSCHI_CUSTOM_BLACKLIST', '')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('ADSCHI_BLOCK_EXTERNAL') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_GOOGLE_FONTS') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_GOOGLE_ANALYTICS') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_TAG_MANAGER') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_FONT_AWESOME') ||
            !Configuration::deleteByName('ADSCHI_LOCAL_FONT_AWESOME') ||
            !Configuration::deleteByName('ADSCHI_LOCAL_FONT_AWESOME_URL') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_PRESTASHOP_API') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_ALL_EXTERNAL') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_UPDATES') ||
            !Configuration::deleteByName('ADSCHI_BLOCK_THEME_LICENSE') ||
            !Configuration::deleteByName('ADSCHI_CUSTOM_WHITELIST') ||
            !Configuration::deleteByName('ADSCHI_CUSTOM_BLACKLIST')
        ) {
            return false;
        }

        return true;
    }

    public function hookActionDispatcher($params)
    {
        $blockFonts = Configuration::get('ADSCHI_BLOCK_GOOGLE_FONTS');
        $blockGA = Configuration::get('ADSCHI_BLOCK_GOOGLE_ANALYTICS');
        $blockGTM = Configuration::get('ADSCHI_BLOCK_TAG_MANAGER');
        $blockFA = Configuration::get('ADSCHI_BLOCK_FONT_AWESOME');
        $localFA = Configuration::get('ADSCHI_LOCAL_FONT_AWESOME');

        if ($blockFonts || $blockGA || $blockGTM || $blockFA || $localFA) {
            ob_start(array($this, 'filterOutput'));
        }
    }

    public function filterOutput($html)
    {
        $blockFonts = Configuration::get('ADSCHI_BLOCK_GOOGLE_FONTS');
        $blockGA = Configuration::get('ADSCHI_BLOCK_GOOGLE_ANALYTICS');
        $blockGTM = Configuration::get('ADSCHI_BLOCK_TAG_MANAGER');
        $blockFA = Configuration::get('ADSCHI_BLOCK_FONT_AWESOME');
        $localFA = Configuration::get('ADSCHI_LOCAL_FONT_AWESOME');
        $localFAUrl = Configuration::get('ADSCHI_LOCAL_FONT_AWESOME_URL');

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

        return $html;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitAdschiEcBlocker')) {
            Configuration::updateValue('ADSCHI_BLOCK_EXTERNAL', (int)Tools::getValue('ADSCHI_BLOCK_EXTERNAL'));
            Configuration::updateValue('ADSCHI_BLOCK_GOOGLE_FONTS', (int)Tools::getValue('ADSCHI_BLOCK_GOOGLE_FONTS'));
            Configuration::updateValue('ADSCHI_BLOCK_GOOGLE_ANALYTICS', (int)Tools::getValue('ADSCHI_BLOCK_GOOGLE_ANALYTICS'));
            Configuration::updateValue('ADSCHI_BLOCK_TAG_MANAGER', (int)Tools::getValue('ADSCHI_BLOCK_TAG_MANAGER'));
            Configuration::updateValue('ADSCHI_BLOCK_FONT_AWESOME', (int)Tools::getValue('ADSCHI_BLOCK_FONT_AWESOME'));
            Configuration::updateValue('ADSCHI_LOCAL_FONT_AWESOME', (int)Tools::getValue('ADSCHI_LOCAL_FONT_AWESOME'));
            Configuration::updateValue('ADSCHI_LOCAL_FONT_AWESOME_URL', Tools::getValue('ADSCHI_LOCAL_FONT_AWESOME_URL'));
            Configuration::updateValue('ADSCHI_BLOCK_PRESTASHOP_API', (int)Tools::getValue('ADSCHI_BLOCK_PRESTASHOP_API'));
            Configuration::updateValue('ADSCHI_BLOCK_ALL_EXTERNAL', (int)Tools::getValue('ADSCHI_BLOCK_ALL_EXTERNAL'));
            Configuration::updateValue('ADSCHI_BLOCK_UPDATES', (int)Tools::getValue('ADSCHI_BLOCK_UPDATES'));
            Configuration::updateValue('ADSCHI_BLOCK_THEME_LICENSE', (int)Tools::getValue('ADSCHI_BLOCK_THEME_LICENSE'));
            Configuration::updateValue('ADSCHI_CUSTOM_WHITELIST', Tools::getValue('ADSCHI_CUSTOM_WHITELIST'));
            Configuration::updateValue('ADSCHI_CUSTOM_BLACKLIST', Tools::getValue('ADSCHI_CUSTOM_BLACKLIST'));

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output . $this->renderForm() . $this->renderFooter();
    }

    protected function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Block External Requests'),
                        'name' => 'ADSCHI_BLOCK_EXTERNAL',
                        'is_bool' => true,
                        'desc' => $this->l('Blocks functions like file_get_contents for external domains.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Block PrestaShop API / Addons',
                        'name' => 'ADSCHI_BLOCK_PRESTASHOP_API',
                        'is_bool' => true,
                        'desc' => 'Blocks connections to PrestaShop main servers and Addons to prevent slowdowns.',
                        'values' => array(
                            array(
                                'id' => 'ps_api_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'ps_api_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Block All External Connections',
                        'name' => 'ADSCHI_BLOCK_ALL_EXTERNAL',
                        'is_bool' => true,
                        'desc' => 'Strictly blocks all external connections ignoring whitelists (excluding local server).',
                        'values' => array(
                            array(
                                'id' => 'all_ext_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'all_ext_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Block Module/Theme Updates Check',
                        'name' => 'ADSCHI_BLOCK_UPDATES',
                        'is_bool' => true,
                        'desc' => 'Stops the system from checking for module and theme updates externally.',
                        'values' => array(
                            array(
                                'id' => 'updates_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'updates_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Block Theme/Module License Checks',
                        'name' => 'ADSCHI_BLOCK_THEME_LICENSE',
                        'is_bool' => true,
                        'desc' => 'Attempts to block external license verification requests for themes and modules.',
                        'values' => array(
                            array(
                                'id' => 'license_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'license_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Block Google Fonts'),
                        'name' => 'ADSCHI_BLOCK_GOOGLE_FONTS',
                        'is_bool' => true,
                        'desc' => $this->l('Removes Google Fonts (fonts.googleapis.com, fonts.gstatic.com) from front-end and back-end to increase speed.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Block Google Analytics',
                        'name' => 'ADSCHI_BLOCK_GOOGLE_ANALYTICS',
                        'is_bool' => true,
                        'desc' => 'Blocks Google Analytics scripts from loading to improve speed.',
                        'values' => array(
                            array(
                                'id' => 'ga_active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'ga_active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Block Google Tag Manager',
                        'name' => 'ADSCHI_BLOCK_TAG_MANAGER',
                        'is_bool' => true,
                        'desc' => 'Blocks Google Tag Manager scripts and iframes to improve speed.',
                        'values' => array(
                            array(
                                'id' => 'gtm_active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'gtm_active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Block Font Awesome',
                        'name' => 'ADSCHI_BLOCK_FONT_AWESOME',
                        'is_bool' => true,
                        'desc' => 'Removes external Font Awesome resources to improve speed.',
                        'values' => array(
                            array(
                                'id' => 'fa_active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'fa_active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Use Local Font Awesome',
                        'name' => 'ADSCHI_LOCAL_FONT_AWESOME',
                        'is_bool' => true,
                        'desc' => 'Replace external Font Awesome with a local one. Requires URL below.',
                        'values' => array(
                            array(
                                'id' => 'fa_local_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'fa_local_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Local Font Awesome URL',
                        'name' => 'ADSCHI_LOCAL_FONT_AWESOME_URL',
                        'desc' => 'Enter the absolute URL to your local font-awesome.min.css (e.g., https://YOUR-Domain.com/assets/css/font-awesome.min.css).',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom Whitelist'),
                        'name' => 'ADSCHI_CUSTOM_WHITELIST',
                        'desc' => $this->l('Domains to allow. Enter one domain per line. (e.g. api.mybank.com)'),
                        'cols' => 40,
                        'rows' => 10,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom Blacklist'),
                        'name' => 'ADSCHI_CUSTOM_BLACKLIST',
                        'desc' => $this->l('Domains to force block. Enter one domain per line.'),
                        'cols' => 40,
                        'rows' => 10,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAdschiEcBlocker';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFormValues()
    {
        return array(
            'ADSCHI_BLOCK_EXTERNAL' => Configuration::get('ADSCHI_BLOCK_EXTERNAL'),
            'ADSCHI_BLOCK_PRESTASHOP_API' => Configuration::get('ADSCHI_BLOCK_PRESTASHOP_API'),
            'ADSCHI_BLOCK_ALL_EXTERNAL' => Configuration::get('ADSCHI_BLOCK_ALL_EXTERNAL'),
            'ADSCHI_BLOCK_UPDATES' => Configuration::get('ADSCHI_BLOCK_UPDATES'),
            'ADSCHI_BLOCK_THEME_LICENSE' => Configuration::get('ADSCHI_BLOCK_THEME_LICENSE'),
            'ADSCHI_BLOCK_GOOGLE_FONTS' => Configuration::get('ADSCHI_BLOCK_GOOGLE_FONTS'),
            'ADSCHI_BLOCK_GOOGLE_ANALYTICS' => Configuration::get('ADSCHI_BLOCK_GOOGLE_ANALYTICS'),
            'ADSCHI_BLOCK_TAG_MANAGER' => Configuration::get('ADSCHI_BLOCK_TAG_MANAGER'),
            'ADSCHI_BLOCK_FONT_AWESOME' => Configuration::get('ADSCHI_BLOCK_FONT_AWESOME'),
            'ADSCHI_LOCAL_FONT_AWESOME' => Configuration::get('ADSCHI_LOCAL_FONT_AWESOME'),
            'ADSCHI_LOCAL_FONT_AWESOME_URL' => Configuration::get('ADSCHI_LOCAL_FONT_AWESOME_URL'),
            'ADSCHI_CUSTOM_WHITELIST' => Configuration::get('ADSCHI_CUSTOM_WHITELIST'),
            'ADSCHI_CUSTOM_BLACKLIST' => Configuration::get('ADSCHI_CUSTOM_BLACKLIST'),
        );
    }

    protected function renderFooter()
    {
        $version = $this->version;
        $html = '
        <div class="panel">
            <div class="panel-footer" style="text-align: center; font-size: 14px; padding: 15px;">
                <p>نسخه ماژول: <strong>' . $version . '</strong></p>
                <p>نویسنده: <strong>Mohammad Babaei</strong></p>
                <p>برای مشاوره و ساخت ماژول اختصاصی درخواست بدهید: <a href="https://adschi.com" target="_blank" style="text-decoration: underline; color: #25b9d7;">adschi.com</a></p>
            </div>
        </div>';

        return $html;
    }
}
