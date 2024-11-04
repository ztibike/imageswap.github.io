<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/config/constants.php');
get_header();
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title">My Orders</h1>
            </header><!-- .entry-header -->
            <div class="entry-content">
<?php
//check if the user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}
//prepare to make the order object
$user_id = $customer_id = get_current_user_id();
$args = array(
    'customer_id' => $customer_id, 
    'limit'       => -1,           
    'orderby'     => 'date',       
    'order'       => 'DESC',       
);
//Check if there are file to that order
function check_files($order_id, $user_id){
    $dir=UPLOADDIR.$user_id.'/';
    $files=scandir($dir);
    $files = array_diff($files, array('.', '..'));
    foreach($files as $key => $value){
        if ($value !==null && $value !==''){
            if (strpos($value,$order_id)!==false){return true;}
        }
    }
    return false;
}

$original_orders = wc_get_orders($args);
$orders=array();
//Remove the orders with the status='checkout-draft'
for ($i=0; $i<count($original_orders); $i++){
    if ($original_orders[$i]->get_status()!=='checkout-draft'){
        $orders[]=$original_orders[$i];
    }
}
//Prepare the pagination
$total_items=count($orders);
$order_per_page=10;
$total_pages=ceil($total_items/$order_per_page);
if (isset($_GET['page'])){
    if ($_GET['page']>$total_pages || !is_numeric((int)$_GET['page'])) wp_redirect(site_url().'/account/my-orders?order_id=1');
    $current_page=$_GET['page'];
}
else {
    $current_page=1;
}
$min_position=($order_per_page*($current_page-1));
$max_position=($total_items>$current_page*$order_per_page)? $current_page*$order_per_page: $total_items;
$pagination=array(
    'first_page' => $current_page-2>1 ? 1:null,
    'last_page' => $current_page+2<$total_pages? 1:null
);
echo "<br>";
echo "<div class='my-orders-div'><table class='my-orders-table'><tr><th>Order ID</th><th>Order Status</th><th>Order Date</th><th>View Order</th></tr>";
//List the orders with the pagination details
for ( $i=$min_position; $i<$max_position; $i++) {
    $order_id=$orders[$i]->get_id();
    $image_link='Pending';
    if (check_files($order_id, $user_id)){ 
        $image_link='<a href="'.site_url().'/account/my-orders/my-images?order_id='.$order_id.'"><b>Ready to download</b></a>';
    }
    $status = ($orders[$i]->get_status()!=='completed')? 'Awaiting payment' : 'Completed';
    echo "<tr><td>";
    echo  $order_id . '</td>';
    echo '<td>' . $status. '</td>';
    echo '<td>' . $orders[$i]->get_date_created()->date('Y-m-d H:i:s') . '</td>';
    echo '<td>' . $image_link . '</td></tr>';
}

echo "</table><div style='text-align:center; letter-spacing=8px; margin-bottom:20px; font-size:18px;'>";
if($total_pages>1){
    if ($pagination['first_page']!==null){
        echo "<a href='".site_url()."/account/my-orders?page=1'>First</a>  ";
    }
    for($i=$current_page-2; ($i<=$total_pages) && ($i<=$current_page+2); $i++){
        if($i<1) continue;
        if($i==$current_page){
            $link="<b style='font-size:20px;'>$i</b> ";
        }
        else {
            $link=" <a href='".site_url()."/account/my-orders?page=$i'>$i</a> ";
        }
        echo $link;
    }
    if ($pagination['last_page']!==null){
        echo "  <a href='".site_url()."/account/my-orders?page=$total_pages'>Last</a>";
    }
}


?>
</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
</main><!-- #main -->
</div><!-- #primary -->

<?php

get_footer();
?>