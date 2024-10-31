<?php
/*
Plugin Name: Scripture Highlighter
Version: 1.2.0
Plugin URI: https://theharvest.co/
Description: Simply integrate the Bibles.org Scripture Highlighter into your wordpress website
Author: TheHarvest Company
Author URI: https://theharvest.co/
*/

if (!defined('SCRIPTHIGH_VERSION'))
	define('SCRIPTHIGH_VERSION', '1.2.0');

// Add defines for Stripe Integration
if (!defined('SCRIPTHIGH_BASE_URL'))
	define('SCRIPTHIGH_BASE_URL', plugin_dir_url(__FILE__));

if (!defined('SCRIPTHIGH_BASE_DIR'))
	define('SCRIPTHIGH_BASE_DIR', dirname(__FILE__));

// Holds the element id's which should be parsed by bibles.org
$hvst_scripthigh_sections = array();

// Global Hook Identifiers
$hvst_option_menu_hook = null;

/**
 * hvst_scripthigh_chortcode_highlight enables highlighting on specific content
 *
 * This function is called by a shortcode, it takes the content and wraps it
 * with a span element with a unique id. The unique id is pushed to the section
 * stack, which is used when generating the javascript for parsing verses
 */
function hvst_scripthigh_shortcode_highlight($attr, $content) {
	global $hvst_scripthigh_sections;

	$element_id = 'scripthigh_section_' . count($hvst_scripthigh_sections);
	$out = '<span id="' . $element_id . '">' . $content . "</span>";
	$hvst_scripthigh_sections[] = $element_id;

	return $out;
}

/**
 * hvst_scripthigh_shortcode_ignore enables ignoring
 */
function hvst_scripthigh_shortcode_ignore($attr, $content) {
	return '<span class="no-scriptures">' . $content . "</span>";
}

/**
 * _admin_init is a static function which may be used in add_action for
 * the wp admin_init action
 */
function hvst_scripthigh_admin_init() {
	//register_setting('stripe_settings_group', 'stripe_settings');
	register_setting('hvst_scripthigh_settings_group', 'hvst_scripthigh_settings');
}

/**
 * hvst_scripthigh_user_add_js is a filter that will add the script tag to the head
 *
 * This function should be able to get the settings from wp_settings and configure
 * the options accordingly
 */
function hvst_scripthigh_user_add_js() {

	// Get options
	$scripthigh_settings = get_option('hvst_scripthigh_settings');

	$script_auto_parse = "";
	if ($scripthigh_settings['autoparse_disabled'] == true)
		$script_auto_parse = "data-autoparse=\"false\"";

	$script_data_version = "";
	if ($scripthigh_settings['version'] != null && $scripthigh_settings['version'] != "")
		$script_data_version = "data-version=\"" . $scripthigh_settings['version'] . "\"";

	// This will only be included if AutoParse is On
	$script_ignore_class = "";
	if ($scripthigh_settings['autoparse_disabled'] == false)
		$script_ignore_class = "data-ignore=\"no-scriptures\"";
		//$script_ignore_class = "data-ignore=\"" . $scripthigh_settings['ignore_class'] . "\"";


	// Print out the script to include bibles.org
	print("<script id=\"bw-highlighter-config\" {$script_data_version} {$script_ignore_class} {$script_auto_parse}>");
	print <<<EOF
(function(w, d, s, e, id) {
  w._bhparse = w._bhparse || [];
  function l() {
    if (d.getElementById(id)) return;
    var n = d.createElement(s), x = d.getElementsByTagName(s)[0];
    n.id = id; n.async = true; n.src = '//bibles.org/linker/js/client.js';
    x.parentNode.insertBefore(n, x);
  }
  (w.attachEvent) ? w.attachEvent('on' + e, l) : w.addEventListener(e, l, false);
})(window, document, 'script', 'load', 'bw-highlighter-src');
</script>
EOF;
}

