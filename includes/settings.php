<?php
 
 // settings for Stripe API
function RACC_stripe_settings_setup() {
	add_options_page('RACC Stripe Integration Settings', 'RACC Stripe', 'manage_options', 'stripe-settings', 'RACC_stripe_render_options_page');
}
add_action('admin_menu', 'RACC_stripe_settings_setup');
 
function RACC_stripe_render_options_page() {
	global $stripe_options;
	?>
	<div class="wrap">
		<h2>Stripe Settings</h2>
		<form id="stripe_settings_form" method="post" action="options.php">
 
			<?php settings_fields('stripe_settings_group'); ?>
			<h3 class="title">API Keys</h3>
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">Test Mode</th>
						<td>
							<input id="stripe_settings[test_mode]" name="stripe_settings[test_mode]" type="checkbox" value="test" <?php checked("test", $stripe_options['test_mode']); ?> />
							<label class="description" for="stripe_settings[test_mode]">Check this to use the plugin in test mode.</label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">Live Secret</th>
						<td>
							<input id="stripe_settings[live_secret_key]" name="stripe_settings[live_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[live_secret_key]">Paste your live secret key.</label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">Live Publishable</th>
						<td>
							<input id="stripe_settings[live_publishable_key]" name="stripe_settings[live_publishable_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[live_publishable_key]">Paste your live publishable key.</label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">Test Secret</th>
						<td>
							<input id="stripe_settings[test_secret_key]" name="stripe_settings[test_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['test_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[test_secret_key]">Paste your test secret key.</label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">Test Publishable</th>
						<td>
							<input id="stripe_settings[test_publishable_key]" name="stripe_settings[test_publishable_key]" class="regular-text" type="text" value="<?php echo $stripe_options['test_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[test_publishable_key]">Paste your test publishable key.</label>
						</td>
					</tr>
				</tbody>
			</table>
 
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Options" />
			</p>
 
		</form>
	<?php
}
 
function RACC_stripe_register_settings() {
	// creates our settings in the options table
	register_setting('stripe_settings_group', 'stripe_settings');
}
add_action('admin_init', 'RACC_stripe_register_settings');



