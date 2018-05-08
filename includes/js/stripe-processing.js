//shows confirmation div, populates values for user review
jQuery(document).ready(function($){
	$('#confirmation_button').click(function(event)
	{	
		$('#stripe-submit').attr("disabled", false);
		$('#confirmation_button').attr("disabled", "disabled");	//disable button to prevent all the clicks	
		$('#confirmation_popup').show();
		$('#confirm_name').html($('#donor_first_name').val() + ' ' + $('#donor_last_name').val());
		$('#confirm_donor_address').html($('#donor_address_1').val() + ' ' + $('#donor_address_2').val() + '<br>' 
			+ $('#donor_city').val() + ', ' + $('#donor_state').val() + ' ' + $('#donor_zip').val() + '<br>');
		if($('#artscardqualify').val() == 'yes'){
			$('#confirm_artscard').html("<p><b>You qualify for the Arts Card!</b></p>");
		}else{
			if (parseFloat($('#fund_total').val()) >= 50.0){
				$('#confirm_artscard').html("<p><b>You could qualify for an arts card by contributing just $" 
					+ (60.0 - parseFloat($('#fund_total').val())).toFixed(2) + " more!</b></p>");
			}else{
				$('#confirm_artscard').html("<p><b>Tip: If you pledge $60 or more, we'll send you the Arts Card.</b></p>");
			}
		}
		if ($('#giftartscard').prop("checked"))
		{
			$('#confirm_artscard_address').html($('#artscard_name').val() + '<br>'
				+ $('#artscard_address_1').val() + ' ' + $('#artscard_address_2').val() + '<br>' 
				+ $('#artscard_city').val() + ', ' + $('#artscard_state').val() + ' ' + $('#artscard_zip').val() + '<br>');
		}else{
			$('#confirm_artscard_address').hide();
		}
		$('#confirm_email').html($('#donor_email').val());
		$('#confirm_fund_community').html('Arts Community Fund: $'+ parseFloat($('#fund_community').val()).toFixed(2));
		$('#confirm_fund_education').html('Arts Education Fund: $'+ parseFloat($('#fund_education').val()).toFixed(2));
		$('#confirm_fund_total').html('Annual Pledge: $'+ parseFloat($('#fund_total').val()).toFixed(2));
		switch($('input[name="donation_frequency"]:checked').val())
		{
			case "cc-once":
				$('#confirm_payroll_deduction').hide();
				$('#confirm_paymethod').html('Giving Method: One-time payment by Credit/Debit Card.');
				break;
			case "cc-recur":
				$('#confirm_payroll_deduction').hide();
				$('#confirm_paymethod').html('Giving Method: Recurring monthly payment of $'+ parseFloat($('#period_total').val()).toFixed(2) +' by Credit/Debit Card.');
				break;
			case "workplace":
				$('#confirm_payroll_deduction').show();
				$('#confirm_payroll_authorization').prop('checked',false);	//decheck box
				$('#stripe-submit').attr("disabled", "disabled");	//disable sumbission for final acknowledgement
				$('#confirm_paymethod').html('Giving Method: Payroll deduction will begin July 2018.');
				break;
			case "check":
				$('#confirm_payroll_deduction').hide();
				$('#confirm_paymethod').html('Giving Method: Please mail your check to Work for Art by June 2018.');
				break;
		}
		return false;
	});
});

jQuery(document).ready(function($){
	$('#confirm_payroll_authorization').click(function(event){
		if ($('#confirm_payroll_authorization').prop("checked"))
		{
			$('#stripe-submit').attr("disabled",false);
		}else{
			$('#stripe-submit').attr("disabled","disabled");
		}
	})
})

//'back' button for confirmation popup.  hides div on click
jQuery(document).ready(function($){
	$('#confirmation_reset').click(function(event)
	{
		$('#confirmation_button').attr("disabled", false);	//re-enable confirmation button
		$('#confirmation_popup').hide();
		return false;
	});
});

