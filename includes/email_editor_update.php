<?php

function racc_stripe_email_editor_update(){
	if(isset($_POST['action']) && $_POST['action'] == 'email_editor'){
		global $wpdb;
		error_log("post action received");
		$input_subject = isset($_POST['email_subject_input']) ? $_POST['email_subject_input'] : null;
		$input_body = isset($_POST['email_body_input']) ? $_POST['email_body_input'] : null;
		$input_template_type = isset($_POST['template_type_input']) ? $_POST['template_type_input'] : null;
		$input_id = isset($_POST['id_input']) ? $_POST['id_input'] : null;

		if (($input_template_type != null) && ($input_id != null)) {
			$rows_inserted = $wpdb->query($wpdb->prepare("UPDATE racc_email_templates SET email_subject=%s, email_body=%s WHERE template_type=%s AND id=%s",$input_subject, $input_body, $input_template_type, $input_id));
			error_log("the code here was.... executed.");
		}
		exit;
	}
}

add_action('init', 'racc_stripe_email_editor_update',1,0);

?>