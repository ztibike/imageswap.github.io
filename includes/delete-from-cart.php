<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');

class DeleteFromCart {
    public function __construct() {
        add_action( 'woocommerce_remove_cart_item', [$this, 'delete_image'], 20, 2 );
    }

    public function delete_image($cart_item_key) {
        global $woocommerce;
        $cart_item = $woocommerce->cart->get_cart_item($cart_item_key);
        if ($cart_item){
            $image_file_name=$cart_item['image_file_name'];
            
        if($image_file_name){
            $file_path=UPLOADDIR.$image_file_name;
            if (file_exists($file_path)){
                unlink($file_path);
            } else{
                error_log('Hiba történt a törlés közben');
            }
        }
    }
}
}


?>