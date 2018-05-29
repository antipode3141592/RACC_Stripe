<?php

function racc_org_selector($atts, $content = null){
	ob_start();
	?>
	<form id="form-selector" method="POST" name="org-selector">
		<select id="orginput" name="orginput" autocomplete="off">
		</select>
		<input type="submit" value="Select" />
	</form>
<?php
	return ob_get_clean();
}
add_shortcode('org_selector','racc_org_selector');
?>