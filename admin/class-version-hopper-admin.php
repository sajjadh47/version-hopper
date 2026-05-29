<?php
/**
 * This file contains the definition of the Version_Hopper_Admin class, which
 * is used to load the plugin's admin-specific functionality.
 *
 * @package       Version_Hopper
 * @subpackage    Version_Hopper/admin
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    1.0.0
 */
class Version_Hopper_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     string $plugin_name The name of this plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		$current_screen = get_current_screen();

		// check if current page is plugin settings page.
		if ( 'toplevel_page_version-hopper-plugins' === $current_screen->id || 'version-hopper_page_version-hopper-themes' === $current_screen->id ) {
			wp_enqueue_style( $this->plugin_name, VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();

		// check if current page is plugin settings page.
		if ( 'toplevel_page_version-hopper-plugins' === $current_screen->id || 'version-hopper_page_version-hopper-themes' === $current_screen->id ) {
			wp_enqueue_script( $this->plugin_name, VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'VersionHopper',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'nonce'              => wp_create_nonce( 'vhversionhopper_apply_package_nonce' ),
					'versionNotSelected' => __( 'Please choose a version before applying.', 'version-hopper' ),
					'applying'           => __( 'Switching version...', 'version-hopper' ),
					'failed'             => __( 'Unable to switch to the selected version.', 'version-hopper' ),
					'btn'                => __( 'Apply', 'version-hopper' ),
					'confirm'            => __( 'Are you sure you want to switch to this version?', 'version-hopper' ),
				)
			);
		}
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     array $links The existing array of plugin action links.
	 * @return    array $links The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=version-hopper-plugins' ) ), __( 'Settings', 'version-hopper' ) );

		return $links;
	}

	/**
	 * Adds the plugin settings page to the WordPress dashboard menu.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Version Hopper', 'version-hopper' ),
			__( 'Version Hopper', 'version-hopper' ),
			'manage_options',
			'version-hopper-plugins',
			array( $this, 'submenu_plugins_page' ),
			'dashicons-update',
		);

		add_submenu_page(
			'version-hopper-plugins',
			__( 'Plugins', 'version-hopper' ),
			__( 'Plugins', 'version-hopper' ),
			'manage_options',
			'version-hopper-plugins',
			array( $this, 'submenu_plugins_page' ),
		);

		add_submenu_page(
			'version-hopper-plugins',
			__( 'Themes', 'version-hopper' ),
			__( 'Themes', 'version-hopper' ),
			'manage_options',
			'version-hopper-themes',
			array( $this, 'submenu_themes_page' ),
		);
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function submenu_plugins_page() {
		$plugins_list_table = new Version_Hopper_Table( 'plugins' );
		$plugins_list_table->prepare_items();

		echo '<div class="wrap vhversionhopper_hopper">';
			$plugins_list_table->display();
		echo '</div>';
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function submenu_themes_page() {
		$themes_list_table = new Version_Hopper_Table( 'themes' );
		$themes_list_table->prepare_items();

		echo '<div class="wrap vhversionhopper_hopper">';
			$themes_list_table->display();
		echo '</div>';
	}

	/**
	 * Apply a specific version package.
	 *
	 * Modifies the transient so WordPress thinks there's a new version at a custom URL.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function apply_package() {
		check_ajax_referer( 'vhversionhopper_apply_package_nonce', 'nonce' );

		if ( ! current_user_can( 'update_plugins' ) && ! current_user_can( 'update_themes' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'version-hopper' ) );
		}

		$type    = isset( $_POST['vhversionhopper_type'] ) ? sanitize_text_field( wp_unslash( $_POST['vhversionhopper_type'] ) ) : '';
		$slug    = isset( $_POST['vhversionhopper_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['vhversionhopper_slug'] ) ) : '';
		$version = isset( $_POST['vhversionhopper_version'] ) ? sanitize_text_field( wp_unslash( $_POST['vhversionhopper_version'] ) ) : '';
		$file    = isset( $_POST['vhversionhopper_file'] ) ? sanitize_text_field( wp_unslash( $_POST['vhversionhopper_file'] ) ) : '';

		if ( ! $type || ! $slug || ! $version || ! $file ) {
			wp_send_json_error( __( 'Missing data.', 'version-hopper' ) );
		}

		// Construct download link.
		if ( strtolower( $version ) === 'trunk' || empty( $version ) ) {
			// Always latest from trunk.
			$package = "https://downloads.wordpress.org/{$type}/{$slug}.zip";
		} else {
			// Specific version.
			$package = "https://downloads.wordpress.org/{$type}/{$slug}.{$version}.zip";
		}

		if ( 'plugin' === $type ) {
			if ( ! class_exists( 'WP_Upgrader' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			// Ensure plugin.php is loaded for get_plugins().
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$transient = get_site_transient( 'update_plugins' );
			if ( ! is_object( $transient ) ) {
				$transient = new stdClass();
			}

			if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
				$transient->response = array();
			}

			// Find the real plugin file (like "folder/plugin.php").
			foreach ( array_keys( get_plugins() ) as $plugin_file ) {
				if ( dirname( $plugin_file ) === $slug ) {
					// Merge with existing entry if exists.
					$existing = isset( $transient->response[ $plugin_file ] )
						? (array) $transient->response[ $plugin_file ]
						: array();

					$transient->response[ $plugin_file ] = (object) array_merge(
						$existing,
						array(
							'slug'        => $slug,
							'new_version' => $version,
							'package'     => $package,
						)
					);
					$transient->checked[ $plugin_file ]  = '0.0.1'; // Force older version.
					break;
				}
			}

			set_site_transient( 'update_plugins', $transient );

			$skin           = new Version_Hopper_Upgrader_Skin();
			$upgrader       = new Plugin_Upgrader( $skin );
			$skin->upgrader = $upgrader;
			$upgrader->upgrade( $file );

			// Return messages as JSON.
			wp_send_json_success(
				array(
					'messages' => $skin->messages,
				)
			);

		} elseif ( 'theme' === $type ) {
			if ( ! class_exists( 'WP_Upgrader' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			$transient = get_site_transient( 'update_themes' );
			if ( ! is_object( $transient ) ) {
				$transient = new stdClass();
			}

			if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
				$transient->response = array();
			}

			$existing = isset( $transient->response[ $slug ] )
				? (array) $transient->response[ $slug ]
				: array();

			$transient->response[ $slug ] = array_merge(
				$existing,
				array(
					'theme'       => $slug,
					'new_version' => $version,
					'package'     => $package,
				)
			);
			$transient->checked[ $slug ]  = '0.0.1'; // Force older version.

			set_site_transient( 'update_themes', $transient );

			$skin           = new Version_Hopper_Upgrader_Skin();
			$upgrader       = new Theme_Upgrader( $skin );
			$skin->upgrader = $upgrader;
			$upgrader->upgrade( $file );

			// Return messages as JSON.
			wp_send_json_success(
				array(
					'messages' => $skin->messages,
				)
			);
		}

		wp_send_json_error( __( 'Something went wrong! Please try again.', 'version-hopper' ) );
	}

	/**
	 * Retrieve all installed plugins.
	 *
	 * Returns an associative array of plugin slugs and names.
	 * The slug is derived from the directory name of the plugin file.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    array $plugins Associative array of plugin slugs as keys and plugin names as values.
	 */
	public static function get_installed_plugins() {
		// Ensure plugin.php is loaded for get_plugins().
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		// Load any cached API data.
		$cache_key   = 'vhversionhopper_plugins_api_cache';
		$cached      = get_transient( $cache_key );
		$api_data    = ( is_array( $cached ) ) ? $cached : array();
		$api_updated = false;
		$_plugins    = get_plugins();
		$plugins     = array();

		foreach ( $_plugins as $path => $plugin ) {
			$plugin_file      = $path;
			$slug             = dirname( $path );
			$plugins[ $slug ] = array(
				'name'        => $plugin['Name'],
				'author'      => $plugin['Author'],
				'current'     => $plugin['Version'],
				'plugin_file' => $plugin_file,
			);

			// Use cached API result if available.
			if ( isset( $api_data[ $slug ] ) ) {
				$plugins[ $slug ]['versions'] = $api_data[ $slug ];
				continue;
			}

			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'versions' => true,
					),
				)
			);

			if ( ! is_wp_error( $api ) && ! empty( $api->versions ) ) {
				$plugins[ $slug ]['versions'] = (array) $api->versions;
				$api_data[ $slug ]            = (array) $api->versions;
				$api_updated                  = true;
			} else {
				unset( $plugins[ $slug ] );
			}
		}

		// Cache only API data for 24 hours.
		if ( $api_updated ) {
			set_transient( $cache_key, $api_data, DAY_IN_SECONDS );
		}

		return $plugins;
	}

	/**
	 * Retrieve all installed themes.
	 *
	 * Returns an associative array of theme slugs and names.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    array $themes Associative array of theme slugs as keys and theme names as values.
	 */
	public static function get_installed_themes() {
		if ( ! function_exists( 'themes_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/themes.php';
		}

		// Load any cached API data.
		$cache_key   = 'vhversionhopper_themes_api_cache';
		$cached      = get_transient( $cache_key );
		$api_data    = ( is_array( $cached ) ) ? $cached : array();
		$api_updated = false;
		$_themes     = wp_get_themes();
		$themes      = array();

		foreach ( $_themes as $slug => $theme ) {
			$name            = $theme->get( 'Name' );
			$themes[ $slug ] = array(
				'name'    => $name,
				'author'  => $theme->get( 'Author' ),
				'current' => $theme->get( 'Version' ),
			);

			// Use cached API result if available.
			if ( isset( $api_data[ $slug ] ) ) {
				$themes[ $slug ]['versions'] = $api_data[ $slug ];
				continue;
			}

			$api = themes_api(
				'theme_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'versions' => true,
					),
				)
			);

			if ( ! is_wp_error( $api ) && ! empty( $api->versions ) ) {
				$themes[ $slug ]['versions'] = (array) $api->versions;
				$api_data[ $slug ]           = (array) $api->versions;
				$api_updated                 = true;
			} else {
				unset( $themes[ $slug ] );
			}
		}

		// Cache for 24 hours.
		if ( $api_updated ) {
			set_transient( $cache_key, $api_data, DAY_IN_SECONDS );
		}

		return $themes;
	}
}
