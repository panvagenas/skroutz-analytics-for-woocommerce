<?php

namespace Pan\SkroutzAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Options extends \Pan\MenuPages\Options {
    const OPTIONS_NAME = 'skz_analytics_options';

    protected static $defaultOptions = [
        'account_id' => '',
    ];

    public function getAccountId() {
        return $this->get( 'account_id' );
    }

    /**
     * @param string $optionsBaseName
     * @param array  $defaults
     *
     * @return $this
     * @throws \ErrorException
     */
    public static function getInstance( $optionsBaseName = '', array $defaults = [ ] ) {
        return parent::getInstance( self::OPTIONS_NAME, self::$defaultOptions );
    }
}