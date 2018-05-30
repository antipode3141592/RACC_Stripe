<?php
//creates result page
function racc_stripe_resultpage($atts, $content = null){
	global $wpdb;
	$success = isset($_GET['success']) ? sanitize_text_field($_GET['success']) : 'no';
	// $error_message = isset($_GET['error_message']) ? sanitize_text_field($_GET['error_message']) : '';
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
		    $period_count = $results[0]->period_count;
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
			<div id="results_intro_paragraph">
				<p><b><?php _e("Thank you, ".$donor_first_name."!") ?></b></p>
				<p>We are so grateful for your <?php 
				if (number_format($fund_total, 2) >= 500.00){
					_e("leaderful");
				}
				?> contribution to Work for Art! Your generous support helps our funded groups bring people together through shared experiences, boost our kids’ creativity and critical thinking, spark conversation and social change, and ensure that a wide variety of performances and events are available to everyone in our community.</p>
				<p><?php _e("A donation receipt will be e-mailed to you at " . $donor_email . ".")  ?></p>
			</div>
			<br>
			<?php
				if($anon == 'yes'){
					?><div id="anon_div"><p>Your gift is marked as anonymous: we will withold your name from all publications.</p></div><?php
				}
				if(($artscardqualify == 1)&&($giftartscard != 1)){	//$giftartscard can be null
					?><div id="artscard_div"><p>Your contribution qualifies you for The Arts Card! Watch for your new Arts Card to arrive by mail in the next couple of weeks. In the meantime, you can check out upcoming events here: <a href="https://workforart.org/artscardevents">workforart.org/artscardevents</a>.</p></div><?php
				}elseif(($artscardqualify == 1)&&($giftartscard == 1)){
					?><div id="artscard_div"><p>Your contribution qualifies you for The Arts Card! You have chosen to gift your Arts Card to <?php _e($artscard_name) ?>. We will mail them their Arts Card soon with a little note that it's a gift from you.</p></div><?php
				}

			?>

			<div id="results_main_body"><p><?php
				$message = "<p>" . $donor_first_name . ", thanks again for bringing the power and joy of the arts into our communities through your support today.</p>";
				$message .= "<br><p>Sincerely,<br>Your Work for Art Team<br>503-823-2969<br><a href='mailto:info@workforarg.org'>info@workforart.org</a>";
				$message .= "<hr><br><b>Work for Art Tax Receipt and Pledge Distribution</b></p>";

				$message .= "<p>Donor: " . $donor_first_name . " " . $donor_last_name;
				if ($fund_community > 0.0){
					$message .=	"<br>Arts Community Fund: $" . number_format($fund_community,2);
							
				}
				if ($fund_education > 0.0){
					$message .= "<br>Arts Education Fund: $" . number_format($fund_education,2);
				}

				if ($fund_designated > 0.0){
					$message .= "<br>Designated Fund (" . $fund_designated_name . "): $" . number_format($fund_designated, 2);
				}
				if (($paytype == "cc-recur") || ($paytype == "workplace")){
					$message .= "<br>Annual Pledge: $" . number_format($fund_total, 2);
				}else{
					$message .= "<br>Total Pledge: $" . number_format($fund_total, 2);
				}
				$db_timestamp = strtotime($timestamp);
				$message .= "<br>Date Received: " . date("m-d-Y", $db_timestamp) . "</p>";
				switch($paytype){
				case "check":
					$message.="<p>Please mail your check by June 30 to:<br>Work for Art<br>411 NW Park Avenue<br>Suite 101"
						."<br>Portland, OR 97209</p>";
					break;
				case "workplace":
					$message.="<p>Your payroll deduction of $".number_format(floatval($period_total),2)
						." will begin in July 2018 and continue for ". number_format($period_count,0,'','') ." pay periods.</p>";
					break;
				case "cc-recur":
					$message.="<p>Your monthly gift of $".number_format(floatval($period_total),2)
							." has begun. We will send you a new acknowledgement and Arts Card (if applicable) each year. You may update or cancel your recurring gifts at any time by calling us at 503-823-2969 or e-mail us at <a href='mailto:info@workforarg.org'>info@workforart.org</a>.</p>";
					break;
				}	
				_e($message); 
			?></p></div>
			<div id="tax_info">
				<p><i>Work for Art is a program of the Regional Arts & Culture Council, a 501(c)(3) nonprofit organization – Tax ID #93-1059037. Your gift is tax deductible to the fullest extent of the law. This letter serves as documentation for your tax purposes, along with the following:  your check stub, personal bank record of this contribution, end-of-the-year paystub or Form W-2, or other employer-furnished document showing the amount withheld for this contribution. If you received The Arts Card, please note that this donor benefit has no cash value. If you use The Arts Card to receive complimentary tickets to events and performances, it may lessen the tax-deductibility of your gift; please consult your tax advisor.</i></p>
			</div>
		</div>
		<div id="printerdiv">
			<button type="button" id="printpreviewbutton">Print Friendly</button>
		</div>
		<?php
	}else{
		?>
		<h1>There was an error processing your payment!</h1>
		<div id="error_message_container">
			<h2>Please go back and ensure your payment details are correct.</h2>
			<button type="button" id="errorbackbutton">Back to Payment</button>
		</div>

		<?php
	}
	?>
	
	<?php
	return ob_get_clean();
}
add_shortcode('racc_resultpage', 'racc_stripe_resultpage');
?>