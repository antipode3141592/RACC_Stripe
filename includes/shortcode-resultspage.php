<?php
//creates result page
function racc_stripe_resultpage($atts, $content = null){
	global $wpdb;
	$success = isset($_GET['success']) ? sanitize_text_field($_GET['success']) : 'no';
	$error_message = isset($_GET['error_message']) ? sanitize_text_field($_GET['error_message']) : '';
	$donor_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';

	ob_start();
	if(($donor_id) && ($success == 'yes')){
		//get data from db
		$results = $wpdb->get_results(
			$wpdb->prepare('CALL sp_getsingledonordata(%s)',$donor_id));
		if ($results){
			$timestamp = $results[0]->timestamp;
		    $donor_first_name = $results[0]->first_name;
		    $donor_last_name = $results[0]->last_name;
		    $anon = $results[0]->anon;
		    $artscardqualify = $results[0]->artscard;
		    $giftartscard = $results[0]->giftartscard;
		    $organization = $results[0]->organization;
		    $donor_email = $results[0]->email;	
		    $fund_community = $results[0]->community_fund;
		    $fund_education = $results[0]->education_fund;
		    $fund_designated = $results[0]->designated_fund;
		    $fund_designated_name = $results[0]->designated_name;
		    $fund_total = $results[0]->pledgetotal;
		    $paytype = $results[0]->paytype;
		    $period_total = $results[0]->periodtotal;
		    $donor_address1 = $results[0]->address1;	
		    $donor_address2 = $results[0]->address2;	
		    $donor_city = $results[0]->city;
		    $donor_state = $results[0]->state;
		    $donor_zip = $results[0]->zipcode;
		    $artscard_name = $results[0]->artscard_name;
		    $artscard_address1 = $results[0]->artscard_add1;
		    $artscard_address2 = $results[0]->artscard_add2;
		    $artscard_city = $results[0]->artscard_city;	
		    $artscard_state = $results[0]->artscard_state;	
		    $artscard_zip = $results[0]->artscard_zip;
		}
		?>
		<div id="resultscontent">
			<h1>Thank You!</h1>
			<div id="results_intro_paragraph">
				<p><?php _e(nl2br("Thank you, ".$donor_first_name.", for your pledge of $" . number_format($fund_total,2)
					."!\nArts Community Fund: $".number_format($fund_community,2)
					."\nArts Education Fund: $".number_format($fund_education,2)
					."\nArts Designated Fund (". $fund_designated_name ."): $".number_format($fund_designated,2))) ?>
				</p>
				<p><?php _e(nl2br("\n\nA receipt will be e-mailed to you at ". $donor_email)) ."." ?>
				</p>
			</div>

			<div id="results_main_body"><p><?php 
				switch($donation_frequency){
				case "check":
					$message="\nPlease mail your check by June 30,2018:\nWork for Art\n411 NW Park Avenue\nSuite 101"
						."\nPortland, OR 97209\n\nWe will mail to you an official acknowledgement and tax receipt in the next few days.";
					break;
				case "workplace":
					$message="\nYour payroll deduction of $".number_format(floatval($period_total),2)
						." per period will begin July 2018. We will mail to you an official acknowledgement and tax receipt in the next few days.";
					break;
				case "cc-once":
					$message="\nSuccessful one-time credit card payment, thanks!"
					."We will mail to you an official acknowledgement and tax receipt in the next few days.";
					break;
				case "cc-recur":
					$message="\nSuccessful monthly recurring credit card payment for $".number_format(floatval($period_total),2).", thank you!"
					." We will mail to you an official acknowledgement and tax receipt in the next few days.";
			}		
			_e(nl2br($message)); 
			?></p></div>
			<div id="results_artscard">
				<?php if(($artscardqualify == 1)&&($giftartscard == 0)){
					?>
					<p><?php _e(nl2br("Your gift qualifies you for the Arts Card and it will be mailed to you with your acknowledgement letter."));?></p>
					<?php
				}elseif(($artscardqualify == 1)&&($giftartscard == 1)){
					?>
					<p><?php _e(nl2br("Your gift qualifies you for the Arts Card. You have chosen to gift it to ".strip_tags($artscard_name).". We will mail them their Arts Card soon with a note that it's a gift from you."));?></p>
					<?php
				}
				?>
			</div>
			<div id="results_anon">
				<?php 
				if($anon == "yes"){
					?>
					<p>Your gift is marked as anonymous: we will withold your name from all publications.</p><?php
				}
				?>
			</div>
			<div id="results_signature_lines">
				<br>
				<p>With gratitude,<br>
					Your Work for Art Team<br>
					503-823-2969</p>
			</div>
		</div>
		<?php
	}else{
		?>
		<h1>There was an error processing your payment!</h1>
		<div id="error_message_container">
			<p><?php _e($error_message);?></p>
		</div>
		<?php
	}
	?>
	<div id="printerdiv">
		<button type="button" id="printpreviewbutton">Print Preview</button>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('racc_resultpage', 'racc_stripe_resultpage');
?>