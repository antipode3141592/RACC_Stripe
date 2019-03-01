<?php
//use $donor_id to query database for e-mail details and then e-mail them
function racc_mailer($donor_id,$success="no"){
	global $stripe_options;
	global $wpdb;

	try{
		$results = $wpdb->get_results(
			$wpdb->prepare('CALL sp_getresultsdata(%s)',$donor_id));
		if ($results){
			$timestamp = $results[0]->timestamp;
		    $donor_first_name = $results[0]->first_name;
		    $donor_last_name = $results[0]->last_name;
		    $anon = $results[0]->anon;
		    $artscardqualify = $results[0]->artscard;
		    $giftartscard = $results[0]->giftartscard;
		    $organization = $results[0]->org_page;
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
		    $artscard_first_name = $results[0]->artscard_first_name;
		    $artscard_last_name = $results[0]->artscard_last_name;
		    $artscard_email = $results[0]->artscard_email;
		    $artscard_address1 = $results[0]->artscard_add1;
		    $artscard_address2 = $results[0]->artscard_add2;
		    $artscard_city = $results[0]->artscard_city;	
		    $artscard_state = $results[0]->artscard_state;	
		    $artscard_zip = $results[0]->artscard_zip;
		} else {
			exit();
		}
		$message = "<p><b>Thank you, ".$donor_first_name."!</b></p>";
		$message .= "<p>We are very grateful for your";
		if (number_format($fund_total, 2) >= 500.00){
			$message .=" leaderful";
		}		
		$message .= " contribution today. Your generous support helps our funded groups bring people together through shared experiences, boost our kids’ creativity and critical thinking, and ensure that a wide variety of performances and events are available to everyone in our community.</p>";
		// $message .= "<p>" . $donor_first_name . ", thanks again for helping us to bring the power and joy of the arts into our communities.</p>";

		if($anon == 'yes'){
			$message.="<p>Your gift is marked as anonymous: we will withold your name from all publications.</p>";
		}

		if(($artscardqualify == 1)&&($giftartscard != 1)){	//$giftartscard can be null
			$message.="<p>Your contribution qualifies you for The Arts Card! Watch for your new Arts Card to arrive by mail in the next couple of weeks. In the meantime, you can check out upcoming events here: <a href='https://artsimpactfund.racc.org/arts-card/'>artsimpactfund.racc.org/arts-card</a>.</p>";
		}elseif(($artscardqualify == 1)&&($giftartscard == 1)){
			$message.="<p>Your contribution qualifies you for The Arts Card! You have chosen to gift your Arts Card to ".$artscard_first_name
			." ".$artscard_last_name. ". We will mail them their Arts Card soon with a little note that it's a gift from you.</p>";
		}

		$message .= "<p>" . $donor_first_name . ", thanks again for bringing the power and joy of the arts into our communities through your support today.</p>";
		$message .= "<p>Sincerely,<br>Windy Hovey<br>Workplace Giving Coordinator<br>503-823-2969<br><a href='mailto:artsimpactfund@racc.org'>artsimpactfund@racc.org</a>";
		
		$message .= "<hr><br><b>Tax Receipt and Pledge Distribution</b></p>";

		$message .= "<p>Donor: " . $donor_first_name . " " . $donor_last_name;
		if ($fund_community > 0.0){
			$message .=	"<br>Arts Impact Fund: $" . number_format($fund_community,2);
		}
		if ($fund_education > 0.0){
			$message .= "<br>Arts Education Fund: $" . number_format($fund_education,2);
		}
		if ($fund_designated > 0.0){
			$message .= "<br>Designated Fund (" . $fund_designated_name . "): $" . number_format($fund_designated, 2);
		}
		// if (($paytype == "cc-recur") || ($paytype == "workplace")){
		// 	$message .= "<br>Annual Pledge: $" . number_format($fund_total, 2);
		// }else{
		$message .= "<br>Total Pledge: $" . number_format($fund_total, 2);
		// }
		$db_timestamp = strtotime($timestamp);
		$message .= "<br>Date Received: " . date("m-d-Y", $db_timestamp) . "</p>";

		switch($paytype){
			case "check":
				$message.= "<p>Please mail your check at your earliest convenience:<br>Regional Arts and Culture Council<br>411 NW Park Avenue<br>Suite 101"
					."<br>Portland, OR 97209</p>";
				break;
			case "workplace":
				$message.= "<p>Your payroll deduction of $".number_format(floatval($period_total),2)
					." will continue for ". number_format($period_count,0,'','') ." pay periods.</p>";
				break;
			case "ongoing":
				$message .= "<p>Your payroll deduction of $".number_format(floatval($period_total),2)
					." per pay period will continue until you opt out. You may opt out by contacting your HR representative.</p>";
				break;
			case "cc-once":
				$message.= "<p>Your payment of $". number_format($fund_total,2). " has been received, thank you!</p>";
				break;
			case "cc-recur":
				$message.= "<p>Your monthtly gift of $".number_format(floatval($period_total),2)
						." has begun. We will send you a new acknowledgement and Arts Card (if applicable) each year. You may update or cancel your recurring gifts at any time by calling us at 503-823-2969 or e-mail us at <a href='mailto:artsimpactfund@racc.org'>artsimpactfund@racc.org</a>.</p>";
				break;
		}

		$message .= "<p><i>The Regional Arts & Culture Council is a 501(c)(3) nonprofit organization – Tax ID #93-1059037. Your gift is tax deductible to the fullest extent of the law. This letter serves as documentation for your tax purposes, along with the following:  your check stub, personal bank record of this contribution, end-of-the-year paystub or Form W-2, or other employer-furnished document showing the amount withheld for this contribution. If you received The Arts Card, please note that this donor benefit has no cash value. If you use The Arts Card to receive complimentary tickets to events and performances, it may lessen the tax-deductibility of your gift; please consult your tax advisor.</i></p>";

		$headers[] = 'From: artsimpactfund@racc.org';
		$headers[] = 'Content-type: text/html';
		
		//donor e-mail
		$result = wp_mail(sanitize_email($donor_email),"Thank you for your contribution to the arts!", $message, $headers);

		$results2 = $wpdb->get_results(
			$wpdb->prepare('CALL sp_getcomment(%s)',$donor_id));
		if ($results2){
			$comment = $results2[0]->comment;
	    }
		$receipt_message = "<p>Donor Comment: " . $comment ."</p>" . 
							"<p>Donor Org: " . $organization ."</p>" . $message;
		//receipt e-mail to RACC staff
		$result2 = wp_mail($stripe_options['email'],"New AIF Donation - from " . $donor_first_name . " " . $donor_last_name ."!", $receipt_message, $headers);
		return $result;
	}
	catch(Exception $e){
		error_log("mailingmethods.php error: " + $e->getException());
		return false;
	}
}


