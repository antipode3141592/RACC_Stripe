<?php

function racc_stripe_email_editor_update(){
	if(isset($_POST['action']) && $_POST['action'] == 'email_editor'){
		global $wpdb;
		// $redirect =get_site_url(null,'/email-editor/');
		$redirect = isset($_POST['action_referrer']) ? $_POST['action_referrer'] : null;

		// error_log("post action received");
		$input_subject = isset($_POST['email_subject_input']) ? $_POST['email_subject_input'] : null;
		$input_body = isset($_POST['email_body_input']) ? $_POST['email_body_input'] : null;
		$input_template_type = isset($_POST['template_type_input']) ? $_POST['template_type_input'] : null;
		$input_id = isset($_POST['id_input']) ? $_POST['id_input'] : null;

		error_log("email_editor_update:  " + $input_subject + ", " + $input_body);


		if (($input_template_type != null) && ($input_id != null) && ($input_subject != null) && ($input_body != null)) {
			$rows_inserted = $wpdb->query($wpdb->prepare("UPDATE racc_email_templates SET email_subject=%s, email_body=%s WHERE template_type=%s AND id=%s",$input_subject, $input_body, $input_template_type, $input_id));
			error_log("the code here was.... executed.");
		}

		
		wp_safe_redirect(esc_url_raw($redirect)); 
		exit;
	}
}

add_action('init', 'racc_stripe_email_editor_update');

?>