// validate required input fields exist
// shows confirmation div, populates values for user review
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
			$('.payment-error').html('');
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
			if ($('#sc_dg').val() == 'yes'){
				$('#confirm_fund_designated').html('Designated Fund (' + $('#fund_designated_name').val() + ': $'+ parseFloat($('#fund_designated').val()).toFixed(2));
			}
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
					$('#confirm_paymethod').html('Giving Method: <b>Please mail your check to Work for Art by June 2018</b>.');
					break;
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
	$('#giftartscard').change(function(){
		artscardhide();
		// console.log('clicked the giftartscard box');
		// if($('#giftartscard').prop("checked")){
		// 	// console.log('show gift address div');
		// 	$('#artscard_address_input').show();
		// 	$('#artscard_name').attr('required','required');
		// 	$('#artscard_email').attr('required','required');
		// 	$('#artscard_address_1').attr('required','required');
		// 	$('#artscard_city').attr('required','required');
		// 	$('#artscard_state').attr('required','required');
		// 	$('#artscard_zip').attr('required','required');
		// }else
		// {
		// 	// console.log('hide gift address div');
		// 	$('#artscard_address_input').hide();
		// 	$('#artscard_name').attr('required',false);
		// 	$('#artscard_email').attr('required',false);
		// 	$('#artscard_address_1').attr('required',false);
		// 	$('#artscard_city').attr('required',false);
		// 	$('#artscard_state').attr('required',false);
		// 	$('#artscard_zip').attr('required',false);
		// }
		// return false;
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
		if($('#sc_payroll').val() == 'no'){
			$('#workplace_div').hide();
			jQuery('#donationradio3').click();
		}else{		
			$('#workplace_div').show();
			jQuery('#donationradio1').click();
		}
		if($('#sc_dg').val() == 'no'){
			$('#dg_fields').hide();
			$('label[for=fund_community_lock').hide();
			$('label[for=fund_education_lock').hide();
		}else{
			$('#dg_fields').show();
			$('.input_lock').show();
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
	var temp_total = parseFloat(fund_total.value);
	var fund_community = document.getElementById("fund_community");
	var fund_education = document.getElementById("fund_education");
	var fund_designated = document.getElementById("fund_designated");
	var com_lock = document.getElementById("fund_community_lock");
	var ed_lock = document.getElementById("fund_education_lock");
	var dg_lock = document.getElementById("fund_designated_lock");
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
			// console.log(temp_periods);
			temp_periods = 1;
		} else if (temp_periods < 1) {
			temp_periods = 1
		}
		periods = temp_periods;
		var tot = temp_total/parseFloat(temp_periods);
		period_total.value = tot.toFixed(2);
		
		//preserve ratio of funds
		var com_ratio = parseFloat(parseFloat(fund_community.value)/parseFloat(parseFloat(fund_community.value) + parseFloat(fund_education.value) + parseFloat(fund_designated.value)));	//% community, do not round
		var ed_ratio = parseFloat(parseFloat(fund_education.value)/parseFloat(parseFloat(fund_community.value) + parseFloat(fund_education.value) + parseFloat(fund_designated.value)));	//% community, do not round
		var dg_ratio = 1.0 - com_ratio - ed_ratio;
		
		//unlock allocations and then scale current values
		com_lock.checked = false;
		ed_lock.checked = false;
		dg_lock.checked = false;
		com_lock.setAttribute('readonly',false);
		ed_lock.setAttribute('readonly',false);
		dg_lock.setAttribute('readonly',false);

		fund_community.value = parseFloat(temp_total * com_ratio).toFixed(2);
		fund_education.value = parseFloat(temp_total * ed_ratio).toFixed(2);
		fund_designated.value = parseFloat(temp_total * dg_ratio).toFixed(2);

		fund_total.value = temp_total.toFixed(2);	//format decimal points on input

		fund_community.setAttribute('max',temp_total.toFixed(2));
		fund_education.setAttribute('max',temp_total.toFixed(2));
		fund_designated.setAttribute('max',temp_total.toFixed(2));
		// fund_community.value = parseFloat(fund_total.value).toFixed(2);
		// fund_education.value = 0.00;
		if(temp_total >= 60.0)
		{
			jQuery('#artscardvalidation').show();
			artscardqualify.setAttribute('value','yes');
		}else{
			document.getElementById('giftartscard').checked = false;
			jQuery('#artscardvalidation').hide();
			artscardhide();
			artscardqualify.setAttribute('value','no');
		}
		// change_fund();
	}catch(err)
	{
		console.log("breakdown_total(): " + err.message);
	}
}


//'lock' functions that set readonly for associated number inputs
jQuery(document).ready(function($){
	$("#fund_community_lock").change(function(){
		if(this.checked){
			$("#fund_community").prop('readonly',true);
		} else {
			$("#fund_community").prop('readonly',false);
		}
	});
});