//show/hide address input div for gifting arts card (show when checked)
jQuery(document).ready(function($){
	$('#giftartscard').change(function(event)
	{
		// console.log('clicked the giftartscard box');
		if($('#giftartscard').prop("checked")){
			// console.log('show gift address div');
			$('#artscard_address_input').show();
			$('#artscard_name').attr('required','required');
			$('#artscard_email').attr('required','required');
			$('#artscard_address_1').attr('required','required');
			$('#artscard_city').attr('required','required');
			$('#artscard_state').attr('required','required');
			$('#artscard_zip').attr('required','required');
		}else
		{
			// console.log('hide gift address div');
			$('#artscard_address_input').hide();
			$('#artscard_name').attr('required',false);
			$('#artscard_email').attr('required',false);
			$('#artscard_address_1').attr('required',false);
			$('#artscard_city').attr('required',false);
			$('#artscard_state').attr('required',false);
			$('#artscard_zip').attr('required',false);
		}
		return false;
	});
});

//show/hide payroll deduction fields on pageshow (just after on 'load')
jQuery(document).ready(function($){
	$(window).on("pageshow",function(){
		
		if($('#sc_payroll').val() == 'no'){
			$('#workplace_div').hide();
			jQuery('#donationradio3').click();
		}else{
				
			$('#workplace_div').show();
			jQuery('#donationradio1').click();
		}
	});
});

//toggle visibility on infopopup class objects
jQuery(document).ready(function($){
	$('.infopopup').click(function(e){
		jQuery(this).children('.popupcontent').toggleClass('show');
		e.stopPropagation();	//stop the click event from bubbling to the page event
	});
});

jQuery(document).ready(function($){
	$('html').click(function(e){
		$('.infopopup').children('.popupcontent').removeClass('show');
	});
});

//javascript updates visibility of input elements based on selected payment type (donationradio)
function change_frequency(donationradio){
	var optionalperiods = document.getElementById("sc_optionalperiods");
	var maxperiods = document.getElementById("sc_payperiods");
	var payperiodinputs =  document.getElementById("payperiodinputs");
	var ccpaymentcontainer = document.getElementById("cc-payment-container");	//div that contains cc data entry
	var periodlabel = document.getElementById("periodinput_label");
	var periortotallabel = document.getElementById("period_total_label");
	var payperiod_container = document.getElementById("payperiod_container");	//div that contains payperiod data
	switch(donationradio.value)
	{
		case "workplace":
			payperiodinputs.style.display = "block";
			ccpaymentcontainer.style.display = "none";
			periodlabel.style.display = "block";
			payperiod_container.style.display = "block";
			periortotallabel.innerHTML = "Per Period Amount";
			if (optionalperiods.value == 'yes'){
				periodlabel.innerHTML = "Pay Periods (max " + parseInt(maxperiods.value,10) + ")";
				payperiodinputs.removeAttribute('readonly');
				payperiodinputs.setAttribute('max',parseInt(maxperiods.value,10));
				payperiodinputs.value = parseInt(maxperiods.value,10);
			}else{
				periodlabel.innerHTML = "Pay Periods";
				payperiodinputs.value = parseInt(maxperiods.value,10);
				payperiodinputs.setAttribute('readonly','readonly');
			}
			break;
    	case "cc-recur":
			payperiodinputs.style.display = "none";
			ccpaymentcontainer.style.display = "flex";
			periodlabel.style.display = "none";
			payperiod_container.style.display = "block";
    		payperiodinputs.removeAttribute('readonly');
    		payperiodinputs.removeAttribute('max');
    		payperiodinputs.value = '12';
    		periortotallabel.innerHTML = "Monthly Amount";
			payperiodinputs.setAttribute('readonly','readonly');
    		break;
		case "check":
			ccpaymentcontainer.style.display = "none";
			payperiod_container.style.display = "none";
			payperiodinputs.value = '1';
			payperiodinputs.setAttribute('readonly','readonly');
			break;
		case "cc-once":
			ccpaymentcontainer.style.display = "flex";
			payperiod_container.style.display = "none";
			payperiodinputs.value = '1';
			payperiodinputs.setAttribute('readonly','readonly');
			break;
		default:
			console.log("change_frequency() error, default case!");
	}
	breakdown_total();
}

