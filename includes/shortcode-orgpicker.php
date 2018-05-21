<?php

function racc_org_picker($atts, $content = null){

	if(isset($_GET['org'])){
	    $org_name = $_GET['org'];
	    $pay_periods = isset($_GET['pp']) ? $_GET['pp'] : null;
	    $optional_periods = isset($_GET['op']) ? $_GET['op'] : null;
	    $payroll = isset($_GET['pr']) ? $_GET['pr'] : null;
	    $dg = isset($_GET['dg']) ? $_GET['dg'] : null;

	    //build shortcode
	    $shortcode = '[payment_form_elementsjs';
	    if($org_name){
	    	$shortcode .= ' organization="' . $org_name . '"';
	    }
	    if($pay_periods){
	    	$shortcode .= ' payperiods="' . $pay_periods. '"';
	    }
	    if($optional_periods){
	    	$shortcode .= ' optionalperiods="' . $optionalperiods. '"';
	    }
	    if($payroll){
	    	$shortcode .= ' payroll="' . $payroll. '"';
	    }
	    if($dg){
	    	$shortcode .= ' dg="' . $dg. '"';
	    }
	    $shortcode .= ']';

	    // error_log($shortcode);
	    ob_start();
	    _e(do_shortcode($shortcode));
	}
	return ob_get_clean();
}
add_shortcode('racc_org_picker', 'racc_org_picker');
?>
