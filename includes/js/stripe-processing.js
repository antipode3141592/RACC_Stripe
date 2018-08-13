// validate required input fields exist
// shows confirmation div, populates values for user review  to make any changes to the text, edit the appropriate inline
// TODO:  make this much easier to alter.  perhaps create an entry form in the admin side for the confirmation popup, results page, and emails
//		so that text changes don't require code alteration!
jQuery(document).ready(function($){
	$('#confirmation_button').click(function(event)
	{	
		$('#stripe-submit').attr("disabled", false);	
		$('#confirmation_button').attr("disabled", "disabled");	//disable button to prevent all the clicks	
		var isValid = true;
		$('input:required').each(function(){
			$(this).removeClass('invalid');
			if ($(this).val() === ''){
				isValid = false;
				$(this).addClass('invalid');
			}
		});
		if (isValid){
			if (parseFloat($('#fund_total').val()) >= 1.0){
				$('.payment-error').html('');
				$('#confirmation_popup').show();
				$('#confirm_name').html($('#donor_first_name').val() + ' ' + $('#donor_last_name').val());
				$('#confirm_email').html($('#donor_email').val());
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
					$('#confirm_artscard_address').html('<hr>Your Arts Card shall be gifted to: <br>' + $('#artscard_name').val() + '<br>'
						+ $('#artscard_address_1').val() + ' ' + $('#artscard_address_2').val() + '<br>' 
						+ $('#artscard_city').val() + ', ' + $('#artscard_state').val() + ' ' + $('#artscard_zip').val() + '<br>');
				}else{
					$('#confirm_artscard_address').hide();
				}
				$('#confirm_fund_community').html('Arts Community Fund: $'+ parseFloat($('#fund_community').val()).toFixed(2));
				// $('#confirm_fund_education').html('Arts Education Fund: $'+ parseFloat($('#fund_education').val()).toFixed(2));
				if ($('#sc_dg').val() == 'yes'){
					$('#confirm_fund_designated').html('Designated Fund (' + $('#fund_designated_name').val() + '): $'+ parseFloat($('#fund_designated').val()).toFixed(2));
				}
				$('#confirm_fund_total').html('Total Pledge: $'+ parseFloat($('#fund_total').val()).toFixed(2));
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
					case "cc-annual":
						$('#confirm_payroll_deduction').hide();
						$('#confirm_paymethod').html('Giving Method: Recurring annual payment of $'+ parseFloat($('#fund_total').val()).toFixed(2) +' by Credit/Debit Card.');
						break;
					case "workplace":
						$('#confirm_payroll_deduction').show();
						$('#confirm_payroll_authorization').prop('checked',false);	//decheck box
						$('#stripe-submit').attr("disabled", "disabled");	//disable sumbission for final acknowledgement
						$('#confirm_paymethod').html('Giving Method: Payroll Deduction');
						break;
					case "check":
						$('#confirm_payroll_deduction').hide();
						$('#confirm_paymethod').html('Giving Method: <b>Please mail your check to Work for Art at your earliest convenience</b>.');
						break;
				}
			} else {
				$('.payment-error').html('Please pledge at least $1.00!');
				$('#confirmation_button').attr("disabled", false);	
			}
		} else{
			$('.payment-error').html('Please fill out all required fields!');
			$('#confirmation_button').attr("disabled", false);
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
	});
});

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
	$('#giftartscard').change(function(){
		artscardhide();
	});
});

function artscardhide(){
	if(jQuery('#giftartscard').prop("checked")){
			// console.log('show gift address div');
			jQuery('#artscard_address_input').show();
			jQuery('#artscard_name').attr('required','required');
			jQuery('#artscard_email').attr('required','required');
			jQuery('#artscard_address_1').attr('required','required');
			jQuery('#artscard_city').attr('required','required');
			jQuery('#artscard_state').attr('required','required');
			jQuery('#artscard_zip').attr('required','required');
		}else
		{
			// console.log('hide gift address div');
			jQuery('#artscard_address_input').hide();
			jQuery('#artscard_name').attr('required',false);
			jQuery('#artscard_email').attr('required',false);
			jQuery('#artscard_address_1').attr('required',false);
			jQuery('#artscard_city').attr('required',false);
			jQuery('#artscard_state').attr('required',false);
			jQuery('#artscard_zip').attr('required',false);
		}
		return false;
}