//breakdown total 
function breakdown_total(){
	var artscard = document.getElementById("artscardvalidation");
	var fund_total = document.getElementById("fund_total");
	var temp_total = fund_total.value;
	var fund_community = document.getElementById("fund_community");
	var fund_education = document.getElementById("fund_education");
	var period_total = document.getElementById("period_total");
	var periods =  document.getElementById("payperiodinputs").value;
	var temp_periods = periods;
	var artscardqualify = document.getElementById("artscardqualify");	
	try{
		var regex = /[0-9]|\./;
  
		if (!regex.test(temp_total)){
			// console.log(temp_total);
			temp_total = 1.0;
		} else if (temp_total < 1.0){
			temp_total = 1.0;
		}
		if (!regex.test(temp_periods)){
			console.log(temp_periods);
			temp_periods = 1;
		} else if (temp_periods < 1) {
			temp_periods = 1
		}
		periods = temp_periods;
		var tot = parseFloat(temp_total)/parseFloat(temp_periods);
		period_total.value = tot.toFixed(2);
		
		//preserve ratio of funds
		var ratio = parseFloat(parseFloat(fund_community.value)/parseFloat(parseFloat(fund_community.value) + parseFloat(fund_education.value)));	//% community, do not round
		console.log("ratio: " + ratio.toFixed(2));
		fund_community.value = parseFloat(parseFloat(temp_total) * parseFloat(ratio)).toFixed(2);
		fund_education.value = parseFloat(parseFloat(temp_total) * (1.00 - parseFloat(ratio))).toFixed(2);
		fund_total.value = parseFloat(temp_total).toFixed(2);	//format decimal points on input

		fund_community.setAttribute('max',parseFloat(temp_total).toFixed(2));
		fund_education.setAttribute('max',parseFloat(temp_total).toFixed(2));
		// fund_community.value = parseFloat(fund_total.value).toFixed(2);
		// fund_education.value = 0.00;
		if(temp_total >= 60.0)
		{
			jQuery('#artscardvalidation').show();
			// artscard.style.display = "block";
			artscardqualify.setAttribute('value','yes');
		}else{
			jQuery('#artscardvalidation').hide();
			// artscard.style.display = "none";
			artscardqualify.setAttribute('value','no');
		}
		// change_fund();
	}catch(err)
	{
		console.log("breakdown_total(): " + err.message);
	}
}

jQuery(document).ready(function($){
	$('#fund_community').change(function(){
		var temp = 0.0;
		var regex = /[0-9]|\./;
  
		if (!regex.test($(this).val())){
			temp = 0.0;
		} else if (parseFloat($(this).val()) > parseFloat($(this).attr('max'))){
			temp = parseFloat($(this).attr('max'));
		} else if (parseFloat($(this).val()) < 0) {
			temp = 0.0
		} else {
			temp = $(this).val();
		}
		$('#fund_education').val(($('#fund_total').val() - temp).toFixed(2));
		$('#fund_community').val(parseFloat(temp).toFixed(2));
	});
});

jQuery(document).ready(function($){
	$('#fund_education').change(function(){
		var temp = 0.0;
		var regex = /[0-9]|\./;
  
		if (!regex.test($(this).val())){
			temp = 0.0;
		} else if (parseFloat($(this).val()) > parseFloat($(this).attr('max'))){
			temp = parseFloat($(this).attr('max'));
		} else if (parseFloat($(this).val()) < 0.0) {
			temp = 0.0
		} else {
			temp = $(this).val();
		}
		$('#fund_community').val(($('#fund_total').val() - temp).toFixed(2));
		$('#fund_education').val(parseFloat(temp).toFixed(2));
	});
});