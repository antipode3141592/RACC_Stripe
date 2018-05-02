<?php

function racc_mailer($donor_first_name,$fund_total,$fund_community,$fund_education,$artscardqualify,$giftartscard,$anon,$artscard_name,$donor_email,$donation_frequency,$period_total){
	try{
		$message = "Thank you, ".strip_tags($donor_first_name).", for your pledge of $" . number_format($fund_total,2)."!\nCommunity Fund: $".number_format($fund_community,2)."\nArts Education Fund: $".number_format($fund_education,2);
		switch($donation_frequency){
			case "check":
				$message.="\n\nPlease mail your check to:\nWork for Art\n411 NW Park Avenue\nSuite 101"
					."\nPortland, OR 97209\n(payment due by June 2018)\n\nWe will mail to you an official acknowledgement and tax receipt in the next few days.";
				break;
			case "workplace":
				$message.="\n\nYour monthtly payroll deduction of $".number_format(floatval($period_total),2)
					." will begin July 2018. We will mail to you an official acknowledgement and tax receipt in the next few days.";
				break;
		}
		
		if(($artscardqualify == "yes")&&($giftartscard == "no")){
			$message.="\n\nYour gift qualifies you for the Arts Card and will be mailed to you with your acknowledgement letter.";
		}elseif(($artscardqualify == "yes")&&($giftartscard == "yes")){
			$message.="\n\nYour gift qualifies you for the Arts Card, and you have chosen to gift it to ".strip_tags($artscard_name).". We will mail them the gifted card in the next few days.";
		}
		if($anon == 'yes'){
			$message.="\n\nYour gift is marked as anonymous.";
		}
		$message.="\n\nWith gratitude,\nYour Work for Art Team\n503-823-5849";
		$result = wp_mail(sanitize_email($donor_email),"[TEST] Thank you for your gift to Work for Art!", $message);
		return $result;
	}
	catch(Exception $e){
		error_log("mailingmethods.php error: " + $e->getException());
		return false;
	}
}

//use $donor_id to query database for e-mail details and then e-mail them
function racc_mailer2($donor_id,$success="no"){
	global $stripe_options;
	global $wpdb;

	try{
		$results = $wpdb->get_results(
			$wpdb->prepare('CALL sp_getemaildata(%s)',$donor_id));
		if ($results){
			$email = $results[0]->Email;
			$fund_total = $results[0]->PledgeTotal;
			$fund_community = $results[0]->Community;
			$fund_education = $results[0]->Education;
			$donor_first_name = $results[0]->first_name;
			$donorlastname = $results[0]->last_name;
			// $artscard_name = $results[0]->ArtsCardName;
			$donation_frequency = $results[0]->paytype;
			$anon = $results[0]->anon;
			$artscardqualify = $results[0]->ArtsCard;
			$giftartscard = $results[0]->GiftArtsCard;
			$period_total = $results[0]->PeriodTotal;
		}
		if ($giftartscard == 1){
			$results = $wpdb->get_results(
				$wpdb->prepare('CALL sp_getartscardaddress(%s)',$donor_id));
			if($results){
				$artscard_name = $results[0]->name;
			}
			// $artscard_name
		}
		$message = "Thank you, ".$donor_first_name.", for your pledge of $" . number_format($fund_total,2)."!\nCommunity Fund: $".number_format($fund_community,2)."\nArts Education Fund: $".number_format($fund_education,2);
		switch($donation_frequency){
			case "check":
				$message.="\n\nPlease mail your check to:\nWork for Art\n411 NW Park Avenue\nSuite 101"
					."\nPortland, OR 97209\n(payment due by June 2018)\n\nWe will mail to you an official acknowledgement and tax receipt in the next few days.";
				break;
			case "workplace":
				$message.="\n\nYour monthtly payroll deduction of $".number_format(floatval($period_total),2)
					." will begin July 2018. We will mail to you an official acknowledgement and tax receipt in the next few days.";
				break;
			case "cc-once":
				if($success == "yes"){
					$message.="\n\nYour payment of $". number_format($fund_total,2). " has been received, thank you!";
				}else{
					$message.="\n\nWe have recieved your pledge of $". number_format($fund_total,2). ", but there was a problem processing your payment, please try again, using the same payment form as before.";
				}
				break;
			case "cc-recur":
				if($success == "yes"){
					$message.="\n\nYour monthtly gift of $".number_format(floatval($period_total),2)
						." has begun. We will mail to you an official acknowledgement and tax receipt in the next few days.";
				}else{
					$message.="\n\nWe have recieved your annual pledge of $". number_format($fund_total,2). ", but there was a problem processing your first monthly payment of $".number_format(floatval($period_total),2). " please try again, using the same payment form as before.";
				}
				break;
		}
		
		if(($artscardqualify == 1)&&($giftartscard != 1)){	//$giftartscard can be null
			$message.="\n\nYour gift qualifies you for the Arts Card and will be mailed to you with your acknowledgement letter.";
		}elseif(($artscardqualify == 1)&&($giftartscard == 1)){
			$message.="\n\nYour gift qualifies you for the Arts Card, and you have chosen to gift it to ".$artscard_name.". We will mail them the gifted card in the next few days.";
		}
		if($anon == 'yes'){
			$message.="\n\nYour gift is marked as anonymous.";
		}
		$message.="\n\nWith gratitude,\nYour Work for Art Team\n503-823-5849";
		$result = wp_mail(sanitize_email($email),"[TEST] Thank you for your gift to Work for Art!", $message);
		return $result;
	}
	catch(Exception $e){
		error_log("mailingmethods.php error: " + $e->getException());
		return false;
	}
}
?>