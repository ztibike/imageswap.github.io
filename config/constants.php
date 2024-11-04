<?php
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/includes/delete-process.php');
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/includes/add-product-meta.php');
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/includes/payment-completed.php');
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/includes/thank-you-class.php');
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/includes/ready-image-upload.php');
require_once (ABSPATH.'wp-content/plugins/imageswap-plugin/template-parts/my-images.php');
if(!defined('UPLOADDIR')){define('UPLOADDIR', wp_upload_dir()['basedir'].'/imageswap_uploads/');}
if(!defined('UPLOADURL')){define('UPLOADURL', site_url().'/wp-content/uploads/imageswap_uploads/');}
if(!defined('IMAGESWAPURL')){define('IMAGESWAPURL', 'https://demo.z-connect.hu/zapit/');}





?>