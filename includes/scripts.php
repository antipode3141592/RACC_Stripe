<?php
 
function racc_load_stripe_scripts() {
 
	global $stripe_options;
	global $racc_db_version;
 
	// check to see if we are in test mode
	if(isset($stripe_options['test_mode']) && $stripe_options['test_mode']) {
		$publishable = $stripe_options['test_publishable_key'];
	} else {
		$publishable = $stripe_options['live_publishable_key'];
	}
 	
	wp_enqueue_script('jquery');
	wp_enqueue_script('stripe', 'https://js.stripe.com/v3/');
	wp_enqueue_script('stripe-processing', STRIPE_BASE_URL . '/includes/js/stripe-processing.js?v=1.7');	//var is to force js update
	wp_enqueue_script('stripe-elements', STRIPE_BASE_URL . '/includes/js/stripe-elements.js');
	wp_enqueue_script( 'grecaptcha', 'https://www.google.com/recaptcha/api.js' );
	wp_enqueue_script('selector', STRIPE_BASE_URL . '/includes/js/selector.js');
	wp_enqueue_script('results', STRIPE_BASE_URL . '/includes/js/results.js');
	wp_localize_script('stripe-processing', 'stripe_vars', array(
			'publishable_key' => $publishable,
		)
	);
	wp_localize_script('stripe-elements', 'stripe_vars', array(
			'publishable_key' => $publishable,
		)
	);
	wp_localize_script('selector', 'urls',array(
			'base_url' => STRIPE_BASE_URL,
			'site_url' => site_url()
		)
	);

	//register styles
	wp_register_style('RACC-Stripe-css', STRIPE_BASE_URL . '/RACC-Stripe.css');
    wp_enqueue_style('RACC-Stripe-css');

 }
add_action('wp_enqueue_scripts', 'racc_load_stripe_scripts');

