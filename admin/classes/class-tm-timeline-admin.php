<?php
/**
 * Provide admin-related functionality.
 *
 * @package    Tm_Timeline
 * @subpackage Tm_Timeline_Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2017 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Register class if it does not exists already.
 */
if ( ! class_exists( 'Tm_Timeline_Admin' ) ) {

	// Load Tm_Timeline if class was not initialized yet.
	if ( ! class_exists( 'Tm_Timeline' ) ) {
		require tm_timeline_plugin_path( 'classes/class-tm-timeline.php' );
	}

	// Load Tm_Timeline_View if class was not initialized yet.
	if ( ! class_exists( 'Tm_Timeline_View' ) ) {
		require tm_timeline_plugin_path( 'classes/class-tm-timeline-view.php' );
	}

	/**
	 * Class contains admin-related functionality.
	 */
	class Tm_Timeline_Admin {

		/**
		 * Determine if initialization is required.
		 *
		 * @var bool
		 */
		private static $_initialized = false;

		/**
		 * Views renderer.
		 *
		 * @var Tm_Timeline_View Timeline view instance
		 */
		private static $_view;

		/**
		 * Initialize plugin admin.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Changed js/css registration - added `Tm_Timeline_Admin::assets` method.
		 */
		public static function initialize() {

			// Initialize only if not initialized already
			if ( false === self::$_initialized ) {

				$views_path  = tm_timeline_plugin_path( 'admin/views' );
				self::$_view = new Tm_Timeline_View( $views_path );

				add_action(
					'admin_menu', array(
						'Tm_Timeline_Admin',
						'menu',
					)
				);

				add_action(
					'add_meta_boxes', array(
						'Tm_Timeline_Admin',
						'register_meta_boxes',
					)
				);

				add_action(
					'save_post', array(
						'Tm_Timeline_Admin',
						'save_post_event_date',
					),
					10, 2
				);

				add_filter(
					'manage_edit-timeline_post_columns', array(
						'Tm_Timeline_Admin',
						'add_table_columns',
					)
				);

				add_action(
					'manage_timeline_post_posts_custom_column', array(
						'Tm_Timeline_Admin',
						'add_table_columns_data',
					)
				);

				add_filter(
					'manage_edit-timeline_post_sortable_columns', array(
						'Tm_Timeline_Admin',
						'register_sortable_table_columns',
					)
				);

				add_action(
					'admin_enqueue_scripts', array(
						'Tm_Timeline_Admin',
						'assets',
					)
				);

				self::$_initialized = true;
			}
		}

		/**
		 * Initialize admin menu.
		 */
		public static function menu() {
			global $submenu;

			$url = 'edit.php?post_type=timeline_post';

			// Rename Timeline sub-menu item into Posts
			$submenu[ $url ][5][0] = esc_html__( 'Posts', 'tm-timeline' );

			// Add shortcode generator page to the Timeline sub-menu
			add_submenu_page(
				$url,
				esc_html__( 'TM Timeline', 'tm-timeline' ),
				esc_html__( 'Settings', 'tm-timeline' ),
				'manage_options',
				Tm_Timeline_Admin::get_menu_slug(),
				array(
					'Tm_Timeline_Admin',
					'handle_settings',
				)
			);
		}

		/**
		 * Retrieve a slug for setting page.
		 *
		 * @since 1.0.5
		 */
		public static function get_menu_slug() {
			return apply_filters( 'tm_timeline_admin_menu_slug', 'timeline_create' );
		}

		/**
		 * Attach admin javascript and styleshet.
		 *
		 * @since 1.0.5
		 */
		public static function assets( $hook ) {
			$menu_slug = Tm_Timeline_Admin::get_menu_slug();

			if ( false === stripos( $hook, $menu_slug ) ) {
				return;
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script(
				'tm-timeline-admin-js',
				tm_timeline_plugin_url( "/admin/js/tm-timeline{$suffix}.js" ),
				array( 'jquery' ),
				TM_TIMELINE_VERSION,
				true
			);

			wp_enqueue_style(
				'tm-timeline-admin-css',
				tm_timeline_plugin_url( "/admin/css/tm-timeline{$suffix}.css" ),
				array(),
				TM_TIMELINE_VERSION
			);
		}

		/**
		 * Register `post-event-date` custom field. Used to store date value
		 */
		public static function register_meta_boxes() {
			add_meta_box(
				'post-event-date',
				esc_html__( 'Timeline Date', 'tm-timeline' ),
				array(
					'Tm_Timeline_Admin',
					'render_post_event_date_meta_box',
				),
				'timeline_post',
				'side',
				'high'
			);
		}

		/**
		 * Render the `post-event-date` custom field.
		 *
		 * @param WP_Post $post WordPress post.
		 * @param array   $atts Metabox options.
		 */
		public static function render_post_event_date_meta_box(
			WP_Post $post,
			array $atts
		) {
			// Gather the values.
			$id    = $atts['id'];
			$title = $atts['title'];
			$value = get_post_meta( $post->ID, $id, true );
			$nonce = wp_create_nonce( basename( __FILE__ ) );

			self::init_meta_box_assets();

			// Render & print the view
			print self::$_view->render(
				'post-event-date', array(
					'id'          => $id,
					'title'       => $title,
					'value'       => date( 'F j, Y', empty( $value ) ? time() : intval( $value ) ),
					'nonce'       => $nonce,
					'date_format' => 'MM d, yy',
				)
			);
		}

		/**
		 * Initialize metabox assets.
		 */
		public static function init_meta_box_assets() {
			global $wp_scripts;

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Attach the assets for jquery's datepicker.
			wp_enqueue_script( 'jquery-ui-datepicker' );

			$jqui_core = $wp_scripts->query( 'jquery-ui-core' );
			$version  = '1.11.4';

			if ( property_exists( $jqui_core, 'ver' ) ) {
				$version = $jqui_core->ver;
			}

			$jqui_theme = 'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $version
				. "/themes/smoothness/jquery-ui{$suffix}.css";

			wp_enqueue_style( 'jquery-ui-core', $jqui_theme, false, $version );

			// Attach admin styles for the metabox.
			wp_enqueue_style(
				'tm-timeline-post-event-date',
				tm_timeline_plugin_url( "/admin/css/tm-timeline{$suffix}.css" ),
				array( 'jquery-ui-core' ),
				TM_TIMELINE_VERSION
			);
		}

		/**
		 * Save the `post-event-date` custom field value
		 *
		 * @param int $post_id WP post id.
		 *
		 * @return int
		 */
		public static function save_post_event_date( $post_id, $post ) {

			// Check if values are passed
			if ( false === isset( $_POST['post-event-date-nonce'] ) ||
				false === isset( $_POST['post-event-date'] )
			) {
				return $post_id;
			}

			// Validate the nonce, post type & permissions
			$nonce = $_POST['post-event-date-nonce'];

			if ( false === wp_verify_nonce( $nonce, basename( __FILE__ ) ) ||
				( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
				( 'timeline_post' == $_POST['post_type'] &&
					false === current_user_can( 'edit_post', $post_id ) )
			) {
				return $post_id;
			}

			// Sanitize and convert the value into the timestamp
			$post_event_date = sanitize_text_field( $_POST['post-event-date'] );
			$post_event_date = strtotime( $post_event_date );

			// Store the value
			update_post_meta( $post_id, 'post-event-date', $post_event_date );

			return $post_id;
		}

		/**
		 * Add `post-event-date` column to the posts listing
		 *
		 * @param array $columns Table columns.
		 *
		 * @return array
		 */
		public static function add_table_columns( array $columns ) {
			$post_event_date_column = array(
				'post-event-date' => esc_html__( 'Timeline Date', 'tm-timeline' ),
			);

			unset( $columns['date'] );

			return array_slice( $columns, 0, 2, true ) + $post_event_date_column + array_slice( $columns, 2, null, true );
		}

		/**
		 * Return the data associated with the column
		 *
		 * @param String $column Current column name.
		 */
		public static function add_table_columns_data( $column = '' ) {
			global $post;

			if ( 'post-event-date' === $column ) {
				$post_event_date = get_post_meta( $post->ID, 'post-event-date', true );

				if ( ! empty( $post_event_date ) ) {
					echo date( 'F j, Y', $post_event_date );
				}
			}
		}

		/**
		 * Set `post-event-date` column as sortable
		 *
		 * @param array $columns Table columns.
		 *
		 * @return array
		 */
		public static function register_sortable_table_columns( array $columns ) {
			$columns['post-event-date'] = array(
				esc_html__( 'Timeline Date', 'tm-timeline' )
			);

			return $columns;
		}

		/**
		 * Render & output the shortcode generator page (also known as `Settings`).
		 *
		 * @throws Exception If view file was not found or renderer has no access to read it.
		 */
		public static function handle_settings() {
			// Get available layouts & date formats.
			$timeline_layouts     = Tm_Timeline::get_supported_layouts();
			$timeline_date_format = Tm_Timeline::get_supported_date_formats();

			// Get tags list
			$tags = get_terms(
				'timeline_post_tag', array(
					'hide_empty' => false,
				)
			);

			// Render the `Settings` page view
			$content = self::$_view->render(
				'settings', array(
					'tags'                 => $tags,
					'timeline_layouts'     => $timeline_layouts,
					'timeline_date_format' => $timeline_date_format,
					'sort_orders' => array(
						'ASC'  => esc_html__( 'Ascending', 'tm-timeline' ),
						'DESC' => esc_html__( 'Descending', 'tm-timeline' ),
					),
				)
			);

			// Check if any exceptions returned and throw if any
			if ( $content instanceof Exception ) {
				throw $content;
			}

			// Output the rendering result
			print $content;
		}
	}
}
