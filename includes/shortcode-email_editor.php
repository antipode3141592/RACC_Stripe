<?php
//create email editor forms

function racc_stripe_email_editor_page($atts, $content = null){
	global $wpdb;

	$template_type = isset($_GET['template_type']) ? $_GET['template_type'] : "ack_cc_once";

	$results = $wpdb->get_results(
		$wpdb->prepare("CALL sp_getemailtemplates(%s)", $template_type));
	if ($results){
		foreach ($results as $result) {
			$db_id = $result->id;
			$db_template_type = $result->template_type;
			$db_email_subject = $result->email_subject;
			$db_email_body = $result->email_body;
			$db_response_type = $result->response_type;
			$db_email_body_id = $result->email_body_id;
		}
	}else{
		//no results 
	}
	
	ob_start();	//start output buffer

	?>
	<div id='emaileditorforms'>
		<form method="post" id="racc_email_template_selector_form" name="racc_email_template_selector_form">
			<h1>Email Template Selector</h1>
			<label for="racc_email_selector">Template</label>
			<select id="racc_email_selector" name="racc_email_selector">
				
				<?php
					$template_types = $wpdb->get_results("SELECT DISTINCT id, template_type from racc_email_templates");
					foreach($template_types as $template){

						_e('<option value="' .$template->template_type . '"');
						if ($template->template_type == $template_type){
							_e(' selected="selected" ');
						}
						_e('>'.$template->template_type.'</option>');
					}
				?>
			</select>
			<input type="hidden" name="action" value="email_template_selector"/>
			<input type="hidden" name="action_referrer" value="<?php _e(get_permalink()); ?>"/>
			<input type="submit" value="Select"/>
		</form>

		<form method="post" id="racc_email_editor_form" name="racc_email_editor_form">
			<h1>Email Template Editor</h1>
			<p>Type: <?php _e($db_template_type) ?></p>
			<p>Response Type: <?php _e($db_response_type) ?></p>
			<div>
				<label for="email_subject_input">Subject Line</label>
				<input type="text" id="email_subject_input" name="email_subject_input" class="text_input" value="<?php _e(stripcslashes($db_email_subject)); ?>"/>
				
			</div>
			<div>
				<label for="email_body_input">Body Text</label>
				<textarea id="email_body_input" name="email_body_input"><?php _e(stripcslashes($db_email_body)); ?></textarea>
			</div>
			<input type="hidden" id="action" name="action" value="email_editor"/>
			<input type="hidden" id="template_type_input" name="template_type_input" value="<?php _e($db_template_type) ?>"/>
			<input type="hidden" id="body_id" name="body_id" value="<?php _e($db_email_body_id) ?>"/>
			<input type="hidden" id="id_input" name="id_input" value="<?php _e($db_id)?>"/>
			<input type="hidden" id="action_referrer" name="action_referrer" value="<?php _e(get_permalink()); ?>"/>
			<button type="button" id="emailpreviewbutton">Preview</button>
			<button type="submit">Submit Edit</button>
		</form>


	</div>

	<?php
	return ob_get_clean();
}
add_shortcode('racc_email_editor', 'racc_stripe_email_editor_page');

function racc_stripe_email_preview($atts, $content = null){
	global $wpdb;
	$template_type = isset($_GET['template_type']) ? $_GET['template_type'] : "ack_cc_once";
	$results = $wpdb->get_results(
		$wpdb->prepare("CALL sp_getemailtemplates(%s)", $template_type));
	if ($results){
		foreach ($results as $result) {
			$db_id = $result->id;
			$db_template_type = $result->template_type;
			$db_email_subject = $result->email_subject;
			$db_email_body = $result->email_body;
			$db_response_type = $result->response_type;
			$db_email_body_id = $result->email_body_id;
		}
	}else{
		//no results 
	}
	
	//[example id, anon, arts card not gift, arts card gifted]
	$exampledata = array(
		array(1, "yes", 0, 1),
		array(2, "yes", 1, 0),
		array(3, "yes", 0, 0),
		array(4, "no", 0, 1),
		array(5, "no", 1, 0),
		array(6, "no", 0, 0)
	);

	ob_start();	//start output buffer

	?>
	<div id='email_template_preview_div'>
		<form method="post" id="racc_email_preview_selector" name="racc_email_preview_selector">
			<h1>Case selector</h1>
			<label for="example_data_selector">Sample Set</label>
			<select id="example_data_selector" name="example_data_selector" autocomplete="off">
				<option value="1">Anon, Arts Card (gifted)</option>
				<option value="2">Anon, Arts Card (not gifted)</option>
				<option value="3">Anon, No Arts Card</option>
				<option value="4">Non Anon, Arts Card (gifted)</option>
				<option value="5">Non Anon, Arts Card (not gifted)</option>
				<option value="6">Non Anon, No Arts Card</option>
			</select>
			<input type="hidden" name="template_type" value="<?php _e($template_type); ?>" /> 
			<input type="hidden" name="action" value="email_template_preview"/>
			<input type="hidden" name="action_referrer" value="<?php _e(get_permalink()); ?>"/>
			<input type="submit" value="Select"/>
		</form>
		<div>
			<label for="email_subject">Subject</label>
			<textarea id="email_subject" name="email_subject"></textarea>
			<label for="email_body_input">Body</label>
			<textarea id="email_body" name="email_body"></textarea>
		</div>
	</div>

	<?php
	return ob_get_clean();
}
add_shortcode('racc_email_preview', 'racc_stripe_email_preview');