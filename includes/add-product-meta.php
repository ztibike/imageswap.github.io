<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');

class AddProductMeta{
    private static $instance=false;

public function __construct(){
    add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_product_meta'], 10, 4);
}
//Add product meta image_file_name on cart page
public function add_product_meta($item, $cart_item_key, $values, $order){
    if (isset($values['image_file_name'])) {
        $item->add_meta_data('image_file_name', $values['image_file_name'], true);
    }
}



public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
?>