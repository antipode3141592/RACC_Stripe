<?php
 
function RACC_stripe_settings_setup() {
	add_options_page('Stripe Settings', 'Stripe Settings', 'manage_options', 'stripe-settings', 'RACC_stripe_render_options_page');
}
add_action('admin_menu', 'RACC_stripe_settings_setup');
 
function RACC_stripe_render_options_page() {
	global $stripe_options;
	?>
	<div class="wrap">
		<h2><?php _e('Stripe Settings', 'RACC_stripe'); ?></h2>
		<form method="post" action="options.php">
 
			<?php settings_fields('stripe_settings_group'); ?>
 
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Test Mode', 'RACC_stripe'); ?>
						</th>
						<td>
							<input id="stripe_settings[test_mode]" name="stripe_settings[test_mode]" type="checkbox" value="1" <?php checked(1, $stripe_options['test_mode']); ?> />
							<label class="description" for="stripe_settings[test_mode]"><?php _e('Check this to use the plugin in test mode.', 'RACC_stripe'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>	
 
			<h3 class="title"><?php _e('API Keys', 'RACC_stripe'); ?></h3>
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Live Secret', 'RACC_stripe'); ?>
						</th>
						<td>
							<input id="stripe_settings[live_secret_key]" name="stripe_settings[live_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[live_secret_key]"><?php _e('Paste your live secret key.', 'RACC_stripe'); ?></label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Live Publishable', 'RACC_stripe'); ?>
						</th>
						<td>
							<input id="stripe_settings[live_publishable_key]" name="stripe_settings[live_publishable_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[live_publishable_key]"><?php _e('Paste your live publishable key.', 'RACC_stripe'); ?></label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Test Secret', 'RACC_stripe'); ?>
						</th>
						<td>
							<input id="stripe_settings[test_secret_key]" name="stripe_settings[test_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['test_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[test_secret_key]"><?php _e('Paste your test secret key.', 'RACC_stripe'); ?></label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Test Publishable', 'RACC_stripe'); ?>
						</th>
						<td>
							<input id="stripe_settings[test_publishable_key]" name="stripe_settings[test_publishable_key]" class="regular-text" type="text" value="<?php echo $stripe_options['test_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[test_publishable_key]"><?php _e('Paste your test publishable key.', 'RACC_stripe'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>	
 
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Options', 'RACC_stripe'); ?>" />
			</p>
 
		</form>
	<?php
}
 
function RACC_stripe_register_settings() {
	// creates our settings in the options table
	register_setting('stripe_settings_group', 'stripe_settings');
}
add_action('admin_init', 'RACC_stripe_register_settings');