//show/hide payroll deduction fields on pageshow (just after on 'load')
jQuery(document).ready(function($){
	$(window).on("pageshow",function(){		
		//payroll options
		if($('#sc_payroll').val() == 'no'){
			$('#workplace_div').hide();
			jQuery('#donationradio3').click();
		}else{		
			$('#workplace_div').show();
			jQuery('#donationradio1').click();
		}
		// designated giving check
		if($('#sc_dg').val() == 'no'){
			$('#dg_fields').hide();
		}else{
			$('#dg_fields').show();
		}
		//org not set check
		if($('#sc_organization').val() == 'None')
		{
			$('#donor_org_div').show();
		}else{
			$('#donor_org_div').hide();
		}
		//check for shortcode fields
		if($('#sc_fund1enable').val() == 'yes')
		{
			$('#div_fund1').show();
			$('#fund1label').html($('#sc_fund1name').val());
		}else{
			$('#div_fund1').hide();
		}
		if($('#sc_fund2enable').val() == 'yes')
		{
			$('#div_fund2').show();
			$('#fund2label').html($('#sc_fund2name').val());
		}else{
			$('#div_fund2').hide();
		}

	});
});

//toggle visibility on infopopup class objects
jQuery(document).ready(function($){
	$('.infopopup').click(function(e){
		if($(this).children('.popupcontent').hasClass('show')){
			$('.infopopup').children('.popupcontent').removeClass('show');	//'close' all popups
		} else {
			$('.infopopup').children('.popupcontent').removeClass('show');	//'close' all popups
			$(this).children('.popupcontent').addClass('show');
		}
		e.stopPropagation();	//stop the click event from bubbling to the page event
		$('html').one("click",function(f){	//create one-time event handler that closes popups
				$('.infopopup').children('.popupcontent').removeClass('show');
		});	
	});
});

jQuery(document).ready(function($){
	$('#payperiodinputs').change(function(){
		var p = parseInt($('#payperiodinputs').val());
		var max = parseInt($('#payperiodinputs').attr('max'));
		if (p >= max){
			p = max;
		} else if (p < 1.0){
			p = 1;
		}
		$('#payperiodinputs').val(parseInt(p));
		fund_sum(p);
	});
});

//javascript updates visibility of input elements based on selected payment type (donationradio)
function change_frequency(donationradio){
	var optionalperiods = document.getElementById("sc_optionalperiods");
	var maxperiods = document.getElementById("sc_payperiods");
	var payperiodinputs =  document.getElementById("payperiodinputs");
	var ccpaymentcontainer = document.getElementById("cc-payment-container");	//div that contains cc data entry
	var periodlabel = document.getElementById("periodinput_label");
	var periodtotallabel = document.getElementById("period_total_label");
	var payperiod_container = document.getElementById("payperiod_container");	//div that contains payperiod data
	switch(donationradio.value)
	{
		case "workplace":
			payperiodinputs.style.display = "block";
			ccpaymentcontainer.style.display = "none";
			periodlabel.style.display = "block";
			payperiod_container.style.display = "block";
			periodtotallabel.innerHTML = "Per Period Amount";
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
    		periodtotallabel.innerHTML = "Monthly Amount";
			payperiodinputs.setAttribute('readonly','readonly');
    		break;
    	case "cc-annual":
			payperiodinputs.style.display = "none";
			ccpaymentcontainer.style.display = "flex";
			periodlabel.style.display = "none";
			payperiod_container.style.display = "none";
    		payperiodinputs.value = '1';
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
	fund_sum();
}

function fund_sum(periods){
	var fund_community = document.getElementById('fund_community');
	var fund_education = document.getElementById('fund_education');
	var fund_designated = document.getElementById('fund_designated');
	periods = typeof periods !== 'undefined' ? periods : document.getElementById("payperiodinputs").value;

	if (isNaN(fund_community.value) || (fund_community.value == "") || (fund_community.value < 0.0)){
		fund_community.value = (0.0).toFixed(2);
	} else {
		fund_community.value = parseFloat(fund_community.value).toFixed(2);
	}
	if (isNaN(fund_education.value) || (fund_education.value == "") || (fund_education.value < 0.0)){
		fund_education.value = (0.0).toFixed(2);
	} else {
		fund_education.value = parseFloat(fund_education.value).toFixed(2);
	}
	if (isNaN(fund_designated.value) || (fund_designated.value == "") || (fund_designated.value < 0.0)){
		fund_designated.value = (0.0).toFixed(2);
	} else {
		fund_designated.value = parseFloat(fund_designated.value).toFixed(2);
	}

	var sum = 0.0;
	var funds = document.getElementsByClassName('racc_fund');
	var i;
	for(i = 0; i < funds.length; i++){
		sum += parseFloat(funds[i].value);
	}
	check_artscardqualifty(sum);
	calc_periodtotal(sum, periods);
	document.getElementById('fund_total').value = sum.toFixed(2);
}

function check_artscardqualifty(test_total){
	var artscardqualify = document.getElementById('artscardqualify');
	if(test_total >= 60.0)
		{
			jQuery('#artscardvalidation').show();
			artscardqualify.setAttribute('value','yes');
		}else{
			document.getElementById('giftartscard').checked = false;
			jQuery('#artscardvalidation').hide();
			artscardhide();
			artscardqualify.setAttribute('value','no');
		}
}

function calc_periodtotal(total, periods){
	var period_total = document.getElementById("period_total");
	var t = total / periods; 
	period_total.value = t.toFixed(2);
}