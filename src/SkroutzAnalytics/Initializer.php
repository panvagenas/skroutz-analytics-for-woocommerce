<?php

namespace Pan\SkroutzAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Initializer {
    const TEXT_DOMAIN = 'woocommerce-skroutz-analytics';
    protected $pluginFile;

    public function __construct( $pluginFile ) {
        $this->pluginFile = $pluginFile;

        load_plugin_textdomain(self::TEXT_DOMAIN);

        add_action(
            'woocommerce_thankyou',
            array( $this, 'actionWcThankYou' )
        );
        add_action( 'plugins_loaded', array( $this, 'run' ) );

    }

    public function run() {
        add_filter( 'woocommerce_integrations', array( $this, 'addIntegration' ) );
    }

    public function addIntegration( $integrations ) {
        $integrations[] = 'Pan\SkroutzAnalytics\SkroutzAnalytics';
        return $integrations;
    }

    public function actionWcThankYou( $orderId ) {
        $skz = new SkroutzAnalytics($this->pluginFile);

        $skz->actionWcThankYou($orderId);
    }
}