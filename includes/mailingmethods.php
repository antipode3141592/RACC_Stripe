<?php
//use $donor_id to query database for e-mail details and then e-mail them
function racc_mailer($donor_id,$success="no"){
	global $stripe_options;
	global $wpdb;

	try{
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
		$message = "<p><b>Thank you, ".$donor_first_name."!</b></p>";
		$message .= "<p>We are so grateful for your contribution to Work for Art through the Spring 2018 Arts Community Campaign. Your generous support helps our funded groups bring people together through shared experiences, boost our kids’ creativity and critical thinking, spark conversation and social change, and ensure that a wide variety of performances and events are available to everyone in our community.</p>";

		if($anon == 'yes'){
			$message.="<p>Your gift is marked as anonymous: we will withold your name from all publications.</p>";
		}

		if(($artscardqualify == 1)&&($giftartscard != 1)){	//$giftartscard can be null
			$message.="<p>Your contribution qualifies you for The Arts Card! Watch for your new Arts Card to arrive by mail in the next couple of weeks. In the meantime, you can check out upcoming events here: <a href='https://workforart.org/artscardevents'>workforart.org/artscardevents</a></p>";
		}elseif(($artscardqualify == 1)&&($giftartscard == 1)){
			$message.="<p>Your contribution qualifies you for The Arts Card! You have chosen to gift your Arts Card to ".$artscard_name.". We will mail them their Arts Card soon with a little note that it's a gift from you.</p>";
		}

		$message .= "<p>" . $donor_first_name . ", thanks again for joining the campaign in support of ourt arts and culture community.</p>";
		$message .= "<p>Sincerely,<br>Your Work for Art Team<br>503-823-2969<br><a href='mailto:info@workforarg.org'>info@workforart.org</a>";
		
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
				$message.="<p>Please mail your check to:<br>Work for Art<br>411 NW Park Avenue<br>Suite 101"
					."<br>Portland, OR 97209<br>(payment due by June 2018)</p>";
				break;
			case "workplace":
				$message.="<p>Your monthtly payroll deduction of $".number_format(floatval($period_total),2)
					." will begin in July 2018 and continue for ". number_format($period_count,0,'','') ." pay periods. You may update or cancel your recurring gifts at any time by contacting us at 503-823-2969 or <a href='mailto:info@workforarg.org'>info@workforart.org</a>.</p>";
				break;
			case "cc-once":
				if($success == "yes"){
					$message.="<p>Your payment of $". number_format($fund_total,2). " has been received, thank you!</p>";
				}else{
					$message.="<p>We have recieved your pledge of $". number_format($fund_total,2). ", but there was a problem processing your payment, please try again, using the same payment form as before.</p>";
				}
				break;
			case "cc-recur":
				if($success == "yes"){
					$message.="<p>Your monthtly gift of $".number_format(floatval($period_total),2)
						." has begun. We will mail to you an official acknowledgement and tax receipt in the next few days. You may update or cancel your recurring gifts at any time by contacting us at 503-823-2969 or <a href='mailto:info@workforarg.org'>info@workforart.org</a>.</p>";
				}else{
					$message.="<p>We have recieved your annual pledge of $". number_format($fund_total,2). ", but there was a problem processing your first monthly payment of $".number_format(floatval($period_total),2). " please try again, using the same payment form as before.</p>";
				}
				break;
		}

		$message .= "<p><i>Work for Art is a program of the Regional Arts & Culture Council, a 501(c)(3) nonprofit organization – Tax ID #93-1059037. Your gift is tax deductible to the fullest extent of the law. This letter serves as documentation for your tax purposes, along with the following:  your check stub, personal bank record of this contribution, end-of-the-year paystub or Form W-2, or other employer-furnished document showing the amount withheld for this contribution. If you received The Arts Card, please note that this donor benefit has no cash value. If you use The Arts Card to receive complimentary tickets to events and performances, it may lessen the tax-deductibility of your gift; please consult your tax advisor.</i></p>";
		

		$headers[] = 'From: receipt@workforart.org';
		$headers[] = 'Content-type: text/html';
		$result = wp_mail(sanitize_email($donor_email),"Thank you for your gift to Work for Art!", $message, $headers);
		return $result;
	}
	catch(Exception $e){
		error_log("mailingmethods.php error: " + $e->getException());
		return false;
	}
}
?>