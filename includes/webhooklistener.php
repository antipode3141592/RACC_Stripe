<?php /* Template Name: Stripe Webhook Reciever */

function racc_stripe_listener($atts, $content = null){
	if(isset($_GET['wps-listener']) && $_GET['wps-listener'] == 'stripe') {
		http_response_code(200);
		global $stripe_options;
		global $wpdb;
		//load the stripe libraries (newest Stripe library has init.php script that loads everything in the correct order
		require_once(STRIPE_BASE_DIR . '/init.php');

		$sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];	//grab signing signature
		//check for test mode
		if(isset($stripe_options['test_mode']) && $stripe_options['test_mode']=="test")  {
			$secret_key = $stripe_options['test_secret_key'];
			$endpoint_secret = $stripe_options['test_endpoint_key'];
		} else {
			$secret_key = $stripe_options['live_secret_key'];
			$endpoint_secret = $stripe_options['live_endpoint_key'];
		}
		//NOTE: latest Stripe API adds namespaces, so be sure to use the proper format for calling classes
		\Stripe\Stripe::setApiKey($secret_key);
		\Stripe\Stripe::setAppInfo("RACC_Stripe");
		// retrieve the request's body and parse it as JSON
		try{
			$body = file_get_contents('php://input');
			// grab the event information
			$event_json = json_decode($body);
		}catch(Exception $e){
			error_log("webhooklistener error: " + $e->message);
		}
		$subject = '';
		$message = '';
		// this will be used to retrieve the event from Stripe
		// re-retrieving helps prevent attacks with fabricated event ids
		if(isset($event_json->id)) {
			//if real event, respond with 2xx HTTP status code
			// $event_id = $event_json->id;
			// $event = $event_json;
			// error_log(json_encode($event_json, JSON_PRETTY_PRINT));
			// to verify this is a real event, we re-retrieve the event from Stripe 
			try{
				$event = \Stripe\Webhook::constructEvent($body, $sig_header, $endpoint_secret);
				// $event = \Stripe\Event::retrieve($event_id);
				$customer = \Stripe\Customer::retrieve($event->data->object->customer);
				$customer_wp_id = $customer->metadata->id_token;
				error_log(json_encode($event, JSON_PRETTY_PRINT));
				error_log(json_encode($customer, JSON_PRETTY_PRINT));
				error_log("customer id: " . $customer_wp_id);

				// successful payment, both one time and recurring payments
				if(isset($event) && ($event->type == 'charge.succeeded')) {
					error_log($event->data->object->invoice);
					$invoice = isset($event->data->object->invoice) ? $event->data->object->invoice : null;
					if($invoice == null) {
						// error_log("charge succeeded webhook");
						$result = racc_mailer_2($customer_wp_id,"yes", "cc-once");
					} else {
						error_log("webhooklistener error:  charge.succeeded returned a invoice id, skipping email");
					}
				}
				//
				elseif($event->type == 'customer.subscription.created'){
					$period = $event->data->object->plan->interval;
					switch ($period){
						case 'month':
							// error_log("month!");
							$result = racc_mailer_2($customer_wp_id, "yes", "cc-recur");
							break;
						case 'year':
							// error_log("annual!?!?!");
							$result = racc_mailer_2($customer_wp_id, "yes", "cc-annual");
							break;
						default:
							error_log("default -  no known interval/period type: " . $period);
					}
					// error_log($period);
				}
				// failed payment
				elseif($event->type == 'charge.failed') {
					// error_log("charge failed, failure code: " . $event->data->object->failure_code . ", failure message: " . $event->data->object->failure_message);
					$result = racc_mailer_2($customer_wp_id,"no", "charge-failed");
				}
				elseif($event->type == 'charge.expired') {
					$result = racc_mailer_2($customer_wp_id,"no", "charge-expired");
				}
				else{
					error_log("No handler for this event type: " . $event->type);
				}
			} catch (\Stripe\Error\Base $e) {
			  // Display a very generic error to the user, and maybe send
			  // yourself an email
				error_log($e->getMessage());
			}catch(Exception $e){
				error_log("webhooklistener error: " + $e->message);
			}
		}
	}
}

add_shortcode('racc_stripe_listener', 'racc_stripe_listener');