<?php

function racc_org_name_shortcode($atts, $content = null){
	if(isset($_GET['org'])){
	    $org = $_GET['org'];
	}

	ob_start();
	// if($organization != 'None'){
?>
	<h1><?php _e(stripslashes($org)) ?></h1>
<?php
	// }
	return ob_get_clean();
}
add_shortcode('org_name','racc_org_name_shortcode');
?>