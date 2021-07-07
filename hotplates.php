<?php
/*
Plugin Name: Hot Plates
Description: Create a sitewide notificaion bar
Version: 0.1
Author: Richard Muscat
Author URI: https://wphotplates.com
*/

register_activation_hook( __FILE__, array( 'HotPlates', 'on_activation' ) );

new HotPlates;

class HotPlates {
	var $slug 		= 'hotplates';
	var $donation 	= "https://wphotplates.com";
	var $html 		= '<div class="hotplates-wrap">{content}</div>';


	/* Initialise the class	*/
	function __construct() {
		add_action( 'admin_menu', array( $this,'admin_menu' ) );
		add_action( 'wp_body_open', array( $this,'rewrite' ), 1 );
		add_action( 'admin_init', array( $this,'save' ) );
	}


	/* Run on plugin activation */
	function on_activation() {
		$default_content = "Start your next adventure! <a href='http://wphotplates.com'>Apply now</a> →";
		$default_style = ".hotplates-wrap {\r\n\tbackground-color: #cd2653;\r\n\tpadding: 15px 0;\r\n\tfont-size: 16px;\r\n\ttext-align: center;\r\n\tcolor: #fff;\r\n}\r\n\r\n.hotplates-wrap a {\r\n\tcolor: #fff;\r\n\ttext-decoration: underline;\r\n}\r\n\r\n.hotplates-wrap a:hover {\r\n\tcolor: #fff;\r\n\ttext-decoration: underline;\r\n}\r\n";

		if ( !get_option( 'hotplates_notif_content' ) ) {
			add_option( 'hotplates_notif_content', $default_content );
		}

		if ( !get_option( 'hotplates_notif_style' ) ) {
			add_option( 'hotplates_notif_style', $default_style );
		}

		if ( !get_option( 'hotplates_notif_enabled' ) ) {
			add_option( 'hotplates_notif_enabled', '0' );
		}

		if (false) {
			delete_option( 'hotplates_notif_content' );
			delete_option( 'hotplates_notif_style' );
			delete_option( 'hotplates_notif_enabled' );
		}
	}
	

	/* Add HP to WP menu */
	function admin_menu() {
		add_menu_page(
			'Hot Plates',
			'Hot Plates',
			'administrator',
			$this->slug,
			array($this, 'options'),
			'dashicons-insert'
		);
	}


	/* Render the options page and load stored settings */
	function options() {
		?>
		<div class="wrap">
			<h2>Hot Plates</h2>
			<form method="post" action="<?php echo admin_url( 'admin.php?page=' . $this->slug, __FILE__ ); ?>"> 

				<p>This plugin will display a notification bar across the top of your site. If you find this useful <a href="<?php echo $this->donation; ?>" target="_blank">please make a donation</a>.<br><br></p>
				
				<h3>Content</h3>
				<p>Plain text, emojis, symbols, and HTML allowed.</p>
				<textarea id="hotplates_notif_content" name="hotplates_notif_content" rows="3" cols="20" class="large-text code"><?php echo stripslashes( get_option('hotplates_notif_content') ); ?></textarea>

				<br><br>
				<h3>Style</h3>
				<textarea id="hotplates_notif_style" name="hotplates_notif_style" rows="10" cols="20" class="large-text code"><?php echo get_option('hotplates_notif_style'); ?></textarea>
		
				<br><br>

				<?php
					if ( get_option( 'hotplates_notif_enabled' ) == 1 ) { ?>
						<input type="checkbox" id="hotplates_notif_enabled" name="hotplates_notif_enabled" checked ><label for="hotplates_notif_enabled"> <strong>Enable notification bar (sitewide)</strong></label>
					<?php } else { ?>
						<input type="checkbox" id="hotplates_notif_enabled" name="hotplates_notif_enabled" ><label for="hotplates_notif_enabled"> <strong>Enable notification bar (sitewide)</strong></label>
					<?php } ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}


	/* Save settings */
	function save () {
		if ( isset( $_POST['submit'] ) ) {
			$post_content = $_POST['hotplates_notif_content'];
			$post_style   = $_POST['hotplates_notif_style'];
			$post_enabled = $_POST['hotplates_notif_enabled'];

			update_option( 'hotplates_notif_content', $post_content );
			update_option( 'hotplates_notif_style', $post_style );

			if ( "on" == $post_enabled ) {
				update_option( 'hotplates_notif_enabled', "1" );
			} else {
				update_option( 'hotplates_notif_enabled', "0" );
			}
		}		
	}


	/* Insert the notification bar */
	function rewrite() {
		if ( get_option( 'hotplates_notif_enabled' ) == "1" ) {
			$notification_bar = "<style type='text/css'>" . get_option( 'hotplates_notif_style' ) . "</style>" . $this->html;
			$notification_bar =  str_replace( "{content}", stripslashes( get_option( 'hotplates_notif_content' ) ), $notification_bar );
			echo $notification_bar;
		}
	}
}


