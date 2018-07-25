<?php

function racc_stripe_process_payment() {
	if(isset($_POST['action']) && $_POST['action'] == 'stripe' && wp_verify_nonce($_POST['stripe_nonce'], 'stripe-nonce')) {
		global $stripe_options;
		global $wpdb;
		//load the stripe libraries (newest Stripe library has init.php script that loads everything in the correct order
		require_once(STRIPE_BASE_DIR . '/init.php');
		$redirect =get_site_url(null,'/payment-result/');

		$success = 'no';
		$error_message = '';
		$amount = isset($_POST['fund_total']) ? $_POST['fund_total'] * 100 : 0; 	//convert from dollars to cents (stripe standard is cents for everything), default 0.0
		$token = isset($_POST['stripeToken']) ? $_POST['stripeToken'] : null;		//retrieve the token generated by stripe.js
		$fund_community = isset($_POST['fund_community']) ? $_POST['fund_community'] : 0.0;
		$fund_education = isset($_POST['fund_education']) ? $_POST['fund_education'] : 0.0;
		$fund_designated = isset($_POST['fund_designated']) ? $_POST['fund_designated'] : 0.0;
		$fund_designated_name = isset($_POST['fund_designated_name']) ? sanitize_text_field($_POST['fund_designated_name']) : null;
		$donation_frequency = isset($_POST['donation_frequency']) ? $_POST['donation_frequency'] : null;
		$fund_total = isset($_POST['fund_total']) ? $_POST['fund_total'] : 0.0;
		$payperiods = isset($_POST['payperiodinputs']) ? $_POST['payperiodinputs'] : 1;
		$period_total = isset($_POST['period_total']) ? $_POST['period_total'] : 0.0;
		$artscardqualify = isset($_POST['artscardqualify']) ? $_POST['artscardqualify'] : 'no';
		$designated = isset($_POST['sc_dg']) ? $_POST['sc_dg'] : 'no';
		$comment = isset($_POST['comment_input']) ? sanitize_text_field($_POST['comment_input']) : null;

		//donor information
		$sc_org = isset($_POST['sc_organization']) ? sanitize_text_field($_POST['sc_organization']) : null;
		$org = isset($_POST['donor_org_input']) ? sanitize_text_field($_POST['donor_org_input']) : null;
		$donor_first_name = isset($_POST['donor_first_name']) ? sanitize_text_field($_POST['donor_first_name']) : null;
		$donor_middle_name = isset($_POST['donor_middle_name']) ? sanitize_text_field($_POST['donor_middle_name']) : null;
		$donor_last_name = isset($_POST['donor_last_name']) ? sanitize_text_field($_POST['donor_last_name']) : null;
		$donor_phone = isset($_POST['donor_phone']) ? sanitize_text_field($_POST['donor_phone']) : null;
		$donor_email = isset($_POST['donor_email']) ? sanitize_email($_POST['donor_email']) : null;
		$donor_address_1 = isset($_POST['donor_address_1']) ? sanitize_text_field($_POST['donor_address_1']) : null;
		$donor_address_2 = isset($_POST['donor_address_2']) ? sanitize_text_field($_POST['donor_address_2']) : null;
		$donor_city = isset($_POST['donor_city']) ? sanitize_text_field($_POST['donor_city']) : null;
		$donor_state = isset($_POST['donor_state']) ? sanitize_text_field($_POST['donor_state']) : null;
		$donor_zip = isset($_POST['donor_zip']) ? sanitize_text_field($_POST['donor_zip']) : null;
		$anon = isset($_POST['anon']) ? $_POST['anon'] : 'no';	//if anon is checked, set to yes, else, no

		//arts card address (may be different from donor information)
		$artscard_name = isset($_POST['artscard_name']) ? sanitize_text_field($_POST['artscard_name']) : null;
		$artscard_email = isset($_POST['artscard_email']) ? sanitize_email($_POST['artscard_email']) : null;
		$artscard_address_1 = isset($_POST['artscard_address_1']) ? sanitize_text_field($_POST['artscard_address_1']) : null;
		$artscard_address_2 = isset($_POST['artscard_address_2']) ? sanitize_text_field($_POST['artscard_address_2']) : null;
		$artscard_city = isset($_POST['artscard_city']) ? sanitize_text_field($_POST['artscard_city']) : null;
		$artscard_state = isset($_POST['artscard_state']) ? sanitize_text_field($_POST['artscard_state']) : null;
		$artscard_zip = isset($_POST['artscard_zip']) ? sanitize_text_field($_POST['artscard_zip']) : null;
		$giftartscard = isset($_POST['giftartscard']) ? $_POST['giftartscard'] : 'no';
 		
		$new_donor_id = date('YmdHis') . rand(1,1000000);
		// Insert data into database, using parameterized stored procedures
		try{
			//wpdb is the WordPress database, query() is used to run the stored procedure call created by prepare()
			if($artscardqualify == 'yes'){
				$db_artscard = 1;
			} else {$db_artscard = 0; }
			if($giftartscard == 'yes'){
				$db_giftartscard = 1;
			}else {$db_giftartscard = 0;}
			if ($sc_org == "None"){
				if ($org != "None"){
					$temp_org = $org;
				}else{
					$temp_org = "None";
				}
			}else{
				$temp_org = $sc_org;
			}
			//store basic contact data
			$rows_inserted = $wpdb->query(
				$wpdb->prepare("CALL sp_adddonor(%s,%s,%s,%s,%s,%s,%s,%s)", $donor_first_name, $donor_middle_name, $donor_last_name, $new_donor_id, $anon,$db_artscard, $db_giftartscard, $temp_org));
			$rows_inserted = $wpdb->query(
				$wpdb->prepare("CALL sp_addcontactinfo(%s,%s,%s)", "email", $donor_email, $new_donor_id));
			if($donor_phone != null){
			$rows_inserted = $wpdb->query(
				$wpdb->prepare("CALL sp_addcontactinfo(%s,%s,%s)", "phone", $donor_phone, $new_donor_id));
			}
			$rows_inserted = $wpdb->query(
				$wpdb->prepare("CALL sp_addaddress(%s,%s,%s,%s,%s,%s,%s,%s)", $donor_address_1, $donor_address_2, $donor_city, $donor_state, $donor_zip, "Primary",$new_donor_id, null));
			//store Arts Card address, if qualified
			if($giftartscard == "yes")
			{
				$rows_inserted = $wpdb->query(
				$wpdb->prepare("CALL sp_addaddress(%s,%s,%s,%s,%s,%s,%s,%s)", $artscard_address_1, $artscard_address_2, $artscard_city, $artscard_state, $artscard_zip, "Arts Card",$new_donor_id, $artscard_name));
				$rows_inserted = $wpdb->query(
				$wpdb->prepare("CALL sp_addcontactinfo(%s,%s,%s)", "email-artscard", $artscard_email, $new_donor_id));
			}
			//store allocations greater than 0.0
			if($fund_community > 0.0){
				$rows_inserted = $wpdb->query(
					$wpdb->prepare("CALL sp_addallocation(%s,%s,%s,%s,%s,%s,%s,%s)", $new_donor_id,"community", number_format($fund_community,2,'.',''), number_format($fund_total,2,'.',''),$donation_frequency, number_format($period_total,2,'.',''),number_format($payperiods,0,'',''),"community"));
			}
			if($fund_education > 0.0){
				$rows_inserted = $wpdb->query(
					$wpdb->prepare("CALL sp_addallocation(%s,%s,%s,%s,%s,%s,%s,%s)", $new_donor_id,"education", number_format($fund_education,2,'.',''), number_format($fund_total,2,'.',''),$donation_frequency, number_format($period_total,2,'.',''),number_format($payperiods,0,'',''),"education"));
			}
			if($fund_designated > 0.0){
				$rows_inserted = $wpdb->query(
					$wpdb->prepare("CALL sp_addallocation(%s,%s,%s,%s,%s,%s,%s,%s)", $new_donor_id, $fund_designated_name, number_format($fund_designated,2,'.',''), number_format($fund_total,2,'.',''),$donation_frequency, number_format($period_total,2,'.',''),number_format($payperiods,0,'',''),"designated"));
			}
			//store comment
			if ($comment != null) {
				$rows_inserted = $wpdb->query(
				$wpdb->prepare("CALL sp_addcomment(%s,%s)", $new_donor_id, $comment));
			}
		}catch(Exception $e){
			error_log("process-payment, database calls error: " + $e->getMessage());
		}
 		if (($donation_frequency == "cc-once") or ($donation_frequency == "cc-recur") or ($donation_frequency == "cc-annual")){
			//check for test mode
			if(isset($stripe_options['test_mode']) && $stripe_options['test_mode']) {
				$secret_key = $stripe_options['test_secret_key'];
				$product_id = 'prod_CmGEdHms0uIcOx';
			} else {
				$secret_key = $stripe_options['live_secret_key'];
				$product_id = 'prod_CuHVCy1RGoijo1';
			}
			//NOTE: latest Stripe API adds namespaces, so be sure to use the proper format for calling classes
			\Stripe\Stripe::setApiKey($secret_key);
			\Stripe\Stripe::setAppInfo("RACC_Stripe");
			//for both cc/debit cases, create a customer
			try{
				$customer = \Stripe\Customer::create(array(	//NOTE: function call was Stripe_Customer in prev Stripe API
						'source' => $token,
						'email' => strip_tags($donor_email),
						'metadata' => array(
							'id_token' => strip_tags($new_donor_id))
					)
				);	
				//determine if one-time or recurring
				if (($donation_frequency == "cc-recur") or ($donation_frequency == "cc-annual")) {
					//Process recurring payment
					try {
						// if(isset($stripe_options['test_mode']) && $stripe_options['test_mode']) {
						// 	// $product_id = 'prod_Bav1bXDCwcN0Kr';
						// 	$product_id = 'prod_CmGEdHms0uIcOx';
						// } else {
						// 	$product_id = 'prod_CuHVCy1RGoijo1';
						// }

						if ($donation_frequency == "cc-recur"){
							$plan = \Stripe\Plan::create(array(
								'amount' => number_format(floatval($period_total)*100,0,'.',''),
								'interval' => 'month',
								'product' => $product_id,
								'currency' => 'usd',
								)
							);
						}
						else{
							$plan = \Stripe\Plan::create(array(
								'amount' => number_format(floatval($amount),0,'.',''),
								'interval' => 'year',
								'product' => $product_id,
								'currency' => 'usd',
								)
							);
						}
						$subscription = \Stripe\Subscription::create(array(
							'customer' => $customer->id,
							'items' => array(
								array('plan' => $plan->id))
							)
						);
						$success = 'yes';
					} catch(\Stripe\Error\Card $e) {
						//decline error
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\RateLimit $e) {
					  // Too many requests made to the API too quickly
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\InvalidRequest $e) {
					  // Invalid parameters were supplied to Stripe's API
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\Authentication $e) {
					  // Authentication with Stripe's API failed
					  // (maybe you changed API keys recently)
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\ApiConnection $e) {
					  // Network communication with Stripe failed
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\Base $e) {
					  // Display a very generic error to the user, and maybe send
					  // yourself an email
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (Exception $e) {
						$success = 'no';
						$error_message = $e->getMessage();
						error_log('Stripe Recurring Payment Error: '.$e->getMessage());
					}
				} elseif ($donation_frequency == "cc-once") {
					//process one-time payment
					//attempt to charge the customer's card
					try {
						$charge = \Stripe\Charge::create(array(	//NOTE: function call was Stripe_Charge in prev Stripe API
								'amount' => $amount,
								'currency' => 'usd',
								'statement_descriptor' => 'Work for Art Donation',
								'customer' => $customer->id,
							)
						);
						$success = 'yes';
					} catch(\Stripe\Error\Card $e) {
						//decline error
						$success = 'no';
					$body = $e->getJsonBody();
					$err = $body['error'];
	  				$error_message = $err['message'];
	  				error_log("Error Type: " + $err['type']);
	  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\RateLimit $e) {
					  // Too many requests made to the API too quickly
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\InvalidRequest $e) {
					  // Invalid parameters were supplied to Stripe's API
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\Authentication $e) {
					  // Authentication with Stripe's API failed
					  // (maybe you changed API keys recently)
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\ApiConnection $e) {
					  // Network communication with Stripe failed
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (\Stripe\Error\Base $e) {
					  // Display a very generic error to the user, and maybe send
					  // yourself an email
						$success = 'no';
						$body = $e->getJsonBody();
						$err = $body['error'];
		  				$error_message = $err['message'];
		  				error_log("Error Type: " + $err['type']);
		  				error_log("Error Message: " + $err['message']);
					} catch (Exception $e) {
						$success = 'no';
						$error_message = $e->getMessage();
						error_log('Stripe One-Time Payment Error: '.$e->getMessage());
					}
				}
			} catch(\Stripe\Error\Card $e) {
				//decline error
				$success = 'no';
				$body = $e->getJsonBody();
				$err = $body['error'];
  				$error_message = $err['message'];
  				error_log("Error Type: " + $err['type']);
  				error_log("Error Message: " + $err['message']);
			} catch (\Stripe\Error\RateLimit $e) {
			  // Too many requests made to the API too quickly
				$success = 'no';
				$body = $e->getJsonBody();
				$err = $body['error'];
  				$error_message = $err['message'];
  				error_log("Error Type: " + $err['type']);
  				error_log("Error Message: " + $err['message']);
			} catch (\Stripe\Error\InvalidRequest $e) {
			  // Invalid parameters were supplied to Stripe's API
				$success = 'no';
				$body = $e->getJsonBody();
				$err = $body['error'];
  				$error_message = $err['message'];
  				error_log("Error Type: " + $err['type']);
  				error_log("Error Message: " + $err['message']);
			} catch (\Stripe\Error\Authentication $e) {
			  // Authentication with Stripe's API failed
			  // (maybe you changed API keys recently)
				$success = 'no';
				$body = $e->getJsonBody();
				$err = $body['error'];
  				$error_message = $err['message'];
  				error_log("Error Type: " + $err['type']);
  				error_log("Error Message: " + $err['message']);
			} catch (\Stripe\Error\ApiConnection $e) {
			  // Network communication with Stripe failed
				$success = 'no';
				$body = $e->getJsonBody();
				$err = $body['error'];
  				$error_message = $err['message'];
  				error_log("Error Type: " + $err['type']);
  				error_log("Error Message: " + $err['message']);
			} catch (\Stripe\Error\Base $e) {
			  // Display a very generic error to the user, and maybe send
			  // yourself an email
				$success = 'no';
				$body = $e->getJsonBody();
				$err = $body['error'];
  				$error_message = $err['message'];
  				error_log("Error Type: " + $err['type']);
  				error_log("Error Message: " + $err['message']);
			} catch (Exception $e) {
				$success = 'no';
				$error_message = $e->getMessage();
				error_log('Stripe Customer Create Error: '.$e->getMessage());
			}
		} elseif ($donation_frequency == "workplace"){
			$result = racc_mailer($new_donor_id,"yes");	
			$success = 'yes';
		} elseif ($donation_frequency == "check"){
			$result = racc_mailer($new_donor_id,"yes");	
			$success = 'yes';
		}
		
		wp_safe_redirect(esc_url_raw(add_query_arg(array(
			'success' => $success,
			//'error_message' => $error_message,
			'id' => $new_donor_id
		), $redirect))); exit;
	}	
}
add_action('init', 'racc_stripe_process_payment',1,0);
?>
