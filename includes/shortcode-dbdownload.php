<?php

function racc_download_db_form($atts, $content = null){
	ob_start();
	?>
	<form id="db_download_form" method="POST">
		<div>
			<div>
				<label for="start_date">Start Date</label>
				<input id="start_date" name="start_date" type="date"/>
			</div>
			<div>
				<label for="end_date">End Date</label>
				<input id="end_date" name="end_date" type="date"/>
			</div>
		</div>
		<div>
			<input type="hidden" name="action" value="db_download"/>
			<button type="submit" id="db_download_submit">Download</button>
		</div>
	</form>
<?php
	return ob_get_clean();
}
add_shortcode('db_download_form','racc_download_db_form');
?>
