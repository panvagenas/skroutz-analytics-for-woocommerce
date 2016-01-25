<?php

namespace Pan\SkroutzAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SkroutzAnalytics {
    const SLUG_ANALYTICS_JS = 'skz-analytics';

    const JS_OBJ_NAME = 'skzAnalytics';

    protected $pluginFile;
    protected $accountId = '';

    protected $order = [ ];
    protected $items = [ ];

    public function __construct( $pluginFile ) {
        $this->pluginFile = $pluginFile;
        $this->accountId  = Options::getInstance()->getAccountId();
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
        wp_localize_script( self::SLUG_ANALYTICS_JS, self::JS_OBJ_NAME, [
            'accountId' => $this->accountId,
        ] );
        add_action( 'wp_print_footer_scripts', [ $this, 'printScript' ] );
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
        $this->order = [
            'order_id' => $order->id,
            'revenue'  => $order->order_total,
            'shipping' => $order->get_total_shipping(),
            'tax'      => $order->get_total_tax(),
        ];

        return $this->order;
    }

    protected function formItemsArray( \WC_Order $order ) {
        $items = $order->get_items();
        $this->items = [];
        foreach ( $items as $item ) {
            $product = $order->get_product_from_item($item);

            $this->items[] = [
                'order_id'  => $order->id,
                'product_id'  => $product->get_sku() ? : $item['item_meta_array']['_product_id'],
                'name'      => $product->get_title(),
                'price'     => $order->get_item_total($item),
                'quantity'  => ( ! empty( $item['qty'] ) ) ? $item['qty'] : 1
            ];
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