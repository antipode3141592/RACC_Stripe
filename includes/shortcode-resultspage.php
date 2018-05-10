<?php

//creates result page
function racc_stripe_resultpage($atts, $content = null){
	$success = isset($_GET['success']) ? $_GET['success'] : 'no';
	$error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';
	$donor_first_name = isset($_GET['donor_first_name']) ? sanitize_text_field($_GET['donor_first_name']) : null;
	$fund_community = isset($_GET['fund_community']) ? $_GET['fund_community'] : 0.0;
	$fund_education = isset($_GET['fund_education']) ? $_GET['fund_education'] : 0.0;
	$fund_total = isset($_GET['fund_total']) ? $_GET['fund_total'] : 0.0;
	$period_total = isset($_GET['period_total']) ? $_GET['period_total'] : 0.0;
	$donation_frequency = isset($_GET['donation_frequency']) ? $_GET['donation_frequency'] : null;
	$artscardqualify = isset($_GET['artscardqualify']) ? $_GET['artscardqualify'] : 'no';

	//donor information
	$donor_middle_name = isset($_GET['donor_middle_name']) ? sanitize_text_field($_GET['donor_middle_name']) : null;
	$donor_last_name = isset($_GET['donor_last_name']) ? sanitize_text_field($_GET['donor_last_name']) : null;
	$donor_email = isset($_GET['donor_email']) ? sanitize_email($_GET['donor_email']) : null;
	$donor_address_1 = isset($_GET['donor_address_1']) ? sanitize_text_field($_GET['donor_address_1']) : null;
	$donor_address_2 = isset($_GET['donor_address_2']) ? sanitize_text_field($_GET['donor_address_2']) : null;
	$donor_city = isset($_GET['donor_city']) ? sanitize_text_field($_GET['donor_city']) : null;
	$donor_state = isset($_GET['donor_state']) ? sanitize_text_field($_GET['donor_state']) : null;
	$donor_zip = isset($_GET['donor_zip']) ? sanitize_text_field($_GET['donor_zip']) : null;
	$anon = isset($_GET['anon']) ? $_GET['anon'] : 'no';	//if anon is checked, set to yes, else, no

	//arts card address (may be different from donor information)
	$artscard_name = isset($_GET['artscard_name']) ? sanitize_text_field($_GET['artscard_name']) : null;
	$artscard_address_1 = isset($_GET['artscard_address_1']) ? sanitize_text_field($_GET['artscard_address_1']) : null;
	$artscard_address_2 = isset($_GET['artscard_address_2']) ? sanitize_text_field($_GET['artscard_address_2']) : null;
	$artscard_city = isset($_GET['artscard_city']) ? sanitize_text_field($_GET['artscard_city']) : null;
	$artscard_state = isset($_GET['artscard_state']) ? sanitize_text_field($_GET['artscard_state']) : null;
	$artscard_zip = isset($_GET['artscard_zip']) ? sanitize_text_field($_GET['artscard_zip']) : null;
	$giftartscard = isset($_GET['giftartscard']) ? $_GET['giftartscard'] : 'no';
		
	ob_start();
	if($success == 'yes')
	{
		?>
		<h1>Thank You!</h1>
		<h2>Thank you for trying our test site! When the site opens for pledging on May 22, this will be a more detailed Thank You/Pledge Summary page that donors may print for their records.</h2>
		<div id="results_intro_paragraph">
			<p><?php _e(nl2br("Thank you, ".$donor_first_name.", for your pledge of $" . number_format($fund_total,2)
				."!\nArts Community Fund: $".number_format($fund_community,2)
				."\nArts Education Fund: $".number_format($fund_education,2))) ?>
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
			<?php if(($artscardqualify == "yes")&&($giftartscard == "no")){
				?>
				<p><?php _e(nl2br("Your gift qualifies you for the Arts Card and it will be mailed to you with your acknowledgement letter."));?></p>
				<?php
			}elseif(($artscardqualify == "yes")&&($giftartscard == "yes")){
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
				<p>Your gift is marked as anonymous: your name will not be used in any publications.</p><?php
			}
			?>
		</div>
		<div id="results_signature_lines">
			<br>
			<p>With gratitude,<br>
				Your Work for Art Team<br>
				503-823-2969</p>
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
		<a href="javascript:window.print()">Print!</a>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('racc_resultpage', 'racc_stripe_resultpage');
?>