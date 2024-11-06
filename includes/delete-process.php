<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');

class DeleteProcess {
public function __construct() {
        add_action( 'woocommerce_remove_cart_item', [$this, 'delete_image'], 20, 2 );
    }
//delete temorary image  when item is deleted from cart
public function delete_image($cart_item_key) {
        global $woocommerce;
        
        $cart_item = $woocommerce->cart->removed_cart_contents[ $cart_item_key ];
        if ($cart_item['image_file_name'])
        {
            if ($cart_item){
            $image_file_name=$cart_item['image_file_name'];
           
        if($image_file_name){
            $file_path=UPLOADDIR.'temp/'.$image_file_name;
            if (file_exists($file_path)){
                unlink($file_path);
            }
        }
    }
}

}
//delete uploaded temporary image if the image swap api send back the ready image
function delete_uploaded_image($uploaded_image_url){
    $uploaded_image_path_parts=explode('/', $uploaded_image_url);
    $uploaded_image = end($uploaded_image_path_parts);
    if (file_exists(UPLOADDIR.'temp/'.$uploaded_image)){
        unlink(UPLOADDIR.'temp/'.$uploaded_image);
    }

}
//auto delete temporary images older than 2 days    
function delete_older_images(){
    $dir=UPLOADDIR.'temp';
    $deadline=2;
    if(file_exists($dir)){
        $now=time();
        $files=scandir($dir);
        if(!empty($files))
        {
            foreach($files as $file){
            if ($file!=='.' && $file !=='..'){
                $path=$dir.'/'.$file;
                if (is_file($path)){
                    $file_mod_time=filemtime($path);
                    $file_age=($now-$file_mod_time)/(60*60*24);
                    if($file_age>=$deadline){
                        unlink($path);
                    }
                }
            }
        }
        }
    }
}

}


?>