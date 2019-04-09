<?php
//mail function that populates tokens in a text stream (search and replace all {{[token.name]}} with [token.value])
function racc_mailer_2($donor_id,$success="no", $donation_frequency){
	$results = get_donor_info($donor_id);
	$message = $results[0];
	$subject = $results[1];
	$donor_email = $results[2];
	$headers[] = 'From: artsimpactfund@racc.org';
	$altaddresses = 'whovey@racc.org, isterry@racc.org, skirkpatrick@racc.org';
	// $headers[] = 'From: testing@wfadev.pairsite.com';
	$headers[] = 'Content-type: text/html';
	if ($success == 'no'){
		//for errors, email us
		$result = wp_mail($altaddresses, $subject, $message, $headers);
	} else {
		//for all other errors, email the donor and us
		$result = wp_mail(sanitize_email($donor_email),$subject, $message, $headers);	
		$result = wp_mail($altaddresses, $subject, $message, $headers);	
	}
	return $result;
}

function racc_email_piping($_message, $_subject, $_pipe_vars){
	// error_log("beginning of racc_email_piping()");
	$booleans = array(
		array("==", "equ"),
		array("!=", "notequ"),
		array(">=", "greequ"),
		array("<=", "lessequ"),
		array("<", "less"),
		array(">", "gre")
	);

	$results = racc_email_piping_if($_pipe_vars, $booleans, $_message, $_subject);
	// error_log("after call to racc_email_piping_if()");
	$message = $results[0];
	$subject = $results[1];

	//replace the piping variables in both the $message and $subject
	foreach ($_pipe_vars as $pv) {
		$message = str_replace($pv[0], $pv[1], $message);	
		$subject = str_replace($pv[0], $pv[1], $subject);
	}
	// error_log("end of racc_email_piping()");
	return array($message, $subject);
}

function racc_email_piping_if($pipe_vars, $booleans, $message, $subject){
	$ifstrings = array("{{if}}","{{/if}}");
	// error_log("beginning of racc_email_piping_if()");

	$pos_start = strpos($message, $ifstrings[0]);
	$pos_end = strpos($message, $ifstrings[1], $pos_start);

	if ($pos_start === false) {
		//if the {{if}} tag is not found, return
		error_log("if string not found! exiting racc_email_piping_if() function");
		return array($message,$subject);
	} else {
		if ($pos_end !== false){
			$fullstring = substr($message, $pos_start, abs($pos_end + strlen($ifstrings[1])-$pos_start));
			$_pos_start = strpos($fullstring, "[");
			$_pos_end = strpos($fullstring, "]");
			$controlstr = substr($fullstring, $_pos_start + 1, abs($_pos_end - 1 - $_pos_start));
			$conditionalstr = substr($fullstring, $_pos_end + 1,strlen($fullstring) - 1 - strlen($ifstrings[1]) - $_pos_end);
			$bool_type = null;
			//get comparitor info
			foreach ($booleans as $_bool) {
				$pos_start_bool = strpos($controlstr, $_bool[0]);
				if ($pos_start_bool !== false){
					$bool_type = $_bool[1];
					$arg_left = substr($controlstr, 0, $pos_start_bool);
					$arg_right = substr($controlstr, $pos_start_bool + strlen($_bool[0]), strlen($controlstr));
					break;	//stop foreach after positive result is found
				}
			}

			//--------------------------------------------
			//condition those arguments:
			$_arg_left = $arg_left;
			//replace piping variables in lefthand argument
			foreach ($pipe_vars as $pv) {
				$_arg_left = str_replace($pv[0], $pv[1], $_arg_left);
			}
			$_arg_right = null;
			$numeric = null;

			if (is_numeric($_arg_left) && is_numeric($_arg_right)){
				//both arguments are numeric
				$numeric = true;
			}elseif (!is_numeric($_arg_left) && !is_numeric($_arg_right)){
				$numeric = false;
			}else{
				error_log("arguments are mixed, skipping evaluation");
			}
			if($numeric){
				$_arg_right = $arg_right;	
			} else {
				$_arg_right = str_replace("\"", "", $arg_right);	
			}
			//--------------------------------------------

			if (isset($bool_type)){
				switch($bool_type){
					case $booleans[0][1]:
						
						if ($_arg_left == $_arg_right){
							$message = str_replace($fullstring, $conditionalstr, $message);
						} else {
							// error_log("equality operator didn't find equality");
							$message = str_replace($fullstring, "", $message);
						}
						break;
					case $booleans[1][1]:
						if ($_arg_left != $_arg_right){
							$message = str_replace($fullstring, $conditionalstr, $message);
						} else {
							// error_log("inequality operator didn't find inequality");
							$message = str_replace($fullstring, "", $message);
						}
						break;
					case $booleans[2][1]:
						if (floatval($_arg_left) >= floatval($_arg_right)){
							$message = str_replace($fullstring, $conditionalstr, $message);
						} else {
							// error_log("right < left");
							$message = str_replace($fullstring, "", $message);
						}
						break;
					case $booleans[3][1]:
						if (floatval($_arg_left) <= floatval($_arg_right)){
							$message = str_replace($fullstring, $conditionalstr, $message);
						} else {
							// error_log("left > right");
							$message = str_replace($fullstring, "", $message);
						}
						break;
					case $booleans[4][1]:
						if (floatval($_arg_left) < floatval($_arg_right)){
							$message = str_replace($fullstring, $conditionalstr, $message);
						} else {
							// error_log("right <= left");
							$message = str_replace($fullstring, "", $message);
						}
						break;
					case $booleans[5][1]:
						if (floatval($_arg_left) > floatval($_arg_right)){
							$message = str_replace($fullstring, $conditionalstr, $message);
						} else {
							// error_log("right >= left");
							$message = str_replace($fullstring, "", $message);
						}
						break;
					default:
						error_log("no known boolean type");
				}
			}
		} else {
			error_log("missing end if {{/if}} placeholder, skipping");
			return array($message, $subject);
		}
		//recurse it!
		return racc_email_piping_if($pipe_vars, $booleans, $message, $subject);
	}
}