jQuery(document).ready(function($){
	$("#fund_education_lock").change(function(){
		if(this.checked){
			$("#fund_education").prop('readonly',true);
		} else {
			$("#fund_education").prop('readonly',false);
		}
	});
});

jQuery(document).ready(function($){
	$("#fund_designated_lock").change(function(){
		if(this.checked){
			$("#fund_designated").prop('readonly',true);
		} else {
			$("#fund_designated").prop('readonly',false);
		}
	});
});


// //value change functions for text inputs
// jQuery(document).ready(function($){
// 	$(".input_text").change(function(){
// 		this.val(str.replace("",this.val()));
// 	});
// });

//value change functions for allocation inputs
jQuery(document).ready(function($){
	$('#fund_community').change(function(){
  		if(!$("#fund_community_lock").checked){		//first check to see if locked, proceed if unlocked
  			var temp = 0.0;
			var regex = /[0-9]|\./;
			if (!regex.test($(this).val())){
				temp = 0.0;
			} else if (parseFloat($(this).val()) > parseFloat($(this).attr('max'))){
				temp = parseFloat($(this).attr('max'));
			} else if (parseFloat($(this).val()) < 0) {
				temp = 0.0
			} else {
				temp = parseFloat($(this).val());
			}
			var tot = parseFloat($('#fund_total').val());
			var fund_ed = parseFloat($('#fund_education').val());
			var fund_dg = parseFloat($('#fund_designated').val());
			if ($("#fund_education_lock").is(':checked')){
				//don't change education, change designated
				console.log("fund_community.change(), education locked!");
				$('#fund_designated').val((tot - temp - fund_ed).toFixed(2));
			} else if ($("#fund_designated_lock").is(':checked')){
				//don't change designated, change education
				console.log("fund_community.change(), designated locked!");
				$('#fund_education').val((tot - temp - fund_dg).toFixed(2));
			} else {
				//change designated and education
				console.log("fund_community.change(), no locks!");
				var fund_ed_prop = fund_ed/(fund_ed + fund_dg);
				$('#fund_designated').val(((tot - temp) * (1.0 - fund_ed_prop)).toFixed(2));
				$('#fund_education').val(((tot - temp) * fund_ed_prop).toFixed(2));
			}
			$('#fund_community').val(temp.toFixed(2));
		} else {
			//action for locked
		}

	});
});

jQuery(document).ready(function($){
	$('#fund_education').change(function(){
		if(!$("#fund_education_lock").checked){
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
			var tot = parseFloat($('#fund_total').val());
			var fund_com = parseFloat($('#fund_community').val());
			var fund_dg = parseFloat($('#fund_designated').val());
			if ($("#fund_community_lock").is(':checked')){
				//change designated
				console.log("fund_education.change(), community locked!");
				$('#fund_designated').val((tot - temp - fund_com).toFixed(2));
			} else if ($("#fund_designated_lock").is(':checked')){
				//change community
				console.log("fund_education.change(), designated locked!");
				$('#fund_community').val((tot - temp - fund_dg).toFixed(2));
			} else {
				//change designated and community
				var fund_com_prop = fund_com/(fund_com + fund_dg);
				console.log("fund_education.change(), no locks!");
				$('#fund_designated').val(((tot - temp) * (1.0 - fund_com_prop)).toFixed(2));
				$('#fund_community').val(((tot - temp) * fund_com_prop).toFixed(2));
			}
			$('#fund_education').val(parseFloat(temp).toFixed(2));
		} else {
			//action for locked
		}
	});
});

jQuery(document).ready(function($){
	$('#fund_designated').change(function(){
		if(!$("#fund_designated_lock").checked){
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
			var tot = parseFloat($('#fund_total').val());
			var fund_com = parseFloat($('#fund_community').val());
			var fund_ed = parseFloat($('#fund_education').val());
			if ($("#fund_community_lock").is(':checked')){
				//change education
				console.log("fund_designated.change(), community locked!");
				$('#fund_education').val((tot - temp - fund_com).toFixed(2));
			} else if ($("#fund_education_lock").is(':checked')){
				//change community
				console.log("fund_designated.change(), education locked!");
				$('#fund_community').val((tot - temp - fund_ed).toFixed(2));
			} else {
				//nothing checked
				console.log("fund_designated.change(), no locks!");
				var fund_com_prop = fund_com/(fund_com + fund_ed);
				$('#fund_education').val(((tot - temp) * (1.0 - fund_com_prop)).toFixed(2));
				$('#fund_community').val(((tot - temp) * fund_com_prop).toFixed(2));
			}
			$('#fund_designated').val(parseFloat(temp).toFixed(2));
		} else {
			//action for locked
		}
	});
});