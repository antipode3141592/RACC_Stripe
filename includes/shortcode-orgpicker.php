<?php

function racc_org_picker($atts, $content = null){
	ob_start();
	if(isset($_GET['org_picker'])){
	    $org_name = sanitize_text_field($_GET['org_picker']);
	    _e(do_shortcode('[payment_form_elementsjs organization=' .$org_name.']'));
	}
	return ob_get_clean();
}
add_shortcode('racc_org_picker', 'racc_org_picker');
?>
