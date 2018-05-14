<?php
/*
Plugin Name: WordPress Stripe Integration for RACC
Plugin URI: https://racc.org
Description: Plugin with shortcode form for pledge payments
Author: Sean Kirkpatrick
Author URI: https://racc.org
Contributors: 
Version: 0.9
*/
 
/**********************************
* constants and globals
**********************************/
 
if(!defined('STRIPE_BASE_URL')) {
	define('STRIPE_BASE_URL', plugin_dir_url(__FILE__));
}
if(!defined('STRIPE_BASE_DIR')) {
	define('STRIPE_BASE_DIR', dirname(__FILE__));
}
 
$stripe_options = get_option('stripe_settings');
 
/*******************************************
* plugin text domain for translations
*******************************************/
 
load_plugin_textdomain( 'RACC_stripe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
 
/**********************************
* includes
**********************************/
 
if(is_admin()) {
	// load admin includes
	include(STRIPE_BASE_DIR . '/includes/settings.php');
} else {
	// load front-end includes
	include(STRIPE_BASE_DIR . '/includes/scripts.php');
	include(STRIPE_BASE_DIR . '/includes/process-payment.php');	
	include(STRIPE_BASE_DIR . '/includes/mailingmethods.php');	
	include(STRIPE_BASE_DIR . '/includes/shortcode-resultspage.php');
	include(STRIPE_BASE_DIR . '/includes/webhooklistener.php');
	include(STRIPE_BASE_DIR . '/includes/shortcode-elementsjs.php');
	include(STRIPE_BASE_DIR . '/includes/shortcode-selector.php');
	include(STRIPE_BASE_DIR . '/includes/shortcode-dbdownload.php');
	include(STRIPE_BASE_DIR . '/includes/dbdownload.php');
}

