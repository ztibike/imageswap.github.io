<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');


class ReadyImageUpload{

    
    public function __construct()
    {
     
    }
//this function makes the upload of the ready, modified image.
//Upload folder is like /imageswap_uploads/{user_id}/{order_id}/{order_id}{C}.file_extension
function ready_image_upload($user_id, $order_id, $image_url, $uploaded_image){
    $allowed_file_formats = array('jpg', 'jpeg', 'png', 'heif', 'heic');
    $image_name=basename($image_url);
    $image_ext = explode('.', $image_name)[1];
    if (!in_array($image_ext, $allowed_file_formats)){
        return 'Wrong extension.';
    }
    $image = file_get_contents($image_url);
    if (empty($image)){
        return 'There was a problem during the download process.';
        
    }
    $new_image_name=$order_id.'_0.'.$image_ext;
    if (!file_exists(UPLOADDIR.$user_id.'/')) {
        wp_mkdir_p(UPLOADDIR.$user_id.'/');
    }
    while (file_exists(UPLOADDIR.$user_id.'/'.$new_image_name)) {
        $new_file_name = explode('_', pathinfo($new_image_name, PATHINFO_FILENAME));
        $counter = (int)$new_file_name[1] + 1;
        $new_image_name = $order_id.'_'.$counter.'.'.$image_ext;
    }
    $filepath=UPLOADDIR.$user_id.'/'.$new_image_name;
    file_put_contents($filepath, $image);
    if (file_exists($filepath)) {
        $delete_helper=new DeleteProcess;
        $delete_helper->delete_uploaded_image($uploaded_image);
    } else {
        return "Error: Failed to save image.";
    }
}



}



?>