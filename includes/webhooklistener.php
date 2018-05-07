<?php /* Template Name: Stripe Webhook Reciever */

function racc_stripe_listener($atts, $content = null){
	if(isset($_GET['wps-listener']) && $_GET['wps-listener'] == 'stripe') {
		http_response_code(200);
		global $stripe_options;
		global $wpdb;
		//load the stripe libraries (newest Stripe library has init.php script that loads everything in the correct order
		require_once(STRIPE_BASE_DIR . '/init.php');
		//check for test mode
		if(isset($stripe_options['test_mode']) && $stripe_options['test_mode']) {
			$secret_key = $stripe_options['test_secret_key'];
		} else {
			$secret_key = $stripe_options['live_secret_key'];
		}
		//NOTE: latest Stripe API adds namespaces, so be sure to use the proper format for calling classes
		\Stripe\Stripe::setApiKey($secret_key);
		\Stripe\Stripe::setAppInfo("RACC_Stripe");
		// retrieve the request's body and parse it as JSON
		try{
			$body = file_get_contents('php://input');
			// grab the event information
			$event_json = json_decode($body);
			// error_log($event_json);
		}catch(Exception $e){
			error_log($e->message);
		}
		$subject = '';
		$message = '';
		// this will be used to retrieve the event from Stripe
		// re-retrieving helps prevent attacks with fabricated event ids
		if(isset($event_json->id)) {
			//if real event, respond with 2xx HTTP status code
			$event_id = $event_json->id;
			// to verify this is a real event, we re-retrieve the event from Stripe 
			try{
				$event = \Stripe\Event::retrieve($event_id);
				$customer = \Stripe\Customer::retrieve($event->data->object->customer);
				$customer_wp_id = $customer->metadata->id_token;

				// successful payment, both one time and recurring payments
				if($event->type == 'charge.succeeded') {
					$result = racc_mailer2($customer_wp_id,"yes");	
				}
				// failed payment
				elseif($event->type == 'charge.failed') {
					$result = racc_mailer2($customer_wp_id,"no");
				}
				else{
					error_log("No handler for this event type: " . $event->type);
				}
			} catch(\Stripe\Error\Card $e) {
				//decline error
				$body = $e->getJsonBody();
  				$err  = $body->error;
  				error_log('type: ' . $err->type);
  				error_log('message: ' . $err->message);
			} catch (\Stripe\Error\RateLimit $e) {
			  // Too many requests made to the API too quickly
				$body = $e->getJsonBody();
  				$err  = $body->error;
  				error_log('type: ' . $err->type);
  				error_log('message: ' . $err->message);
			} catch (\Stripe\Error\InvalidRequest $e) {
			  // Invalid parameters were supplied to Stripe's API
				$body = $e->getJsonBody();
  				$err  = $body->error;
  				error_log('type: ' . $err->type);
  				error_log('message: ' . $err->message);
			} catch (\Stripe\Error\Authentication $e) {
			  // Authentication with Stripe's API failed
			  // (maybe you changed API keys recently)
				$body = $e->getJsonBody();
  				$err  = $body->error;
  				error_log('type: ' . $err->type);
  				error_log('message: ' . $err->message);
			} catch (\Stripe\Error\ApiConnection $e) {
			  // Network communication with Stripe failed
				$body = $e->getJsonBody();
  				$err  = $body->error;
  				error_log('type: ' . $err->type);
  				error_log('message: ' . $err->message);
			} catch (\Stripe\Error\Base $e) {
			  // Display a very generic error to the user, and maybe send
			  // yourself an email
				$body = $e->getJsonBody();
				$err  = $body->error;
  				error_log('type: ' . $err->type);
  				error_log('message: ' . $err->message);
			}catch(Exception $e){
				error_log($e->message);
			}
		}
	}
}

add_shortcode('racc_stripe_listener', 'racc_stripe_listener');