<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');
class PaymentComplete
{
    private static $instance=false;
    public function __construct()
    {
        add_action('woocommerce_order_status_completed', [$this, 'order_status_is_completed'], 10, 1);
    }
//If payment is complete call the function which send the datas to the image swap API
function order_status_is_completed($order_id) {
    $order = wc_get_order($order_id);
    $current_user_id = get_current_user_id();
    $items = $order->get_items();
    foreach ($items as $item_id =>$item){
        $product_id = $item->get_product_id();
        $uploaded_image_url = UPLOADURL.'temp/'.$item->get_meta('image_file_name');
        $original_image_id=get_post_thumbnail_id($product_id);
        $product_image_url =wp_get_attachment_url($original_image_id);
        
        $this->send_image_url_to_api($uploaded_image_url, $product_image_url, IMAGESWAPURL, $order_id, $current_user_id);
    }
}
//Send the datas to the image swap API and upload the ready image gotten back from the API with the ReadyImageUpload class.
public function send_image_url_to_api($uploaded_image, $product_image, $url, $order_id, $current_user_id){
    $data = [
        'uploaded_image_url' => $uploaded_image,
        'product_image_url' => $product_image
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $response = curl_exec($ch);
    $result = json_decode($response, true);
    if ($response===false){
        error_log('cURL error: '.curl_error($ch));
    }
    $ready_image=$result['readyimage'];
    //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $upload = new ReadyImageUpload();
    $upload -> ready_image_upload($current_user_id, $order_id,$ready_image, $uploaded_image);
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