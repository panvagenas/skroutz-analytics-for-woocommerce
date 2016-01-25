<?php

namespace Pan\SkroutzAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @property mixed api_key
 * @property mixed debug
 */
class SkroutzAnalytics extends \WC_Integration {
    const SLUG_ANALYTICS_JS = 'skz-analytics';

    const JS_OBJ_NAME = 'skzAnalytics';

    protected $pluginFile;
    protected $accountId = '';

    protected $order = array();
    protected $items = array();

    public function __construct( $pluginFile = '' ) {
        $this->id                 = 'skz-analytics';
        $this->method_title       = __( 'Skroutz Analytics', Initializer::TEXT_DOMAIN );
        $this->method_description = __( 'Skroutz Analytics for WooCommerce', Initializer::TEXT_DOMAIN );

        $this->init_form_fields();
        $this->init_settings();
        // Define user set variables.
        $this->api_key = $this->get_option( 'api_key' );
        $this->debug   = $this->get_option( 'debug' );
        // Actions.
        add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
        add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );

        $this->pluginFile = $pluginFile;
        $this->accountId  = $this->get_option( 'skz_ana_ac_id' );
    }

    public function init_form_fields() {
        $skzUrl         = 'https://merchants.skroutz.gr/merchants/account/settings/analytics';
        $skzAccountLink = '<a href="' . $skzUrl . '" target="_blank">skroutz.gr</a>';

        $this->form_fields = array(
            'skz_ana_ac_id' => array(
                'title'       => __( 'Account ID', Initializer::TEXT_DOMAIN ),
                'type'        => 'text',
                'description' => __( sprintf( 'You can get this from %s', $skzAccountLink ), Initializer::TEXT_DOMAIN ),
                'desc_tip'    => __( 'This will add a title to your slider', Initializer::TEXT_DOMAIN ),
                'default'     => '',
            ),
        );
    }

    public function sanitize_settings( $settings ) {
        // We're just going to make the api key all upper case characters since that's how our imaginary API works
        if ( isset( $settings )
             && isset( $settings['skz_ana_ac_id'] )
        ) {
            $settings['skz_ana_ac_id'] = strip_tags( trim( $settings['skz_ana_ac_id'] ) );
        }

        return $settings;
    }

    public function validate_skz_ana_ac_id_field( $key ) {
        // get the posted value
        $value = strip_tags( trim( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) );

        if ( isset( $value )
             && 12 < strlen( $value )
        ) {
            $this->errors[] = $key;
        }

        return $value;
    }


    public function display_errors() {

        // loop through each error and display it
        foreach ( $this->errors as $key => $value ) {
            ?>
            <div class="error">
                <p><?php _e(
                        'Looks like you made a mistake with the '
                        . $value
                        . ' field. Make sure it is exactly 12 characters', Initializer::TEXT_DOMAIN
                    ); ?></p>
            </div>
            <?php
        }
    }

    public function actionWcThankYou( $orderId ) {
        if ( ! $this->accountId ) {
            return;
        }
        /** @var \WC_Order $order */
        $order = WC()->order_factory->get_order( $orderId );
        if ( ! $order || is_wp_error( $order ) ) {
            return;
        }

        $this->formOrderArray( $order );
        $this->formItemsArray( $order );
        if ( empty( $this->order ) || empty( $this->items ) ) {
            return;
        }

        wp_enqueue_script( self::SLUG_ANALYTICS_JS, plugins_url( 'assets/js/analytics.js', $this->pluginFile ) );
        wp_localize_script( self::SLUG_ANALYTICS_JS, self::JS_OBJ_NAME, array(
            'accountId' => $this->accountId,
        ) );
        add_action( 'wp_print_footer_scripts', array( $this, 'printScript' ) );
    }

    public function printScript() {
        ?>
        <script type="text/javascript">
            <?php
            echo $this->getOrderJs();
            echo $this->getItemsJs();
            ?>
        </script>
        <?php
    }

    protected function formOrderArray( \WC_Order $order ) {
        $this->order = array(
            'order_id' => $order->id,
            'revenue'  => $order->order_total,
            'shipping' => $order->get_total_shipping(),
            'tax'      => $order->get_total_tax(),
        );

        return $this->order;
    }

    protected function formItemsArray( \WC_Order $order ) {
        $items       = $order->get_items();
        $this->items = array();
        foreach ( $items as $item ) {
            $product = $order->get_product_from_item( $item );

            $this->items[] = array(
                'order_id'   => $order->id,
                'product_id' => $product->get_sku() ?: $item['item_meta_array']['_product_id'],
                'name'       => $product->get_title(),
                'price'      => $order->get_item_total( $item ),
                'quantity'   => ( ! empty( $item['qty'] ) ) ? $item['qty'] : 1,
            );
        }

        return $this->items;
    }

    protected function getOrderJs() {
        $orderData = json_encode( $this->order );

        return "sa('ecommerce', 'addOrder', '{$orderData}');";
    }

    protected function getItemsJs() {
        $out = '';
        foreach ( $this->items as $item ) {
            $itemData = json_encode( $item );
            $out .= "sa('ecommerce', 'addItem', '{$itemData}');";
        }

        return $out;
    }
}