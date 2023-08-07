<?php
namespace Joomunited\WPSOL\Cdn;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Integration
 */
class Integration
{
    /**
     * Integration constructor.
     */
    public function __construct()
    {
        add_action('template_redirect', array($this, 'handleRewriteCdn'));
    }

    /**
     * Execute rewrite cdn
     *
     * @return void
     */
    public function handleRewriteCdn()
    {
        $cdn_integration = get_option('wpsol_cdn_integration');

        if (empty($cdn_integration)) {
            return;
        }

        if ($cdn_integration['cdn_url'] === '') {
            return;
        }

        if (get_option('home') === $cdn_integration['cdn_url']) {
            return;
        }

        $rewrite = new Rewrite($cdn_integration);

        //rewrite CDN Url to html raw
        add_filter('wpsol_cdn_content_return', array(&$rewrite, 'rewrite'));
    }
}