function get_donor_info($donor_id){
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
		error_log("Sending E-Mail to ". $donor_email);
		$artscard_qual_and_gift = null;
		$artscard_qual_no_gift = null;
		if ($artscardqualify && $giftartscard) {
			$artscard_qual_and_gift = true;
			$artscard_qual_no_gift = false;
		} elseif ($artscardqualify && !($giftartscard)){
			$artscard_qual_and_gift = false;
			$artscard_qual_no_gift = true;
		} elseif (!($artscardqualify) && !($giftartscard)){
			$artscard_qual_and_gift = false;
			$artscard_qual_no_gift = false;
		}

		$template_subject ='';
		$template_body = '';

		$results = $wpdb->get_results(
			$wpdb->prepare("CALL sp_getemailtemplate(%s)",$paytype));
		if($results){
			foreach ($results as $result) {
				$template_subject = stripcslashes($result->email_subject);
				$template_body = stripcslashes($result->email_body);
			}
		}
		$pipe_vars = array(
			array("{{artscard_qual_and_gift}}", $artscard_qual_and_gift),
			array("{{artscard_qual_no_gift}}", $artscard_qual_no_gift),
			array("{{first_name}}", $donor_first_name),
			array("{{last_name}}", $donor_last_name),
			array("{{timestamp_submit}}", $timestamp),
			array("{{anon}}", $anon),
			array("{{arts_card_qualified}}", $artscardqualify),
		    array("{{gift_arts_card}}", $giftartscard),
		    array("{{organization}}", $organization),
		    array("{{donor_email}}", $donor_email),
		    array("{{amount_fund_community}}", number_format($fund_community,2)),
		    array("{{amount_fund_education}}", number_format($fund_education,2)),
		    array("{{amount_fund_designated}}",number_format($fund_designated,2)),
		    array("{{designated_fund_name}}", $fund_designated_name),
		    array("{{total}}", number_format($fund_total,2)),
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
		//initialize message and subject to defaults in database
		// error_log("email body, pre piping: " . $template_body);
		$results = racc_email_piping($template_body, $template_subject, $pipe_vars);
		// $message = $results[0];
		// $subject = $results[1];
		return array($results[0], $results[1], $donor_email);
	}
	catch(Exception $e){
		error_log("mailingmethods.php error: " + $e->getException());
		return false;
	}
}
?>