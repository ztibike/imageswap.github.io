<?php

class MyImages {
//This class makes the permalinks for my-orders and my-images sites.
    function my_images_rewrite_rules() {
        add_rewrite_rule('^account/my-orders/?$', 'index.php?my_orders=1', 'top');
        add_rewrite_rule('^account/my-orders/my-images/?$', 'index.php?my_images=1', 'top');
    }

    function load_my_images($template){
        if (get_query_var('my_images') == 1) {
            return WP_PLUGIN_DIR . '/imageswap-plugin/template-parts/my-images-site.php';
        }
        elseif (get_query_var('my_orders') == 1){
            return WP_PLUGIN_DIR . '/imageswap-plugin/template-parts/my-orders.php';
        }
        return $template;
    }

    function add_query_vars($vars) {
        $vars[] = 'my_images';
        $vars[] = 'my_orders';
        return $vars;
    }

    function add_my_orders_menu($items, $args){
        if (is_user_logged_in())
        {
            if($args->theme_location == 'primary'){
            $items .='<li class="menu-item my-image-menu"><a href="'.site_url().'/account/my-orders">My orders</a></li>';
            }
        }
        return $items;
    }
   
}

$my_images = new MyImages();
add_action('init', [$my_images, 'my_images_rewrite_rules']);
add_filter('query_vars', [$my_images,'add_query_vars']);
add_filter('template_include', [$my_images,'load_my_images']);
add_filter('wp_nav_menu_items', [$my_images,'add_my_orders_menu'], 10, 2);

?>