<?php
/**
 * WP_Upgrader_Skin is not loaded automatically so we need to load it manually.
 *
 * @package    Version_Hopper
 */

if ( ! class_exists( 'WP_Upgrader_Skin' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
}

/**
 * Custom Upgrader Skin for Version Hopper.
 *
 * Displays upgrade/install progress inline on the admin page, similar to WordPress's
 * "Upload Plugin" or "Upload Theme" screens.
 *
 * Handles feedback messages, errors, and flushes output for real-time progress display.
 *
 * @version       1.0.0
 * @package       Version_Hopper
 * @subpackage    Version_Hopper/includes
 */
class Version_Hopper_Upgrader_Skin extends WP_Upgrader_Skin {
	/**
	 * Store all feedback messages.
	 *
	 * @var       array
	 * @access    public
	 * @since     1.0.0
	 */
	public $messages = array();

	/**
	 * Output feedback messages during the upgrade/install process.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     string|WP_Error $message Feedback message or WP_Error object.
	 * @param     mixed           ...$args Optional additional arguments for vsprintf formatting.
	 * @return    void
	 */
	public function feedback( $message, ...$args ) {
		if ( is_wp_error( $message ) ) {
			$message = $message->get_error_message();
		} elseif ( isset( $this->upgrader->strings[ $message ] ) ) {
			$message = $this->upgrader->strings[ $message ];
			if ( $args ) {
				$message = vsprintf( $message, $args );
			}
		} elseif ( $args ) {
			$message = vsprintf( $message, $args );
		}

		$this->messages[] = $message;
	}

	/**
	 * Output header for upgrader process.
	 *
	 * Empty because we handle inline output without extra markup.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function header() {}

	/**
	 * Output footer for upgrader process.
	 *
	 * Empty because we handle inline output without extra markup.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function footer() {}

	/**
	 * Display errors encountered during upgrade/install.
	 *
	 * @param     string|WP_Error $error Error message or WP_Error object.
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function error( $error ) {
		$this->messages[] = is_wp_error( $error ) ? $error->get_error_message() : (string) $error;
	}
}