// //mail function that populates tokens in a text stream (search and replace all {{[token.name]}} with [token.value])
function racc_mailer_2($donor_id,$success="no"){
	global $stripe_options;
	global $wpdb;
	// error_log("racc_mailer_2 begins!");
	try{
		$results = $wpdb->get_results(
			$wpdb->prepare('CALL sp_getresultsdata(%s)',$donor_id));
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
		    $artscard_first_name = $results[0]->artscard_first_name;
		    $artscard_last_name = $results[0]->artscard_last_name;
		    $artscard_email = $results[0]->artscard_email;
		    $artscard_address1 = $results[0]->artscard_add1;
		    $artscard_address2 = $results[0]->artscard_add2;
		    $artscard_city = $results[0]->artscard_city;	
		    $artscard_state = $results[0]->artscard_state;	
		    $artscard_zip = $results[0]->artscard_zip;
		} else {
			exit();
		}

		$template_subject ='';
		$template_body = '';

		$template_type = 'ack_recurring_monthly';
		$results = $wpdb->get_results(
			$wpdb->prepare("SELECT email_subject, email_body FROM racc_email_templates WHERE template_type=%s",$template_type));
		if($results){
			foreach ($results as $result) {
				$template_subject = $result->email_subject;
				$template_body = $result->email_body;
			}
		}
		error_log($template_subject);
		error_log($template_body);
		//initialize message and subject to defaults in database
		$message = $template_body;
		$subject = $template_subject;

		////list of piping variables
		$pipe_vars = array(
			array("{{first_name}}", $donor_first_name),
			array("{{last_name}}", $donor_last_name),
			array("{{timestamp_submit}}", $timestamp),
			array("{{anon}}", $anon),
			array("{{arts_card_qualified}}", $artscardqualify),
		    array("{{gift_arts_card}}", $giftartscard),
		    array("{{organization}}", $organization),
		    array("{{donor_email}}", $donor_email),
		    array("{{amount_fund_community}}", $fund_community),
		    array("{{amount_fund_education}}", $fund_education),
		    array("{{amout_fund_designated}}", $fund_designated),
		    array("{{designated_fund_name}}", $fund_designated_name),
		    array("{{total}}", $fund_total),
		    array("{{paytype}}", $paytype),
		    array("{{total_period}}", $period_total),
		    array("{{number_of_periods}}", $period_count),
		    array("{{donor_address_1}}", $donor_address1),
		    array("{{donor_address_2}}", $donor_address2),
		    array("{{donor_city}}", $donor_city),
		    array("{{donor_state}}", $donor_state),
		    array("{{donor_zip}}", $donor_zip),
		    array("{{artscard_first_name}}", $artscard_first_name),
		    array("{{artscard_last_name}}", $artscard_last_name),
		    array("{{artscard_email}}", $artscard_email),
		    array("{{artscard_address_1}}", $artscard_address1),
		    array("{{artscard_address_2}}", $artscard_address2),
		    array("{{artscard_city}}", $artscard_city),
		    array("{{artscard_state}}", $artscard_state),
		    array("{{artscard_zip}}", $artscard_zip)
		);
		// array("{{first_name}}", $donor_first_name
		// array("{{last_name}}", $donor_last_name
		// array("{{timestamp_submit}}", $timestamp
		// array("{{anon}}", $anon
		

		$subject = str_replace("{{first_name}}", $donor_first_name, $subject);

		foreach ($pipe_vars as $pv) {
			$message = str_replace($pv[0], $pv[1], $message);	
		}
		


		// $headers[] = 'From: artsimpactfund@racc.org';
		$headers[] = 'From: testing@wfadev.pairsite.com';
		$headers[] = 'Content-type: text/html';
		
		//donor e-mail
		$result = wp_mail(sanitize_email($donor_email),$subject, $message, $headers);
		return $result;
	}
	catch(Exception $e){
		error_log("mailingmethods.php error: " + $e->getException());
		return false;
	}
}
?>