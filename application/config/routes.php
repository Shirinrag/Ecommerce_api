<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Frontend';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
// ========================== Common Controller============================================

// Frontend

$route['register_data']='Frontend/register';
$route['login-data']='Frontend/login';
$route['get-home-page-data']='Frontend/get_home_page_data';
$route['update-user-profile']='Frontend/update_user_profile';
$route['save-new-address']='Frontend/save_new_address';
$route['update-address']='Frontend/update_address';
$route['change-password']='Frontend/update_change_password';
$route['forget-password']='Frontend/forget_password';
$route['product-details-on-id']='Frontend/product_details_on_id';
$route['product-details-on-search']='Frontend/get_product_on_search';
$route['send-otp']='Frontend/send_otp';
$route['get-language']='Frontend/get_language';
$route['get-user-profile-data']='Frontend/get_user_profile_data';
$route['get-dynamic-menu'] = 'Frontend/get_dynamic_menu';
$route['verify-otp']='Frontend/verify_otp';
$route['resend-otp']='Frontend/resend_otp';
$route['add-to-cart']='Frontend/add_cart';
$route['delete-cart']='Frontend/delete_cart';
$route['get-all-user-cart']='Frontend/get_all_user_cart';
$route['plus-minus-cart-count']='Frontend/plus_minus_cart_count';
$route['save-wishlist']='Frontend/add_wishlist';
$route['get-all-whislist']='Frontend/get_all_whislist';
$route['delete-whislist']='Frontend/delete_whislist';
$route['add-user-comment']='Frontend/add_user_comment';
$route['get-all-address-list']='Frontend/get_all_address_list';
$route['get-search-product']='Frontend/get_search_product';
$route['category-data']='Frontend/category_data';
$route['get-product-on-category']='Frontend/get_product_on_category';
$route['get-product-on-sub-category']='Frontend/get_product_on_sub_category';
$route['get-product-on-child-category']='Frontend/get_product_on_child_category';
$route['check-out-api']='Frontend/check_out_api';
$route['add-payment-data']='Frontend/add_payment_data';
$route['get-confirm-order-details']='Frontend/get_confirm_order_details';
$route['order-place']='Frontend/place_order';
$route['order-history']='Frontend/order_history';
$route['order-history-on-order-id']='Frontend/order_history_on_order_id';
$route['get-all-address-on-user-id']='Frontend/get_all_address_on_user_id';
$route['get-address-on-id']='Frontend/get_address_on_id';
$route['get-product-name-data']='Frontend/get_product_name_data';
$route['save-ratings']='Frontend/save_ratings';
$route['get-delivery-charges-on-address-id']='Frontend/get_delivery_charges_on_address_id';
$route['contact-us-data']='Frontend/contact_us_data';
$route['get-related-product']='Frontend/get_related_product';
$route['subscribed']='Frontend/subscribed';
$route['whatsapp-api']='Frontend/whatsapp_api';
$route['get-gift-card']='Frontend/get_gift_card';
$route['get-product-on-brand']='Frontend/get_product_on_brand';
$route['get-all-product-data']='Frontend/get_all_product_data';
$route['get-all-featured-product']='Frontend/get_all_featured_product';
$route['get-all-latest-product']='Frontend/get_all_latest_product';
$route['get-all-best-selling-product']='Frontend/get_all_best_selling_product';
$route['get-brand-on-search']='Frontend/get_brand_on_search';
$route['delete-address']='Frontend/delete_address';
$route['get-brand-details']='Frontend/get_brand_details';
$route['share_myapp']='Frontend/share_myapp';
