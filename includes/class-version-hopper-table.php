<?php
/**
 * WP_List_Table is not loaded automatically so we need to load it manually.
 *
 * @package    Version_Hopper
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Abstract base class for plugins/themes tables.
 *
 * This class extends WP_List_Table to provide a foundation for creating
 * tables that display plugins/themes information in the WordPress admin.
 *
 * @version       1.0.0
 * @package       Version_Hopper
 * @subpackage    Version_Hopper/includes
 */
class Version_Hopper_Table extends WP_List_Table {
	/**
	 * Type: Plugins or Themes
	 *
	 * @var       string
	 * @access    private
	 */
	private $type;

	/**
	 * Constructor for the class.
	 *
	 * This constructor sets up the class properties, including the singular and
	 * plural labels for the list table.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     string $type Type: Plugins or Themes.
	 * @return    void
	 */
	public function __construct( $type ) {
		parent::__construct(
			array(
				'singular' => __( 'Version Hopper', 'version-hopper' ),
				'plural'   => __( 'Version Hopper', 'version-hopper' ),
				'ajax'     => false,
			)
		);

		$this->type = $type;
	}

	/**
	 * Get the columns for the table.
	 *
	 * This method returns an array defining the columns to be displayed in the table.
	 * The keys of the array are the column IDs, and the values are the column labels.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array An associative array of column IDs and labels.
	 */
	public function get_columns() {
		return array(
			'name'     => __( 'Name', 'version-hopper' ),
			'slug'     => __( 'Slug', 'version-hopper' ),
			'author'   => __( 'Author', 'version-hopper' ),
			'current'  => __( 'Current Version', 'version-hopper' ),
			'versions' => __( 'Versions', 'version-hopper' ),
		);
	}

	/**
	 * Default column handler.
	 *
	 * This method handles the display of data for columns that don't have a
	 * specific column rendering method defined (e.g., column_title()).  It
	 * retrieves the value from the `$item` array using the column name.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     array  $item        The data for the current table row.
	 * @param     string $column_name The name of the column being rendered.
	 * @return    mixed               The data to display for the column, or an empty string if the column is not found.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'current':
				return '<strong>' . esc_html( $item[ $column_name ] ?? '' ) . '</strong>';

			case 'author':
			case 'slug':
				return esc_html( $item[ $column_name ] ?? '' );

			case 'versions':
				$type    = $item['type']; // plugin|theme.
				$slug    = $item['slug'];
				$file    = $item['file'];
				$output  = '<div class="vhversionhopper_versions">';
				$output .= '<form>';
				$output .= '<input type="hidden" name="vhversionhopper_type" value="' . esc_attr( $type ) . '">';
				$output .= '<input type="hidden" name="vhversionhopper_slug" value="' . esc_attr( $slug ) . '">';
				$output .= '<input type="hidden" name="vhversionhopper_file" value="' . esc_attr( $file ) . '">';
				$output .= '<select name="vhversionhopper_version" required>';
				$output .= '<option value="">' . esc_html__( 'Select Version', 'version-hopper' ) . '</option>';

				if ( ! empty( $item['versions'] ) ) {
					foreach ( $item['versions'] as $version => $download_link ) {
						$output .= sprintf(
							'<option value="%1$s">%1$s</option>',
							esc_attr( $version ),
						);
					}
				}

				$output .= '</select>';
				$output .= '<button class="button button-primary apply_package" type="button">' . esc_html__( 'Apply', 'version-hopper' ) . '</button>';
				$output .= '</form>';
				$output .= '</div>';

				return $output;

			default:
				return '';
		}
	}

	/**
	 * Message to display when no items are found.
	 *
	 * This method outputs a user-friendly message when there are no items
	 * to display in the table.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function no_items() {
		esc_html_e( 'No items are available.', 'version-hopper' );
	}

	/**
	 * Prepare the items for display.
	 *
	 * This method fetches the data for the table, sets up the column headers,
	 * and configures pagination. It calls the abstract method {@see get_items()}
	 * to retrieve the actual data.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function prepare_items() {
		$all_items             = $this->get_items();
		$columns               = $this->get_columns();
		$per_page              = $this->get_items_per_page( 'logs_per_page', 50 );
		$current_page          = $this->get_pagenum();
		$total_items           = count( $all_items );
		$this->_column_headers = array( $columns, array(), array() );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		$this->items = array_slice( $all_items, ( $current_page - 1 ) * $per_page, $per_page );
	}

	/**
	 * Retrieve all installed plugins and themes as a combined list.
	 *
	 * Each item contains:
	 * - name
	 * - slug
	 * - type ("plugin" or "theme")
	 * - version
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array
	 */
	public function get_items() {
		$items = array();

		if ( 'plugins' === $this->type ) {
			// Plugins.
			$plugins = Version_Hopper_Admin::get_installed_plugins();

			foreach ( $plugins as $slug => $plugin ) {
				$items[] = array(
					'name'     => $plugin['name'],
					'slug'     => $slug,
					'author'   => $plugin['author'],
					'type'     => 'plugin',
					'current'  => $plugin['current'],
					'versions' => $plugin['versions'] ?? array(),
					'file'     => $plugin['plugin_file'] ?? $slug,
				);
			}
		} elseif ( 'themes' === $this->type ) {
			// Themes.
			$themes = Version_Hopper_Admin::get_installed_themes();

			foreach ( $themes as $slug => $theme ) {
				$items[] = array(
					'name'     => $theme['name'],
					'slug'     => $slug,
					'author'   => $theme['author'],
					'type'     => 'theme',
					'current'  => $theme['current'],
					'versions' => $theme['versions'] ?? array(),
					'file'     => $slug,
				);
			}
		}

		return $items;
	}
}