function hvst_scripthigh_user_add_js_footer() {
	global $hvst_scripthigh_sections;

	// Get options
	$scripthigh_settings = get_option('hvst_scripthigh_settings');

	// If autoparse is not 'disabled' then return now (all verses should be found this way)
	//		if autoparse is On
	if ($scripthigh_settings['autoparse_disabled'] == false)
		return;

	// initialize the string for the script
	$script = '';

	// If shortcode used, generate a script for parsing
	if (count($hvst_scripthigh_sections) > 0) {

		$elements = join(', ', $hvst_scripthigh_sections);
		$script .= "_bhparse.push($elements);\n";
	}

	if (strlen($script) > 0) {
		$script = "<script>" . $script . "</script>";

		// Print the script to the browser
		print($script);
	}

}

/**
 * hvst_scripthigh_settings_help is responsible for building the help popup
 *
 * This should contain all of the needed information about the plugin setting
 * and also links to TheHarvest Company KB, TheHarvest Company Plugin Page, and WP Repo
 * previous to 3.3
 */
function hvst_scripthigh_settings_help($contextual_help, $screen_id, $screen) {
	global $hvst_option_menu_hook;

	if ($screen_id == $hvst_option_menu_hook) {
		ob_start();
		include(SCRIPTHIGH_BASE_DIR . '/views/overview_help.php');
		$contextual_help = ob_get_clean();
		//ob_end_clean();
	}

	return $contextual_help;
}

/**
 *
 *
 * This should be used for 3.3 and greater, puttin all of the tabs and side
 * help on the context menu
 */
function hvst_scripthigh_settings_add_help() {
	global $hvst_option_menu_hook;


	$screen = get_current_screen();

	//var_dump($screen);

	if ($screen->id != $hvst_option_menu_hook)
		return;


	// Load the content for each tab


	/* Video Template
	ob_start();
	include(SCRIPTHIGH_BASE_DIR . '/views/settings_video_help.php');
	$video_content = ob_get_clean();
	*/

	ob_start();
	include(SCRIPTHIGH_BASE_DIR . '/views/settings_quickreference_help.php');
	$quickreference_content = ob_get_clean();

	ob_start();
	include(SCRIPTHIGH_BASE_DIR . '/views/settings_about_help.php');
	$about_content = ob_get_clean();

	// Sidebar content
	ob_start();
	include(SCRIPTHIGH_BASE_DIR . '/views/settings_sidebar_help.php');
	$sidebar_content = ob_get_clean();

	// Add my_help_tab if current screen is My Admin Page
    /* Videos Tab
	$screen->add_help_tab( array(
        'id'	=> 'hvst_scripthigh_settings_video_help',
        'title'	=> __('Video Guide'),
        'content'	=> $video_content,
    ) );
	*/

	$screen->add_help_tab( array(
        'id'	=> 'hvst_scripthigh_settings_quickreference_help',
        'title'	=> __('Quick Reference'),
        'content'	=> $quickreference_content,
    ) );

	$screen->add_help_tab( array(
        'id'	=> 'hvst_scripthigh_settings_about_help',
        'title'	=> __('About TheHarvest Co.'),
        'content'	=> $about_content,
    ) );

	$screen->set_help_sidebar($sidebar_content);
}

register_activation_hook(__FILE__, function() {
	//TODO: Activate the plugin here

	// Get the email
	$email = get_option('admin_email', null);

	// Get the URI
	$url = get_option('siteurl', null);

	// Get User information
	$user = wp_get_current_user();
	$firstname = null;
	$lastname = null;
	$displayname = null;
	$useremail = null;
	if ($user instanceof WP_User) {
		$firstname = $user->user_firstname;
		$lastname = $user->user_lastname;
		$displayname = $user->display_name;
		$useremail = $user->user_email;
	}

	$body = '{"jsonrpc": "2.0", "method": "Track.Event", "params": ['
			. '"' . $url . '", "activate", {'
			. (($email !== null) ? '"site_email": "' . $email . '",' : '')
			. (($firstname !== null) ? '"firstname": "' . $firstname . '",' : '')
			. (($lastname !== null) ? '"lastname": "' . $lastname . '",' : '')
			. (($displayname !== null) ? '"displayname": "' . $displayname . '",' : '')
			. (($useremail !== null) ? '"user_email": "' . $useremail . '",' : '')
			. '"plugin": "hvst-scripture-highlighter", "version": "' . SCRIPTHIGH_VERSION . '"'
			. '}], "id": 1}';

	$body = preg_replace('/,,/', ',', $body);
	$body = preg_replace('/,\]/', ']', $body);
	$body = preg_replace('/,\}/', '}', $body);

	//die($body);
	$response = wp_remote_post( 'http://api.tazdij.com/', array(
		'method' => 'POST',
		'timeout' => 2000,
		'blocking' => true,
		'headers' => array(),
		'body' => $body,
		'cookies' => array()
	));
});


