<?php
/*
	Plugin Name: Visual Website Optimizer Snippet Embedder
	Plugin URI: http://michaelkjeldsen.com/vwo-embedder/
	Description: Easily add the Visual Website Optimizer script on your website.
	Author: Michael Kjeldsen
	Version: 1.1.2
	Author URI: http://michaelkjeldsen.com/
	Text Domain: mchl-vwo-snippet-embedder

	RELEASE NOTES
	---------------------------------------
	1.1.2 - Refactored code
	1.1.1 - Added "Create account" link
	1.1.0 - I18n Ready
	1.0.1 - Now with 100 % more input fields (for, like, you know,
			this plugin to actually make sense)
	1.0.0 - A Working Release

	FEATURE WISH LIST
	---------------------------------------
	* "No account_id added too settings page" admin error
	* Allow user to change advanced seettings (tolerances, use_existing_jquery)
	* API-connect to select Project
	* API-connect to get test results
	* API-to-Dashboard to showcase running test(s)
*/

	define( 'MCHL_VWOSE_VERSION', '1.1.2' );

	if ( !is_admin() )
		{
			add_action('wp_head','hook_javascript_vwo_script');
			function hook_javascript_vwo_script()
				{
					$output="<!-- Start Visual Website Optimizer Asynchronous Code -->
	<!-- Added by mchl VWO Snippet Embedder(tm). http://mchl.dk/vwo-embedder/ -->
	<script type='text/javascript'>
		var _vwo_code=(function(){
		var account_id=" . get_option('mchl_vwo_data') . ",
		settings_tolerance=2000,
		library_tolerance=2500,
		use_existing_jquery=false,
		// DO NOT EDIT BELOW THIS LINE
		f=false,d=document;return{use_existing_jquery:function(){return use_existing_jquery;},library_tolerance:function(){return library_tolerance;},finish:function(){if(!f){f=true;var a=d.getElementById('_vis_opt_path_hides');if(a)a.parentNode.removeChild(a);}},finished:function(){return f;},load:function(a){var b=d.createElement('script');b.src=a;b.type='text/javascript';b.innerText;b.onerror=function(){_vwo_code.finish();};d.getElementsByTagName('head')[0].appendChild(b);},init:function(){settings_timer=setTimeout('_vwo_code.finish()',settings_tolerance);this.load('//dev.visualwebsiteoptimizer.com/j.php?a='+account_id+'&u='+encodeURIComponent(d.URL)+'&r='+Math.random());var a=d.createElement('style'),b='body{opacity:0 !important;filter:alpha(opacity=0) !important;background:none !important;}',h=d.getElementsByTagName('head')[0];a.setAttribute('id','_vis_opt_path_hides');a.setAttribute('type','text/css');if(a.styleSheet)a.styleSheet.cssText=b;else a.appendChild(d.createTextNode(b));h.appendChild(a);return settings_timer;}};}());_vwo_settings_timer=_vwo_code.init();
	</script>
	<!-- End Visual Website Optimizer Asynchronous Code -->";

					echo $output;
				}

			function mchl_vwo_plugin_get_version()
				{
					if ( ! function_exists( 'get_plugins' ) ) 
						{
							require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						}

					$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
					$plugin_file = basename( ( __FILE__ ) );
					echo $plugin_folder[$plugin_file]['Version'];
				}
		}

	if ( is_admin () )
		{
			// Runs when plugin is activated
			register_activation_hook(__FILE__,'mchl_vwo_install');
			function mchl_vwo_install()
				{
					add_option("mchl_vwo_data", '', '', 'yes');
				}

			// Runs on plugin deactivation
			register_deactivation_hook( __FILE__, 'mchl_vwo_remove' );
			function mchl_vwo_remove()
				{
					delete_option('mchl_vwo_data');
				}

			// Add Admin submenu page
			add_action('admin_menu', 'mchl_vwo_account_id_options_panel');
			function mchl_vwo_account_id_options_panel()
				{
					add_submenu_page( 'tools.php', 'VWO Snippet Embedder', 'VWO Snippet Embedder', 'manage_options', 'mchl-vwo-snippet-embedder', 'mchl_vwo_snippet_embedder_html_page', plugins_url('lh-admin/lederne-cirkler.png') );
				}

			// VWO Account ID
			// @todo: Refactor
			add_action('init','mchl_vwo_account_id');
			function mchl_vwo_account_id()
				{
					$mchl_vwo_account_id = get_option('mchl_vwo_account_id');
					return '<span class="mchl_vwo_account_id">' . $mchl_vwo_account_id . '</span>';
				}

			// SaveAdmin
			// @todo: Refactor
			function mchl_vwo_saveadmin()
				{
					$mchl_vwo_account_id = get_option('mchl_vwo_saveadmin_data');
					return '<span class="mchl_vwo_account_id">' . $mchl_vwo_account_id . '</span>';
				}

			// Add settings link on plugin page
			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", 'vwo_snippet_embedder_donate_link' );
			function vwo_snippet_embedder_donate_link($links)
				{ 
					$donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=69NSYMHNFPEFJ&lc=DK&item_name=VWO%20Snippet%20Embedder%20donation&item_number=vwosnipemb%2ddonation&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest">' . __( 'Donate', 'mchl-vwo-snippet-embedder' ) . '</a>'; 
					array_unshift($links, $donate_link); 
					return $links; 
				}

			// Add donate link on plugin page
			$plugin = plugin_basename(__FILE__); 
			add_filter("plugin_action_links_$plugin", 'vwo_snippet_embedder_settings_link' );
			function vwo_snippet_embedder_settings_link($links)
				{ 
					$settings_link = '<a href="tools.php?page=mchl-vwo-snippet-embedder.php">' . __( 'Settings', 'mchl-vwo-snippet-embedder' ) . '</a>'; 
					array_unshift($links, $settings_link); 
					return $links;
				}

			// Markup for the settings page
			function mchl_vwo_snippet_embedder_html_page ()
				{
					$output = '
						<div class="wrap">
							div id="icon-themes" class="icon32"><br></div>
							<h2>Visual Website Optimizer Snippet Embedder&trade; <small>' . __( 'by', 'mchl-vwo-snippet-embedder' ) . ' Michael Kjeldsen</small></h2>';

							if ( isset($_GET['settings-updated']) )
								{
									$output .= '<div id="message" class="updated">
										<p><strong>' . __( 'Settings updated.', 'mchl-vwo-snippet-embedder' ) . '</strong></p>
									</div>';
								}

							$output .= '<form method="post" action="options.php">
							' . wp_nonce_field('update-options') . '

								<input type="hidden" name="action" value="update" />
								<input type="hidden" name="page_options" value="mchl_vwo_data" />

								<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row"><label for="mchl_vwo_data">Account ID:</label></th>
										<td><input name="mchl_vwo_data" class="large-text" style="width:200px;" type="text" pattern="[0-9]*" id="mchl_vwo_data" value="' . get_option('mchl_vwo_data') . '" placeholder="XXXXX" required autofocus></td>
									</tr>
									<tr>
										<th>' . __( "Don't have an account?", 'mchl-vwo-snippet-embedder' ) . '</th>
										<td><em><a href="https://vwo.com/?utm_source=vwo+snippet+embedder+wp+plugin&amp;utm_medium=link&amp;&utm_campaign=create+free+account" target="_blank">' . __( "Create a free account here", 'mchl-vwo-snippet-embedder' ) . ' &raquo;</a></em></td>
									</tr>
								</tbody>
								</table>

								<p>
									<input type="submit" name="submit" id="submit" class="button button-primary" value="' . __( 'Update settings', 'mchl-vwo-snippet-embedder' ) . '" />
								</p>

							</form>
							<hr>
							<p>
								<em>Visual Website Optimizer Snippet Embedder&trade;</em> ' . __( 'is a forever-free plugin by', 'mchl-vwo-snippet-embedder' ) .' <a href="http://michaelkjeldsen.com">Michael Kjeldsen</a>.
								' . __( 'Feel free to share with everyone you know.', 'mchl-vwo-snippet-embedder' ) .'
								' . __( 'Do you like this plugin, please', 'mchl-vwo-snippet-embedder' ) .' <a href="http://michaelkjeldsen.com/donate-vwo/" target="_blank">' . __( 'donate a beer to the developer', 'mchl-vwo-snippet-embedder' ) . '</a>.
							</p>
						</div>';

					echo $output;
				}
		}

// Here be dragons...