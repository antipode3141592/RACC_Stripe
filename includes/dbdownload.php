<?php

function racc_db_download() {
	if(isset($_POST['action']) && $_POST['action'] == 'db_download') {
		global $wpdb;
		$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
		$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

		$results = $wpdb->get_results(
			$wpdb->prepare('CALL sp_getdonors(%s,%s)',$start_date. " 00:00:00", $end_date . " 23:59:59"));
		if ($results){
			$filename = 'racc_db';
		    $date = date("YmdHis");
		    $output = fopen('php://output', 'w');
		    header("Pragma: public");
		    header("Expires: 0");
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    header("Cache-Control: private", false);
		    header("Content-Type: text/csv; charset=utf-8");
		    header("Content-Disposition: attachment; filename=\"" . $filename . " " . $date . ".csv\";" );
		    header("Content-Transfer-Encoding: binary");
		    fputcsv( $output, array('timestamp', 'donor_id', 'first_name', 'last_name', 'anon', 'artscard', 'giftartscard', 'organization', 'org_page', 'email', 'phone', 'fund_amount', 'fund_name', 'pledgetotal', 'paytype', 'periodtotal','periods', 'address1', 'address2', 'city', 'state', 'zipcode', 'comment', 'browser', 'platform','Campaign', 'Appeal', 'Fund', 'Gift ID', 'Gift Type', 'Arts Card Expiry', 'Arts Card Description', 'Arts Card Name'));
		    foreach ($results as $value) {
		        $modified_values = array(
		                        $value->timestamp,
		                        $value->donor_id,	
		                        $value->first_name,	
		                        $value->last_name,
		                        $value->anon,	
		                        $value->artscard,	
		                        $value->giftartscard,	
		                        $value->organization,
		                        $value->org_page,	
		                        $value->email,
		                        $value->phone,
		                        $value->fund_amount,	
		                        $value->fund_name,	
		                        $value->pledgetotal,	
		                        $value->paytype,	
		                        $value->periodtotal,
		                        $value->period_count,
		                        $value->address1,	
		                        $value->address2,	
		                        $value->city,	
		                        $value->state,	
		                        $value->zipcode
		                        $value->comment,
		                        $value->browser,
		                        $value->platform,
		                        '',
		                        '',
		                        '',
		                        '',
		                        'pledge',
		                        '',
		                        '',
		                        '');
		        fputcsv( $output, $modified_values );
		    }
		    exit;
		}		
	}	
}
add_action('init', 'racc_db_download');

// function racc_output_
?>
