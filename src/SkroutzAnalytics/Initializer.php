<?php

namespace Pan\SkroutzAnalytics;

use Pan\MenuPages\Fields\Text;
use Pan\MenuPages\PageElements\Components\CmpTabForm;
use Pan\MenuPages\PageElements\Containers\CnrTabbedSettings;
use Pan\MenuPages\Pages\SubPage;
use Pan\MenuPages\WpMenuPages;
use Respect\Validation\Validator;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Initializer {
    protected $pluginFile;
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    public function run(){
        add_action('woocommerce_thankyou', [ new SkroutzAnalytics($this->pluginFile), 'actionWcThankYou' ]);
        $this->optionsSetUp();
    }

    protected function optionsSetUp(){
        $wpMenuPages = new WpMenuPages($this->pluginFile, Options::getInstance());
        $menuPage = new SubPage($wpMenuPages,SubPage::PARENT_SETTINGS, 'Skroutz Analytics');

        $tabs = new CnrTabbedSettings($menuPage, SubPage::POSITION_MAIN);
        $optionsTab = new CmpTabForm($tabs, 'Account ID', true);
        $accountIdFld = new Text($optionsTab, 'account_id');
        $accountIdFld->setLabel('Account ID')->attachValidator(Validator::stringType()->notEmpty());
    }
}