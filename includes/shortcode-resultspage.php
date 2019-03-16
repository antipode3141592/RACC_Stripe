<?php
//creates result page
function racc_stripe_resultpage($atts, $content = null){
	global $wpdb;
	$success = isset($_GET['success']) ? sanitize_text_field($_GET['success']) : 'no';
	$error_message = isset($_GET['error_message']) ? sanitize_text_field($_GET['error_message']) : '';
	$donor_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';

	ob_start();
	if(($donor_id) && ($success == 'yes')){
		$results = get_donor_info($donor_id);
		if ((isset($results)) && ($results != false)){
			$message = $results[0];
		} else
		{
			$message = '<p>no message!?!?!?!?!</p>';
		}
		?>
		<div id="resultscontent">
		<?php _e($message); ?>	
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
			<div id="errormesage"><p>
			<?php _e($error_message); ?>
			</p></div>
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