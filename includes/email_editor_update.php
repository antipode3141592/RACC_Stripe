<?php

function racc_stripe_email_editor_update(){
	if(isset($_POST['action']) && $_POST['action'] == 'email_editor'){
		global $wpdb;
		//grab POST data
		$redirect = isset($_POST['action_referrer']) ? $_POST['action_referrer'] : null;
		$input_subject = isset($_POST['email_subject_input']) ? ($_POST['email_subject_input']) : null;
		$input_body = isset($_POST['email_body_input']) ? ($_POST['email_body_input']) : null;
		$input_template_type = isset($_POST['template_type_input']) ? $_POST['template_type_input'] : null;
		$input_id = isset($_POST['id_input']) ? $_POST['id_input'] : null;
		$input_body_id = isset($_POST['body_id']) ? $_POST['body_id'] : null;

		if (($input_template_type != null) && ($input_id != null) && ($input_subject != null) && ($input_body != null)) {
			$rows_inserted = $wpdb->query($wpdb->prepare("CALL sp_updateemailbody(%s,%d)", $input_body, $input_body_id));
			$rows_inserted = $wpdb->query($wpdb->prepare("CALL sp_updateemailsubject(%s,%s,%d)", $input_subject, $input_template_type, $input_id));
		}

		// wp_safe_redirect(esc_url_raw($redirect)); 
		wp_safe_redirect(esc_url_raw(add_query_arg(array('template_type' => $input_template_type),$redirect)));
		exit;
	}
}
add_action('init', 'racc_stripe_email_editor_update');

function racc_stripe_email_template_selection(){
	if(isset($_POST['action']) && $_POST['action'] == 'email_template_selector'){
		$redirect = isset($_POST['action_referrer']) ? $_POST['action_referrer'] : null;
		$template_type = isset($_POST['racc_email_selector']) ? $_POST['racc_email_selector'] : null;
		wp_safe_redirect(esc_url_raw(add_query_arg(array('template_type' => $template_type),$redirect)));
		exit;
	}
}
add_action('init', 'racc_stripe_email_template_selection');


function racc_stripe_email_preview_selection(){
	if(isset($_POST['action']) && $_POST['action'] == 'email_template_preview'){
		$redirect = isset($_POST['action_referrer']) ? $_POST['action_referrer'] : null;
		$template_type = isset($_POST['template_type']) ? $_POST['template_type'] : null;
		$test_data = isset($_POST['example_data_selector']) ? $_POST['example_data_selector'] : null;
		// example_data_selector

		

		wp_safe_redirect(esc_url_raw(add_query_arg(array('template_type' => $template_type),$redirect)));
		exit;
	}
}
add_action('init', 'racc_stripe_email_preview_selection');
?>