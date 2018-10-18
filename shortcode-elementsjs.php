<?php
function racc_stripe_payment_form_elementsjs($atts, $content = null) {
	// $atts contains organization(string) and payperiods(int) options.  example: [payment_form_singlepage organization="PGE" payperiods=26 optionalperiods="yes"]
	global $stripe_options;
	global $wpdb;
	extract( shortcode_atts( array(
		'organization' => 'None',	//default None for organization name
		'payperiods' => '26',		//default 26 for payperiods in a year
		'optionalperiods' => 'no',	//'yes' or 'no', 'yes' allows donor to specify 
		'payroll' => 'yes',			//'yes' allows selection of workplace giving option and disables cc-recur, 'no' disables workplace giving option and enables cc-recur
		'dg' =>'no'
	), $atts ) );

	//TODO: add $_POST calls to grab starting data, for cases where user 

	ob_start();
	?> 
	<div id="racc-form-holder">
	<form method="post" id="stripe-payment-form-singlepage" class="stripe-payment-form-elementsjs">
		<?php
			if ($organization != "None"){
				?>
				<h1><?php _e(strip_tags($organization))?></h1>
				<?php		
			}
		
		?>
		<div name="donor-information" id="donor-information">
			<h2>Donor Information</h2>
			<div class="form-row" id="donor_names_div">
				<span id="first_name_section">
					<label for="donor_first_name">First Name *</label>
					<input type="text" size="40" maxlength="40" name="donor_first_name" id="donor_first_name" class="input_text" autocomplete="given-name" required/>
				</span>
				<span id="last_name_section">	
					<label for="donor_last_name">Last Name *</label>
					<input type="text" size="40" maxlength="40" name="donor_last_name" id="donor_last_name" class="input_text" autocomplete="family-name" required/>
				</span>
			</div>
			<div class="form-row" id="email_and_phone_div">	
				<span id="donor_email_section">
					<label for="donor_email">E-Mail *</label>
					<input type="email" size="40" maxlength="40" class="email" name="donor_email" id="donor_email" class="input_text" autocomplete="email" required/>
				</span>
				<span id="donor_phone_section">
					<label for="donor_phone">Phone *</label>
					<input type="text" size="15" maxlength="40" name="donor_phone" autocomplete="tel-national" id="donor_text" class="input_text" required />
				</span>
			</div>
			<div class="form-row" id="donor_addresses_div">	
					<label for="donor_address_1">Mailing Address *</label>
					<input type="text" size="50" maxlength="50" name="donor_address_1" id="donor_address_1" class="input_text" autocomplete="address-line1" required/>
					<input type="text" size="50" maxlength="50" name="donor_address_2" id="donor_address_2" class="input_text" autocomplete="address-line2" />
			</div>
			<div class="form-row" id="donor_addresses_citystatezip_div">
				<span id="city-section">
					<label for="donor_city">City *</label> 
					<input type="text" size="50" maxlength="50" name="donor_city" id="donor_city" class="input_text" autocomplete="address-level2" required/>
				</span>
				<span id="state-section">
					<label for="donor_state">State *</label>
					<input type="text" size="2" maxlength="2" name="donor_state" id="donor_state" class="input_text" autocomplete="address-level1" required/>
				</span>
				<span id="zip-section">
					<label for="donor_zip">Zip *</label>
					<input type="text" size="10" maxlength="15" name="donor_zip" id="donor_zip" class="input_text" autocomplete="postal-code" required/>
				</span>
			</div>
			<div class="form-row" id="donor_org_div">
				<label for="donor_org_input">Organization</label>
				<input type="text" name="donor_org_input" id="donor_org_input" maxlength="50" class="input_text" autocomplete="off">
			</div>
			<div class="form-row" id="donor_anon_div">
				<label for="anon">Anonymous Gift</label>
				<input type="checkbox" name="anon" id="anon" value="yes"/>
				<div class="infopopup">i
					<div id="anon_description" class="popupcontent">Check this box if you would like us to withold your name from all publications.</div>
				</div>
			</div>
		</div>
		<div name="gift-details" id="gift-details">
			<h2>How would you like to contribute?</h2>
			<div class="form-row" id="freqradio">
				<div id="workplace_div">
					<input type="radio" id="donationradio1" name="donation_frequency" value="workplace" onclick="change_frequency(this);"/><label for="donationradio1">Payroll Deduction</label>
				</div>
				<div>
					<input type="radio" id="donationradio2" name="donation_frequency" value="cc-recur" onclick="change_frequency(this);"/><label for="donationradio2">Recurring Gift - Credit/Debit Card</label>
					<div class="infopopup">i
						<div class="popupcontent">We will send you a new pledge acknowledgement and Arts Card (if applicable) every year.</div>
					</div>
				</div>
				<div>
					<input type="radio" id="donationradio3" name="donation_frequency" value="cc-once" onclick="change_frequency(this);"/><label for="donationradio3">One-Time Gift - Credit/Debit Card</label>
				</div>
				<div>
					<input type="radio" id="donationradio4" name="donation_frequency" value="check" onclick="change_frequency(this);"/><label for="donationradio4">One-Time Gift - Check</label>
				</div>
				
			</div>
			<div id="freq">
				<div id="gift_allocations">
					<h3>Allocation</h3>
					<p>How would you like to distribute your pledge?</p>
					
					<div class="form-row">
						<label for="fund_community" id="fund_community_label">Arts Community Fund</label>
						<div class="infopopup">i
							<div id="fund_community_description" class="popupcontent">Supports tri-county-based arts and culture organizations that receive RACC General Operating Support (GOS) and Project Grant funding for a wide array of services, programs, exhibits, events, and performances.</div>
						</div>
						<input type="number" name="fund_community" id="fund_community" class="racc_fund" value='30.00' step="0.01" min='0' autocomplete="off" onchange="fund_sum();"/>
					</div>

					<div class="form-row">
						<label for="fund_education" id="fund_education_label">Arts Education Fund</label>
						<div class="infopopup">i
							<div id="fund_education_description" class="popupcontent">Distributed to 40+ arts and culture organizations (many GOS groups) that provide substantial arts education opportunities for students and teachers throughout our region.</div>
						</div>
						<input type="number" name="fund_education" id="fund_education" class="racc_fund" value='30.00' step="0.01" min="0" autocomplete="off" onchange="fund_sum();"/>
					</div>

					<div class="form-row" id="dg_fields">
						<label for="fund_designated" id="fund_designated_label">Designated Fund</label>
						<div class="infopopup">i
							<div id="fund_designated_description" class="popupcontent">You may designate part or all of your gift to any arts &amp; culture nonprofit based in Clackamas, Multnomah, or Washington County.</div>
						</div>
						<input type="text" name="fund_designated_name" id="fund_designated_name" placeholder="Org Name" value="" maxlength="100"/>
						<input type="number" name="fund_designated" id="fund_designated" class="racc_fund" value='0.00' step="0.01" min="0" autocomplete="off" onchange="fund_sum();"/>
					</div>

					<div class="form-row" >
						<label for="fund_total" id="fund_total_label">Annual Pledge</label>
						<input type="number" name="fund_total" id="fund_total" value='60.00' step='0.01' min='1' autocomplete="off" readonly/>
					</div>
					
					<div id="payperiod_container" style="display: none">
						<label for="payperiodinputs" id="periodinput_label" style="display: none">Pay Periods</label>
						<input type="number" name="payperiodinputs" id="payperiodinputs" style="display: none" value="1" step="1" min="1" autocomplete="off" readonly/>
						<label for="period_total" id="period_total_label">Per Period Amount</label>
						<input type="number" name="period_total" id="period_total" value='60.00' readonly/>
					</div>
				</div>
			</div>
		</div>
		<div id="artscardvalidation">
			<h2>In appreciation of your pledge of $60 or more, we'll be mailing you The Arts Card!</h2>
			<div id="artscard_image">&nbsp;</div>
			<div id="giftartscard_input">
				<label for="giftartscard">I prefer to gift my Arts Card</label>
				<input type="checkbox" name="giftartscard" id="giftartscard" value="yes"/>
			</div>
		</div>
		<div id="artscard_address_input" style="display: none">
			<h3>Arts Card Recepient</h3>
			<div class="form-row" id="artscard_name_div">
				<label for="artscard_name">Name *</label>
				<input type="text" size="50" maxlength="50" name="artscard_name" id="artscard_name" class="input_text" autocomplete="section-gift name" />
			</div>
			<div class="form-row" id="artscard_email_div">
				<label for="artscard_email">E-Mail *</label>
				<input type="email" size="40" maxlength="40" class="email" name="artscard_email" id="artscard_email" class="input_text" autocomplete="section-gift email" />
			</div>
			<div class="form-row" id="artscard_address_div">	
				<label for="artscard_address_1">Mailing Address *</label>
				<input type="text" size="50" maxlength="50" name="artscard_address_1" id="artscard_address_1" class="input_text" autocomplete="section-gift address-line1" />
				<input type="text" size="50" maxlength="50" name="artscard_address_2" id="artscard_address_2" class="input_text" autocomplete="section-gift address-line2" />
			</div>
			<div class="form-row" id="artscard_citystatezip_div">
				<span id="artscard_city_section">
					<label for="artscard_city">City *</label> 
					<input type="text" size="50" maxlength="50" name="artscard_city" id="artscard_city" class="input_text" autocomplete="section-gift address-level2" />
				</span>
				<span id="artscard_state_section">
					<label for="artscard_state">State *</label>
					<input type="text" size="2" maxlength="2" name="artscard_state" id="artscard_state" class="input_text" autocomplete="section-gift address-level1" />
				</span>
				<span id="artscard_zip_section">
					<label for="artscard_zip">Zip *</label>
					<input type="text" size="10" maxlength="15" name="artscard_zip" id="artscard_zip" class="input_text" autocomplete="section-gift postal-code" />
				</span>
			</div>
		</div>
		<div id="cc-payment-container" style="display: flex">
			<h2>Payment Information</h2>
			<label for="card-element">Credit or Debit</label>
			<div id="card-element" class="form-row">
			</div>
			<div id="cc_images">
				<table class="image_table">
					<tr>
						<td id="img1"></td>
						<td id="img2"></td>
						<td id="img3"></td>
						<td id="img4"></td>
						<td id="img5"></td>
						<td id="img6"></td>
					</tr>
				</table>
			</div>
		</div>
		<div id="additional-comments" class="wrap">
			<h2>Comments?</h2>
			<textarea id="comment_input" name="comment_input" maxlength="500" placeholder="Comments/Questions/Concerns" rows="5" ></textarea>
		</div>
		<div class="payment-error" id="payment_error_div"></div>
		<input type="hidden" name="sc_organization" id="sc_organization" value="<?php _e($organization);?>"/>
		<input type="hidden" id="sc_payperiods" value="<?php _e($payperiods);?>"/>
		<input type="hidden" name="sc_optionalperiods" id="sc_optionalperiods" value="<?php _e($optionalperiods);?>"/>
		<input type="hidden" name="sc_payroll" id="sc_payroll" value="<?php _e($payroll);?>"/>
		<input type="hidden" name="sc_dg" id="sc_dg" value="<?php _e($dg);?>"/>
		<input type="hidden" name="artscardqualify" id="artscardqualify" value="yes"/>
		<input type="hidden" name="action" value="stripe"/>
		<input type="hidden" name="stripe_nonce" value="<?php _e(wp_create_nonce('stripe-nonce')); ?>"/>
		<button type="button" name="confirmation_button" id="confirmation_button">Go to Confirmation</button>
		
		<div name="confirmation_popup" id="confirmation_popup" style="display: none">
			<div id="confirmation_popup_content">
				<h2>Confirmation</h2>
				<p>Please review the details below before confirming your pledge.</p>
				<div id="personal_info">
					<div id="confirm_name"></div>
					<div id="confirm_email"></div>
					<div id="confirm_donor_address"></div>
				</div>
				<div id="artscard_info">
					<div id="confirm_artscard"></div>
					<div id="confirm_artscard_image"></div>
					<div id="confirm_artscard_address"></div>
				</div>
				<div id="gift_info">
					<div id="confirm_fund_community"></div>
					<div id="confirm_fund_education"></div>
					<div id="confirm_fund_designated"></div>
					<div id="confirm_fund_total"></div>
					<div id="confirm_paymethod"></div>
					<div id="confirm_payroll_deduction">
						<label class="important_text" for="confirm_payroll_authorization">Check to Authorize Your Payroll Deduction Gift</label>
						<input type="checkbox" name="confirm_payroll_authorization" id="confirm_payroll_authorization"/>
					</div>
				</div>
				<button type="button" id="confirmation_reset">Edit Pledge</button>
				<button type="submit" id="stripe-submit" form="stripe-payment-form-singlepage" disabled>Confirm Pledge</button>
				<div class="payment-error" id="payment_error_popup_div"></div>
			</div>
		</div>
		</form>
			
		</div>

	<?php
	return ob_get_clean();
}
add_shortcode('payment_form_elementsjs', 'racc_stripe_payment_form_elementsjs');

?>