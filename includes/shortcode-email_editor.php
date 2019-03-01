<?php
//create email editor forms

function racc_stripe_email_editor_page($atts, $content = null){
	global $wpdb;

	$results = $wpdb->get_results(
		$wpdb->prepare("SELECT id, template_type, email_subject, email_body from racc_email_templates where template_type=%s", 'ack_recurring_monthly'));
	if ($results){
		foreach ($results as $result) {
			$db_id = $result->id;
			$db_template_type = $result->template_type;
			$db_email_subject = $result->email_subject;
			$db_email_body = $result->email_body;
		}
	}else{
		//no results 
	}

	ob_start();	//start output buffer

	?>
	<div>
		<form method="post" id="racc_email_editor_form" name="racc_email_editor_form">
			<h1>Email Templates</h1>
			<p>Type: <?php _e($db_template_type) ?></p>
			<div>
				<input type="text" id="email_subject_input" name="email_subject_input" class="text_input" value="<?php _e($db_email_subject); ?>"/>
				<label for="email_subject_input">Subject Line</label>
			</div>
			<div>
				<textarea id="email_body_input" name="email_body_input"><?php _e($db_email_body); ?></textarea>
				<label for="email_body_input">Body Text</label>
			</div>
			<input type="hidden" id="action" name="action" value="email_editor"/>
			<input type="hidden" id="template_type_input" name="template_type_input" value="<?php _e($db_template_type) ?>"/>
			<input type="hidden" id="id_input" name="id_input" value="<?php _e($db_id)?>"/>
			<input type="hidden" id="action_referrer" name="action_referrer" value="<?php _e(get_permalink()); ?>"/>
			<button type="submit">Submit</button>
		</form>
	</div>

	<?php
	return ob_get_clean();
}
add_shortcode('racc_email_editor', 'racc_stripe_email_editor_page');
