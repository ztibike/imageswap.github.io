<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');
/*
Plugin Name: imageswap-plugin
Plugin URI: https://z-connect.hu
Description: File upload before add to cart button
Version:1.0.0
Text Domain: z-connect.hu
Domain Path: /languages/
Author: Z_Tibi
Author URI: https://z-connect.hu
WC requires at least: 2.6.0
WC tested up to: 9.0.0
*/

class ImageSwap
{
	private static $instance=false;
	private $rest_api_namespace = 'imageswap-plugin-main/v1';
	
	
private function __construct()
	{
		add_action( 'before_woocommerce_init', function() {
			if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		});
		//add the REST API endpoint for post images
        add_action('rest_api_init', function () {
            
            register_rest_route(
                $this->rest_api_namespace,
                '/upload-image',
                [
                    'methods' => 'POST',
                    'callback' => [$this, 'upload_image'],
                    'permission_callback' => '__return_true'
                ]
            );
			
        });
		
		add_action('wp_enqueue_scripts',[$this,'enque_scripts']);
        add_action('woocommerce_before_add_to_cart_form',[$this,'add_upload_field']);
		add_action('woocommerce_add_cart_item_data', [$this,'add_image_file_name_to_cart_item'], 10, 2);
		add_action('woocommerce_checkout_create_order_line_item', [$this,'add_product_meta'], 10, 4);
		add_action('woocommerce_order_status_completed', [$this, 'order_status_changed'], 10, 1);
		add_action('woocommerce_thankyou', [$this, 'make_button_on_thank_you_page'], 10, 1);
		add_filter('woocommerce_get_item_data', [$this,'display_image_file_name_in_cart'], 10, 2);
		add_action('woocommerce_remove_cart_item', [$this, 'remove_from_cart']);
		add_Action('wp', [$this,'auto_delete_old_images']);
	}
public function enque_scripts()
	{
			wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', null,null, true);
			wp_enqueue_script('imageswap-js', plugin_dir_url(__DIR__).'imageswap-plugin/assets/js/imageswap-upload.js', [],null, true );
			
	}
//Add image upload field to products	
public function add_upload_field()
	{
		
		?> 
		<p></p>
		<form id="file_upload_form" action="https://z-connect.hu/termek/uj-termek-1/" method="post" enctype="multipart/form-data">
            <input type="file" name="uploaded_file" id="uploaded_file" required>
        </form>
        <?php
}

//the REST api callback for image uploads
public function upload_image(){
	//check if the file is sent to the server
	if (isset($_FILES['uploaded_file']['name'])) {
		if ($_FILES['uploaded_file']['error'] !== 0) {
			return new WP_REST_Response([
				'error' => 'Fájl feltöltési hiba',
				'error_code' => $_FILES['uploaded_file']['error']
			], 400);
		}
		//Check the uploaded file extension
		$allowed_file_extensions = array('jpg', 'jpeg', 'png', 'heif', 'heic');
		$file_extension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
		$temp_file_name = 'image_0.'.$file_extension;
	
		if (in_array($file_extension, $allowed_file_extensions)) {
			//Make temporary folder for temporary files, if it does not exist
			if (!file_exists(UPLOADDIR.'temp/')) {
				wp_mkdir_p(UPLOADDIR.'temp/');
			}
			//If there is other file with the same temporary name, change it
			while (file_exists(UPLOADDIR.'temp/'.$temp_file_name)) {
				$new_file_name = explode('_', pathinfo($temp_file_name, PATHINFO_FILENAME));
				$counter = (int)$new_file_name[1] + 1;
				$temp_file_name = 'image_'.$counter.'.'.$file_extension;
			}
			//If the file was uploaded send the response
			if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], UPLOADDIR.'temp/'.$temp_file_name)) {
				return new WP_REST_Response([
					'status' => 'success',
					'fileName' => $temp_file_name
				], 200);
			} else {
				return new WP_REST_Response(['error' => 'Nem sikerült a fájlt feltölteni.'], 400);
			}
		} else {
			return new WP_REST_Response(['error' => 'Rossz formátum.'], 400);
		}
	} else {
		return new WP_REST_Response(['error' => 'Nincs fájl kijelölve'], 400);
	}
}
	
//make a button which links to my-orders
function make_button_on_thank_you_page($order_id){
	$thankyou_helper = ImageSwapThankYou::get_instance();
	$thankyou_helper->make_button_on_thank_you_page($order_id);
}
//Add product meta image_file_name on cart page
function add_image_file_name_to_cart_item($cart_item_data, $product_id) {
    if (isset($_POST['image_file_name'])) {
        $cart_item_data['image_file_name'] = $_POST['image_file_name'];
    }
	return $cart_item_data;
}
//delete temorary image  when item is deleted from cart
function remove_from_cart($cart_item_key)
{
	
		if($cart_item_key)
	{$delete_helper=new DeleteProcess;
    $delete_helper->delete_image($cart_item_key);
}

}
//auto delete temporary images older than 2 days
function auto_delete_old_images(){
	$delete_helper=new DeleteProcess;
	$delete_helper->delete_older_images();
}
//Add product meta image_file_name to order
function add_product_meta($item, $cart_item_key, $values, $order){
	$meta_helper = AddProductMeta::get_instance();
	$meta_helper->add_product_meta($item, $cart_item_key, $values, $order);
}
//Send datas to the image swap API if order is completed
function order_status_changed($order_id)
{
	$payment_helper = PaymentComplete::get_instance();
	$payment_helper->order_status_is_completed($order_id);

}
//Show the cart items meta image_file_name
function display_image_file_name_in_cart($item_data, $cart_item) {
    if (isset($cart_item['image_file_name'])) {
        $item_data[] = array(
            'key'   => __('Image File', 'text-domain'),
            'value' => '<a href="'.site_url().'/wp-content/uploads/imageswap_uploads/temp/'.wc_clean($cart_item['image_file_name']).'">'.wc_clean($cart_item['image_file_name']).'</a>'
        );
    }
    return $item_data;
}

public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
ImageSwap::get_instance();

?>