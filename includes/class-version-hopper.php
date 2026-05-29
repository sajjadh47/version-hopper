<?php
/**
 * This file contains the definition of the Version_Hopper class, which
 * is used to begin the plugin's functionality.
 *
 * @package       Version_Hopper
 * @subpackage    Version_Hopper/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks and public-facing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since    1.0.0
 */
class Version_Hopper {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var       Version_Hopper_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var       string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var       string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function __construct() {
		$this->version     = defined( 'VERSION_HOPPER_PLUGIN_VERSION' ) ? VERSION_HOPPER_PLUGIN_VERSION : '1.0.0';
		$this->plugin_name = 'version-hopper';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Version_Hopper_Loader.        Orchestrates the hooks of the plugin.
	 * - Version_Hopper_Table.         Defines a class to show a wp list table.
	 * - Version_Hopper_Upgrader_Skin. Defines a class to show theme/plugin upgrade messages.
	 * - Version_Hopper_Admin.         Defines all hooks for the admin area.
	 * - Version_Hopper_Public.        Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'includes/class-version-hopper-loader.php';

		/**
		 * The class responsible for defining a class which shows a wp list table.
		 */
		require_once VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'includes/class-version-hopper-table.php';

		/**
		 * The class responsible for defining a class which extends WP_Upgrader_Skin.
		 */
		require_once VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'includes/class-version-hopper-upgrader-skin.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'admin/class-version-hopper-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'public/class-version-hopper-public.php';

		$this->loader = new Version_Hopper_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Version_Hopper_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'plugin_action_links_' . VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_BASENAME, $plugin_admin, 'add_plugin_action_links' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );

		$this->loader->add_action( 'wp_ajax_vhversionhopper_apply_package', $plugin_admin, 'apply_package' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function define_public_hooks() {
		$plugin_public = new Version_Hopper_Public( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    Version_Hopper_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