register_deactivation_hook(__FILE__, function() {
	//TODO: Deactivate the plugin here

	// Get the email
	$email = get_option('admin_email', null);

	// Get the URI
	$url = get_option('siteurl', null);

	// Get User information
	$user = wp_get_current_user();
	$firstname = null;
	$lastname = null;
	$displayname = null;
	$useremail = null;
	if ($user instanceof WP_User) {
		$firstname = $user->user_firstname;
		$lastname = $user->user_lastname;
		$displayname = $user->display_name;
		$useremail = $user->user_email;
	}

	$body = '{"jsonrpc": "2.0", "method": "Track.Event", "params": ['
			. '"' . $url . '", "deactivate", {'
			. (($email !== null) ? '"site_email": "' . $email . '",' : '')
			. (($firstname !== null) ? '"firstname": "' . $firstname . '",' : '')
			. (($lastname !== null) ? '"lastname": "' . $lastname . '",' : '')
			. (($displayname !== null) ? '"displayname": "' . $displayname . '",' : '')
			. (($useremail !== null) ? '"user_email": "' . $useremail . '",' : '')
			. '"plugin": "hvst-scripture-highlighter", "version": "' . SCRIPTHIGH_VERSION . '"'
			. '}], "id": 1}';

	$body = preg_replace('/,,/', ',', $body);
	$body = preg_replace('/,\]/', ']', $body);
	$body = preg_replace('/,\}/', '}', $body);

	//die($body);
	$response = wp_remote_post( 'http://api.tazdij.com/', array(
		'method' => 'POST',
		'timeout' => 2000,
		'blocking' => true,
		'headers' => array(),
		'body' => $body,
		'cookies' => array()
	));
});

// Admin section only code
if (is_admin()) {
	// admin (administration only)

	// Add admin_menu items
	add_action('admin_menu', function() {
		global $hvst_option_menu_hook;
		//add_menu_page(__('OhSnap!'), __('OhSnap!'), 'manage_options', 'ldigi-ohsnap/admin/dashboard', function() {
		//	$admin = new controllers\Admin();
		//	print $admin->Dashboard();
		//}, 'dashicons-cloud', 74.1);

		$hvst_option_menu_hook = add_options_page(__('Scripture Highlighter'), __('Scripture Highlighter'), 'manage_options', 'hvst-scripture-highlighter/settings', function() {
			// Include the php view
			$scripthigh_settings = get_option('hvst_scripthigh_settings');
			include(SCRIPTHIGH_BASE_DIR . '/views/settings.php');
		});

		add_action('load-' . $hvst_option_menu_hook, 'hvst_scripthigh_settings_add_help');
	});

	// Add Admin::_admin_init
	add_action('admin_init', 'hvst_scripthigh_admin_init');

	// Add Contextual Help
	//add_filter('contextual_help', 'hvst_scripthigh_settings_help', 10, 3);

} else {
	// user (front-end & others, not Admin)

	//add_shortcode('payment_form', function($attr, $content='') {
	//	$obj = new controllers\UserPaymentForm();
	//	return $obj->PaymentForm_shortcode($attr, $content);
	//});

	add_filter('wp_head', 'hvst_scripthigh_user_add_js');
	add_filter('wp_footer', 'hvst_scripthigh_user_add_js_footer');

	// Add the shortcode highlight
	add_shortcode('HighlightScriptures', 'hvst_scripthigh_shortcode_highlight');
	add_shortcode('IgnoreScriptures', 'hvst_scripthigh_shortcode_ignore');

}
