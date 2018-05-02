function stripeResponseHandler(token){
	if(token.error){
		jQuery(".payment-error").html(token.error.message);
		jQuery('#stripe-submit').attr("disabled", false);
	}else {
		var form = jQuery("#stripe-payment-form-singlepage");
        if(jQuery('input[name="donation_frequency"]:checked').val() == "cc-recur" || jQuery('input[name="donation_frequency"]:checked').val() == "cc-once")
        {
        	var tokenid = token.token.id;
			form.append("<input type='hidden' name='stripeToken' value='" + tokenid + "'/>");
		}
		form.submit();	
	}
}

window.addEventListener("DOMContentLoaded", function(event){
	// Add an instance of the card Element into the `card-element` <div>.
	var form = document.getElementById('stripe-payment-form-singlepage');
	if (form){
		try{
			var stripe = Stripe('pk_test_uWWAtAgCx02zLJ4dTmXopRn8');
			var elements = stripe.elements();
			var card = elements.create('card',{hidePostalCode: true});
			// var elements = stripe.elements();
			card.mount('#card-element');
			card.addEventListener('change', function(event) {
  				if (event.error) {
				    jQuery('.payment-error').html(event.error.message);
				} else {
				    jQuery('.payment-error').html('');
				}
			});
			// document.getElementById('stripe-payment-form-singlepage').addEventListener('submit',function(e){
			document.getElementById('stripe-submit').addEventListener('click',function(e){
				
				document.getElementById('stripe-submit').setAttribute("disabled", "disabled");
				if ((jQuery('input[name="donation_frequency"]:checked').val() == "cc-once") || (jQuery('input[name="donation_frequency"]:checked').val() == "cc-recur")){
					e.preventDefault();
					stripe.createToken(card).then(stripeResponseHandler);
				}else{
					document.getElementById('stripe-payment-form-singlepage').submit();
				}
			});
		}catch(err){
			console.log(err.message);
		}
	}
});