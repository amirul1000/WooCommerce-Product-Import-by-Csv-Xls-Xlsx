<?php
/*
Plugin Name: woocommerce-product-upload
Description: woocommerce-product-upload
Version:     1.6
Author:  Amirul   
Domain Path: wse
*/


 //Admin		
	add_action('admin_menu', 'upload_product');
	function upload_product(){
	  add_menu_page('WooCommerce Upload Product', 'WooCommerce Upload Product', 'manage_options', 'upload_product', 'product_import_page_func');
	}
	
	
	function product_import_page_func(){
	   include_once dirname(__FILE__) . '/import_view.php';   	
	}
?>