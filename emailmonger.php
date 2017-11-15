<?php
/*
Plugin Name: Email Monger
plugin URI:  https://github.com/andrewklimek/emailmonger
Description: Email tools
Version:     0.4.0-beta
Author:      Andrew J Klimek
Author URI:  https://github.com/andrewklimek/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Email Monger is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Email Monger is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Email Monger. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

add_action( 'admin_menu', 'emailmonger_admin_menu' );
add_action( 'phpmailer_init', 'emailmonger_smtp' );
add_action( 'admin_init', 'emailmonger_settings_smtp' );
add_action( 'admin_init', 'emailmonger_settings_regemail' );


/****
*
* Register admin pages
*
****/
function emailmonger_admin_menu() {
	add_submenu_page( 'options-general.php', 'Email', 'Email', 'edit_users', 'emailmonger', 'emailmonger_settings_page' );
	add_submenu_page( 'tools.php', 'Test Email', 'Test Email', 'edit_users', 'emailmonger-test', 'emailmonger_test' );
}



/****
*
* Settings > Email
*
****/

function emailmonger_settings_page() {
?>
<div class="wrap">
	<h2>Email Monger</h2>
	<form action="options.php" method="post">
		<?php settings_fields( 'emailmonger' ); ?>
		<?php do_settings_sections( 'emailmonger' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
<?php
}

/****
*
* Settings > Email > SMTP
*
****/

function emailmonger_settings_smtp() {

	// New Registration Section
	$section = 'emailmonger_smtp';
	$settings = get_option( $section );
	$defaults = array(
		"host" => "mail.example.com",
		"port" => "587",
		"protocol" => "tls",
		"username" => "user@example.com",
		"password" => ""
	);
	$settings = $settings ? array_merge( $defaults, array_filter( $settings) ) : $defaults;
	

	add_settings_section(
		$section,
		'SMTP',
		$section .'_callback',
		'emailmonger'
	);
	
	$field = 'host';
	add_settings_field(
		"{$section}_{$field}",
		'Host',
		'emailmonger_setting_callback_text',
		'emailmonger',
		$section,
		array( 'label_for' => "{$section}_{$field}", 'name' => "{$section}[{$field}]", 'value' => $settings[$field] )
	);
	
	$field = 'port';
	add_settings_field(
		"{$section}_{$field}",
		'Port',
		'emailmonger_setting_callback_number',
		'emailmonger',
		$section,
		array( 'label_for' => "{$section}_{$field}", 'name' => "{$section}[{$field}]", 'value' => $settings[$field] )
	);
	
	$field = 'protocol';
	add_settings_field(
		"{$section}_{$field}",
		'Protocol',
		'emailmonger_setting_callback_protocol',
		'emailmonger',
		$section,
		array( 'label_for' => "{$section}_{$field}", 'name' => "{$section}[{$field}]", 'value' => $settings[$field] )
	);
	
	$field = 'username';
	add_settings_field(
		"{$section}_{$field}",
		'Username',
		'emailmonger_setting_callback_text',
		'emailmonger',
		$section,
		array( 'label_for' => "{$section}_{$field}", 'name' => "{$section}[{$field}]", 'value' => $settings[$field] )
	);
	
	$field = 'password';
	add_settings_field(
		"{$section}_{$field}",
		'Password',
		'emailmonger_setting_callback_password',
		'emailmonger',
		$section,
		array( 'label_for' => "{$section}_{$field}", 'name' => "{$section}[{$field}]", 'value' => $settings[$field] )
	);

	register_setting( 'emailmonger', $section );
}

function emailmonger_smtp( $phpmailer ) {
	
	$settings = get_option( 'emailmonger_smtp' );
	
	// check for blank options
	if ( $settings !== array_filter($settings) ) return;
	
	$phpmailer->isSMTP();
	$phpmailer->Host = $settings['host'];
	$phpmailer->Port = $settings['port'];
	$phpmailer->SMTPSecure = $settings['protocol'];
	$phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
	//$phpmailer->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
	$phpmailer->Username = $settings['username'];
	$phpmailer->Password = $settings['password'];
	
}


/****
*
* Settings > Email > Registration Email
*
****/


function emailmonger_settings_regemail() {

	// New Registration Section
	$section = 'emailmonger_regemail';
	$settings = get_option( $section );
	$defaults = array(
		'usermessage' => "Username: [username]\r\nLogin: [login_url]\r\n\r\nTo set your password, visit the following address:\r\n[set_password_url]",
		'usersubject' => "[[sitename]] Your username and password info",

	);
	$settings = $settings ? array_merge( $defaults, array_filter( $settings) ) : $defaults;

	add_settings_section(
		$section,
		'New User Registration Email',
		$section .'_callback',
		'emailmonger'
	);
	
	$field = 'usersubject';
	add_settings_field(
		"{$section}_{$field}",
		'Subject to New User',
		'emailmonger_setting_callback_text',
		'emailmonger',
		$section,
		array( 'label_for' => "{$section}_{$field}", 'name' => "{$section}[{$field}]", 'value' => $settings[$field] )
	);

	$field = 'usermessage';
	add_settings_field(
		"{$section}_{$field}",
		'Email to New User',
		'emailmonger_setting_callback_textarea',
		'emailmonger',
		$section,
		array( 'label_for' => "{$section}_{$field}", 'name' => "{$section}[{$field}]", 'value' => $settings[$field] )
	);

	register_setting( 'emailmonger', $section );
}

/****
*
* Section & Field Callbacks
*
****/

function emailmonger_smtp_callback() {
	echo "<p>Some help text</p>";
}

function emailmonger_regemail_callback() {
	echo "<p>You can use shortcodemerge fields: <code>[sitename]</code> <code>[username]</code> <code>[email]</code> <code>[set_password_url]</code> and <code>[login_url]</code></p>";
}

function emailmonger_setting_callback_text( $args ) {
	printf(
		'<input type="text" name="%s" id="%s" value="%s" class="regular-text">',
		$args['name'],
		$args['label_for'],
		$args['value']
	);
}

function emailmonger_setting_callback_textarea( $args ) {
	printf(
		'<textarea name="%s" id="%s" rows="10" class="large-text code">%s</textarea>',
		$args['name'],
		$args['label_for'],
		$args['value']
	);
}

function emailmonger_setting_callback_protocol( $args ) {

	print "<select name='{$args['name']}' id='{$args['label_for']}'>";
	print "<option value='tls'";
	if ( $args['value'] == 'tls' ) print " selected";
	print ">TLS</option> <option value='ssl'";
	if ( $args['value'] == 'ssl' ) print " selected";
	print ">SSL</option> </select>";
}


function emailmonger_setting_callback_number( $args ) {
	printf(
		'<input type="number" name="%s" id="%s" value="%s" class="regular-text">',
		$args['name'],
		$args['label_for'],
		$args['value']
	);
}

function emailmonger_setting_callback_password( $args ) {
	printf(
		'<input type="password" name="%s" id="%s" value="%s" class="regular-text">',
		$args['name'],
		$args['label_for'],
		$args['value']
	);
}

/****
*
* Pluggable new user notification
* uses template set in Settings > Email > Registration Email
*
****/

if ( !function_exists('wp_new_user_notification') ) :
/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @since 2.0.0
 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
 * @since 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
 *
 * @global wpdb         $wpdb      WordPress database object for queries.
 * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
 *
 * @param int    $user_id    User ID.
 * @param null   $deprecated Not used (argument deprecated).
 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
 *                           string (admin only), or 'both' (admin and user). Default empty.
 */
function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
	if ( $deprecated !== null ) {
		_deprecated_argument( __FUNCTION__, '4.3.1' );
	}

	global $wpdb, $wp_hasher;
	
	$settings = get_option('emailmonger_regemail');

	$user = get_userdata( $user_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
	$blogname;
	$user->user_login;
	$user->user_email;

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	$message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notifcation.
	if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
		return;
	}

	// Generate something random for a password reset key.
	$key = wp_generate_password( 20, false );

	/** This action is documented in wp-login.php */
	do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	$search = array(
		'[sitename]',
		'[username]',
		'[email]',
		'[set_password_url]',
		'[login_url]',
	);
	$replace = array(
		$blogname,
		$user->user_login,
		$user->user_email,
		network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login'),
		wp_login_url(),
	);


	$subject = !empty ( $settings['usersubject'] ) ? $settings['usersubject'] : "[[sitename]] Your username and password info";
	$subject = str_replace($search, $replace, $subject);

	$message = !empty ( $settings['usermessage'] ) ? $settings['usermessage'] : "Username: [username]\r\nLogin: [login_url]\r\n\r\nTo set your password, visit the following address:\r\n[set_password_url]";
	$message = str_replace($search, $replace, $message );


	wp_mail($user->user_email, $subject, $message);
}
endif;


