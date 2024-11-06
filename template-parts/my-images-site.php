<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');
get_header();
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
            <h1 class="entry-title"> My Images</h1>

<?php
//check if the user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}
//check if order exists, redirect, to my orders if not
if(!isset($_GET['order_id'])){
    wp_redirect(site_url().'/account/my-orders');
    exit;
}
//Loop through the folder belongs to the user. Check if there are files.
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$dir=UPLOADDIR.$user_id;
$url=UPLOADURL.$user_id.'/';
$files=scandir($dir);
$files = array_diff($files, array('.', '..'));

$imageHtml = "<div>";
        foreach ($files as $key => $value) {
            if ($value !== null && $value !== "") {
                if(strpos($value, $_GET['order_id'])!==false)
                {
                    $imagesUrls[] = $value;
                }
            }
        }
        if (!empty($imagesUrls))
        {
             //Show all the purchased images.
            foreach ($imagesUrls as $image) {
            $imageHtml .= "<div class='my-images-holder' style='display:inline-block; margin-right:25px;'><a href='".$url.$image."' target='_blank'><img class='my-images' src='" . $url.$image . "'></a>
            <p style='text-align:center; width:100%'><a href='".$url.$image."' download><button>Download</button></a></p></div>";
        }
        //add back button
        $imageHtml .= "</div><div style='text-align:center;'><a href='".site_url()."/account/my-orders'><button>Back To My Orders</button></a></div>";
        
        echo $imageHtml;
    }
        else {
            echo "<p class='my-images'>There are no images for this order!</p>";
        }
    

?>
</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
</main><!-- #main -->
</div><!-- #primary -->

<?php

get_footer();
?>