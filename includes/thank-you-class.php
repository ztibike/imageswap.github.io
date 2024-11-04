<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');

class ImageSwapThankYou{

    private static $instance=false;
    public function __construct()
    {
        add_action('woocommerce_thankyou', [$this, 'make_button_on_thank_you_page'], 10, 1);
    }
//make a button on thank you page whci redirects to the my-orders site.
function make_button_on_thank_you_page($order_id){
    if (!$order_id){
        return;
    }
    echo "<a href='".site_url()."/account/my-orders/'><button class='imageswap-thank-you' style='margin-bottom:25px;'>Check here if you ImageSwap is ready!</button></a>";

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