/****
*
* Tools > Test Email
*
****/

function emailmonger_test() {

	echo '
	<style>
		#emailmonger label {
			width: 16em;
			float: left;
		}
		#emailmonger .text {
			width: 30em;
		}
		</style>
	<div id="emailmonger" class="wrap">
	';

	if( !empty( $_POST["emailmonger_to"] ) )
	{
		$headers = emailmonger_send( $_POST["emailmonger_to"] );
		echo '<div class="updated">Email has been sent!</div>';
	}

	echo '
	<h2>Email Monger</h2>

	<form action="tools.php?page=emailmonger-test" method="post">
	<p>SMTP server: ' . ini_get("SMTP") . '</p>
		<p><label for="emailmonger_mime">MIME Version</label>
		<input type="text" name="emailmonger_mime" id="emailmonger_mime" value="';
	if ( isset( $_POST["emailmonger_mime"] ) ) {
		echo $_POST["emailmonger_mime"];
	} else {
		echo '1.0';
	}
	echo '" /></p>
		<p><label for="emailmonger_type">Content type</label>
		<input type="text" name="emailmonger_type" id="emailmonger_type" value="';
	if ( isset( $_POST["emailmonger_type"] ) ) {
		echo $_POST["emailmonger_type"];
	} else {
		echo 'text/plain; charset=' . get_option('blog_charset');
	}
	echo '" class="text"  /></p>';
	/*			<p><label for="emailmonger_break_n">Header line break type</label>
		<input type="radio" name="emailmonger_break" id="emailmonger_break_n" value="n"';
	if ( empty( $_POST["emailmonger_break"] ) || $_POST["emailmonger_break"] == 'n' ) {
		echo ' checked="checked"';
	}
	echo ' /> \n
		<input type="radio" name="emailmonger_break" id="emailmonger_break_rn" value="rn"';
	if ( isset( $_POST["emailmonger_break"] ) && $_POST["emailmonger_break"] == 'rn' ) {
		echo ' checked="checked"';
	}
	echo ' /> \r\n</p>*/
	echo '<p><label for="emailmonger_from">From</label>
		<input type="text" name="emailmonger_from" id="emailmonger_from" value="';
	if ( !empty( $_POST["emailmonger_from"] ) ) {
		echo $_POST["emailmonger_from"];
	} else {
		echo get_option( 'admin_email' );
	}
	echo '" class="text"  /></p>
		<p><label for="emailmonger_to">To</label>
		<textarea name="emailmonger_to" id="emailmonger_to" cols="30" rows="4" class="text">';
	if ( !empty( $_POST["emailmonger_to"] ) ) {
		echo $_POST["emailmonger_to"];
	}
	echo '</textarea></p>
		<p><label for="emailmonger_cc">CC</label>
		<textarea name="emailmonger_cc" id="emailmonger_cc" cols="30" rows="4" class="text">';
	if ( !empty( $_POST["emailmonger_cc"] ) ) {
		echo $_POST["emailmonger_cc"];
	}
	echo '</textarea></p>
		<p><label for="emailmonger_bcc">BCC</label>
		<textarea name="emailmonger_bcc" id="emailmonger_bcc" cols="30" rows="4" class="text">';
	if ( !empty( $_POST["emailmonger_bcc"] ) ) {
		echo $_POST["emailmonger_bcc"];
	}
	echo '</textarea></p>
		<p><label for="emailmonger_subject">Subject</label>
		<input type="text" name="emailmonger_subject" id="emailmonger_subject" value="';
	if ( !empty( $_POST["emailmonger_subject"] ) ) {
		echo stripslashes( $_POST["emailmonger_subject"] );
	} else {
		echo 'Test email from '.get_bloginfo("name");

	}
	echo '" class="text"  /></p>
		<p><label for="emailmonger_message">Message</label>
		<textarea name="emailmonger_message" id="emailmonger_message" cols="30" rows="4" class="text">';
	if ( !empty( $_POST["emailmonger_message"] ) ) {
		echo stripslashes( $_POST["emailmonger_message"] );
	} else {
		echo 'This a test email sent by your WordPress site: '. get_bloginfo('url');
	}
	echo '</textarea></p>
	<input type="submit" name="emailmonger_go" id="emailmonger_go" class="button-primary" value="Send test email" /></p>
	</form>
	</div>
	';

}

// send a test email
function emailmonger_send($to, $headers = array() ) {
	$subject = stripslashes( $_POST["emailmonger_subject"] ) . date(' [H:i:s T]');
	$message = stripslashes( $_POST["emailmonger_message"] );
	// 	$break = $_POST["emailmonger_break"] === 'rn' ? chr( 13 ) . chr( 10 ) : chr( 10 );
	$headers[] = "MIME-Version: " . trim( $_POST["emailmonger_mime"] );
	$headers[] = "From: " . trim( $_POST["emailmonger_from"] );
	$headers[] = "Cc: " . trim( $_POST["emailmonger_cc"] );
	$headers[] = "Bcc: " . trim( $_POST["emailmonger_bcc"] );
	$headers[] = "Content-Type: " . trim( $_POST["emailmonger_type"] );

	wp_mail( $to, $subject, $message, $headers );
	return $headers;
}

