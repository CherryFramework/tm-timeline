<?php
/**
 * Provide front-end related functionality.
 *
 * @package    Tm_Timeline
 * @subpackage Tm_Timeline
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2017 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Register class if it does not exists already.
 */
if ( ! class_exists( 'Tm_Timeline' ) ) {

	// Load Tm_Timeline_View if class was not initialized yet.
	if ( ! class_exists( 'Tm_Timeline_View' ) ) {
		require tm_timeline_plugin_path( 'classes/class-tm-timeline-view.php' );
	}

	/**
	 * Class contains front-end related functionality.
	 */
	class Tm_Timeline {

		/**
		 * Determine if initialization is required
		 *
		 * @var bool Initialized flag
		 */
		private static $_initialized = false;

		/**
		 * Views renderer.
		 *
		 * @var Tm_Timeline_View View renderer instance
		 */
		private static $_view;

		/**
		 * Shortcode tag.
		 *
		 * @since 1.1.0
		 * @var string
		 */
		private static $shortcode_tag = 'tm-timeline';

		/**
		 * Initialize plugin frontend.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Change to enqueued scripts - added call to action `wp_enqueue_scripts`.
		 */
		public static function initialize() {

			// Initialize only if not initialized already
			if ( ! self::$_initialized ) {

				$views_path  = tm_timeline_plugin_path( 'views' );
				self::$_view = new Tm_Timeline_View( $views_path );

				self::init_filters();

				add_action( 'init', array( __CLASS__, 'init_shortcode' ), -1 );
				add_action( 'init', array( __CLASS__, 'init_post_type' ) );
				add_action( 'wp_enqueue_scripts', array( __CLASS__, 'init_shortcode_assets' ) );

				self::$_initialized = false;
			}
		}

		/**
		 * Initialize custom post type.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Added `tm_timeline_register_post_type_args`, `tm_timeline_register_taxonomy_args` filters.
		 */
		public static function init_post_type() {
			$post_labels = array(
				'name'      => esc_html__( 'Timeline Posts', 'tm-timeline' ),
				'singular'  => esc_html__( 'Timeline Post', 'tm-timeline' ),
				'menu_name' => esc_html__( 'TM Timeline', 'tm-timeline' ),
			);

			register_post_type(
				'timeline_post',
				apply_filters( 'tm_timeline_register_post_type_args', array(
					'labels'              => $post_labels,
					'capability_type'     => 'post',
					'description'         => esc_html__( 'Timeline post item', 'tm-timeline' ),
					'exclude_from_search' => false,
					'public'              => true,
					'publicly_queryable'  => true,
					'show_ui'             => true,
					'menu_position'       => 25,
					'menu_icon'           => 'dashicons-clock',
					'rewrite'             => array(
						'slug' => 'timeline-post',
					),
					'taxonomies' => array(
						'timeline_post_tag',
					),
					'supports'  => array(
						'title',
						'editor',
						'custom_fields',
					),
				) )
			);

			$tag_labels = array(
				'name'     => esc_html__( 'Tags', 'tm-timeline' ),
				'singular' => esc_html__( 'Tag', 'tm-timeline' ),
			);

			register_taxonomy(
				'timeline_post_tag',
				'timeline_post',
				apply_filters( 'tm_timeline_register_taxonomy_args', array(
					'labels'              => $tag_labels,
					'description'         => esc_html__( 'Timeline post tag', 'tm-timeline' ),
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'show_tagcloud'       => true,
					'show_ui'             => true,
					'show_admin_column'   => true,
					'rewrite'             => array(
						'slug' => 'tm-timeline',
					),
				) )
			);
		}

		/**
		 * Initialize shortcode.
		 */
		public static function init_shortcode() {
			add_shortcode( self::get_shortcode_tag(), array(
				__CLASS__,
				'shortcode_frontend',
			) );

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once tm_timeline_plugin_path( 'includes/ext/elementor/class-tm-timeline-elementor-compat.php' );
				tm_timeline_elementor_compat( array(
					self::get_shortcode_tag() => array(
						'title' => esc_html__( 'Cherry Timeline', 'tm-timeline' ),
						'file'  => tm_timeline_plugin_path( 'includes/ext/elementor/class-tm-timeline-elementor-module.php' ),
						'class' => 'TM_Timeline_Elementor_Widget',
						'icon'  => 'eicon-time-line',
						'atts'  => self::get_shortcode_atts(),
					),
				) );
			}
		}

		/**
		 * Retrieve a shortcode tag.
		 *
		 * @since 1.1.0
		 * @return string
		 */
		public static function get_shortcode_tag() {
			return apply_filters( 'tm_timeline_shortcode_tag', self::$shortcode_tag );
		}

		/**
		 * Retrieve a shortcode attributes.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public static function get_shortcode_atts() {
			return apply_filters( 'tm_timeline_get_shortcode_atts', array(
				'layout' => array(
					'type'        => 'select',
					'title'       => esc_html__( 'Layout', 'tm-timeline' ),
					'description' => esc_html__( 'Layout type', 'tm-timeline' ),
					'options'     => wp_list_pluck( self::get_supported_layouts(), 'title' ),
					'value'       => '0',
					'default'     => '0',
				),
				'visible-items' => array(
					'type'        => 'slider',
					'title'       => esc_html__( 'Visible items', 'tm-timeline' ),
					'description' => esc_html__( 'Timeline number to show (only for "Horizontal" layout)', 'tm-timeline' ),
					'value'       => 3,
					'max_value'   => 5,
					'min_value'   => 1,
					'condition' => array(
						'layout' => '0',
					),
				),
				'date-format' => array(
					'type'    => 'select',
					'title'   => esc_html__( 'Date format', 'tm-timeline' ),
					'options' => wp_list_pluck( self::get_supported_date_formats(), 'title' ),
					'value'   => '0',
					'default' => '0',
				),
				'tag' => array(
					'type'        => 'select',
					'title'       => esc_html__( 'Tag', 'tm-timeline' ),
					'description' => esc_html__( 'Tag slug, empty value mean that no filtering will be performed', 'tm-timeline' ),
					'class'       => 'cherry-multi-select',
					'multiple'    => true,
					'options'     => false,
					'options_cb'  => array( __CLASS__, 'get_tags' ),
					'value'       => '',
				),
				'anchors' => array(
					'type'        => 'switcher',
					'title'       => esc_html__( 'Anchors', 'tm-timeline' ),
					'description' => esc_html__( 'Post title as anchor to the post', 'tm-timeline' ),
					'toggle'      => array(
						'true_toggle'  => esc_html__( 'Yes', 'tm-timeline' ),
						'false_toggle' => esc_html__( 'No', 'tm-timeline' ),
					),
					'value'   => 'off',
					'default' => 'off',
				),
				'order' => array(
					'type'        => 'select',
					'title'       => esc_html__( 'Order', 'tm-timeline' ),
					'description' => esc_html__( 'Sort order', 'tm-timeline' ),
					'options'     => array(
						'ASC'  => esc_html__( 'Ascending', 'tm-timeline' ),
						'DESC' => esc_html__( 'Descending', 'tm-timeline' ),
					),
					'value'   => 'DESC',
					'default' => 'DESC',
				),
			) );
		}

		/**
		 * Get default shortcode configuration.
		 *
		 * @deprecated 1.1.0 Use a `get_shortcode_atts`-method.
		 * @since 1.0.0
		 * @since 1.0.5 Added `tm_timeline_shortcode_default_attrs` filter.
		 * @return array
		 */
		public static function get_default_attrs() {
			return apply_filters( 'tm_timeline_shortcode_default_attrs', array(
				'layout'        => 1, // Horizontal layout
				'visible-items' => 5, // 5 visible items
				'date-format'   => 2, // `Y.m.d` date format
				'tag'           => '', // Tag slug, empty value mean that no filtering will be performed
				'anchors'       => true, // Post title as anchor to the post
				'order'         => 'DESC', // Sort order (ASC|DESC)
			) );
		}

		/**
		 * Get supported layouts list.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Added `tm_timeline_shortcode_supported_layouts` filter.
		 * @return array
		 */
		public static function get_supported_layouts() {
			return apply_filters( 'tm_timeline_shortcode_supported_layouts', array(
				0 => array(
					'title' => esc_html__( 'Horizontal', 'tm-timeline' ),
					'view'  => 'horizontal',
				),
				1 => array(
					'title' => esc_html__( 'Vertical', 'tm-timeline' ),
					'view'  => 'vertical',
				),
				2 => array(
					'title' => esc_html__( 'Vertical (chess order)', 'tm-timeline' ),
					'view'  => 'vertical-chessorder',
				),
			) );
		}

		/**
		 * Get supported date formats.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Added `tm_timeline_shortcode_supported_date_formats` filter.
		 * @return array
		 */
		public static function get_supported_date_formats() {
			return apply_filters( 'tm_timeline_shortcode_supported_date_formats', array(
				array(
					'title'  => esc_html__( 'YYYY - MM - DD', 'tm-timeline' ),
					'format' => 'Y-m-d',
				),
				array(
					'title'  => esc_html__( 'YYYY / MM / DD', 'tm-timeline' ),
					'format' => 'Y/m/d',
				),
				array(
					'title'  => esc_html__( 'YYYY . MM . DD', 'tm-timeline' ),
					'format' => 'Y.m.d',
				),
				array(
					'title'  => esc_html__( 'DD - MM - YYYY', 'tm-timeline' ),
					'format' => 'd-m-Y',
				),
				array(
					'title'  => esc_html__( 'DD / MM / YYYY', 'tm-timeline' ),
					'format' => 'd/m/Y',
				),
				array(
					'title'  => esc_html__( 'DD . MM . YYYY', 'tm-timeline' ),
					'format' => 'd.m.Y',
				),
				array(
					'title'  => esc_html__( 'MM', 'tm-timeline' ),
					'format' => 'm',
				),
				array(
					'title'  => esc_html__( 'YYYY', 'tm-timeline' ),
					'format' => 'Y',
				),
			) );
		}

		/**
		 * Shortcode rendering function.
		 *
		 * @since  1.0.0
		 * @since  1.0.5 Added `tm_timeline_query_args`, `tm_timeline_remove_shortcode_script` filters.
		 * @param  array $atts Shortcode attributes.
		 * @return string
		 */
		public static function shortcode_frontend( $atts ) {
			$defaults = wp_list_pluck( self::get_shortcode_atts(), 'value' );
			$args     = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );

			$args['anchors']   = filter_var( $args['anchors'], FILTER_VALIDATE_BOOLEAN );
			$supported_layouts = self::get_supported_layouts();
			$view              = $supported_layouts[ $defaults['layout'] ]['view'];
			$layout            = intval( $args['layout'] );
			$pages             = array();

			if ( isset( $supported_layouts[ $layout ] ) &&
				isset( $supported_layouts[ $layout ]['view'] )
			) {
				$view = $supported_layouts[ $layout ]['view'];
			}

			$qargs = array(
				'post_type'      => 'timeline_post',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_key'       => 'post-event-date',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
			);

			// If tag is defined, add `tax_query` into the `$qargs`
			if ( false === empty( $args['tag'] ) ) {
				$tag = get_term_by(
					'slug',
					$args['tag'],
					'timeline_post_tag'
				);

				if ( $tag ) {
					$qargs['tax_query'] = array(
						array(
							'taxonomy' => 'timeline_post_tag',
							'field'    => 'term_id',
							'terms'    => array(
								$tag->term_id,
							),
						),
					);
				}
			}

			if ( false === empty( $args['order'] ) ) {
				$qargs['order'] = in_array( $args['order'], array( 'ASC', 'DESC' ) ) ? $args['order'] : 'DESC';
			}

			$qargs = apply_filters( 'tm_timeline_query_args', $qargs, $atts );

			// Get posts.
			$query = new WP_Query( $qargs );

			if ( 0 === $layout ) {
				$pages = self::get_pages( $query->posts, $args['visible-items'] );

				/**
				 * Filter a flag that control enqueue for shortcode script.
				 *
				 * @since 1.0.5
				 */
				if ( false === apply_filters( 'tm_timeline_remove_shortcode_script', false ) ) {
					wp_enqueue_script( 'tm-timeline-js' );
				}
			}

			// Return the rendered shortcode.
			return self::$_view->render(
				$view,
				array(
					'config'          => $args,
					'pages'           => $pages,
					'timeline_events' => $query->posts,
				)
			);
		}

		/**
		 * Calculate pages based on visible_items count.
		 *
		 * @param  array $timeline_events Collection of all timeline posts.
		 * @param  int   $visible_items   Limit the visible items (only for horizontal layout).
		 * @return array
		 */
		private static function get_pages( array $timeline_events, $visible_items = -1 ) {
			$pages = array();
			$total = sizeof( $timeline_events );

			// If no visible items, show all.
			if ( 0 >= $visible_items ) {
				$visible_items = $total;
			}

			if ( $total === $visible_items ) {

				// We got only one page.
				$pages = array(
					$timeline_events
				);

			} else {
				$pages = array_chunk( $timeline_events, $visible_items, true );
			}

			return $pages;
		}

		/**
		 * Retrieve the terms in a taxonomy.
		 *
		 * @since  1.1.0
		 * @param  string $tax The taxonomies to retrieve terms from.
		 * @param  string $key Key for array - `id` or `slug`.
		 * @return array       Array with term names.
		 */
		public static function get_tags( $tax = 'timeline_post_tag' ) {
			$terms = array( esc_html__( 'From All', 'cherry-team' ) );

			foreach ( (array) get_terms( $tax, array( 'hide_empty' => false ) ) as $term ) {
				$terms[ $term->slug ] = $term->name;
			}

			return $terms;
		}

		/**
		 * Add shortcode js/css into the queue.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Added a correct plugin version. Changed a handle for `font-awesome.min.css`.
		 */
		public static function init_shortcode_assets() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script(
				'tm-timeline-js',
				tm_timeline_plugin_url( "/js/tm-timeline{$suffix}.js" ),
				array( 'jquery' ),
				TM_TIMELINE_VERSION,
				true
			);

			wp_enqueue_style(
				'font-awesome',
				tm_timeline_plugin_url( '/css/font-awesome.min.css' ),
				array(),
				'4.6.3'
			);

			wp_enqueue_style(
				'tm-timeline-css',
				tm_timeline_plugin_url( '/css/tm-timeline.css' ),
				array(),
				TM_TIMELINE_VERSION
			);
		}

		/**
		 * Setup date and content filters.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Changed the priority for `tm_timeline_format_content` filter (10 => 11).
		 */
		public static function init_filters() {
			add_filter( 'tm_timeline_format_date', array( __CLASS__, 'timeline_date_filter' ), 10, 2 );
			add_filter( 'tm_timeline_format_content', array( __CLASS__, 'timeline_content_filter' ), 11, 1 );
		}

		/**
		 * Default timeline date filter. Parse and format the date
		 *
		 * @param int    $date   The date timestamp.
		 * @param string $format The date format.
		 *
		 * @return string
		 */
		public static function timeline_date_filter( $date = '', $format = '' ) {
			$result = $date;

			if ( empty( $date ) ) {
				return '&mdash;';
			}

			$supported_date_formats = self::get_supported_date_formats();
			$date_format            = $supported_date_formats[0]['format'];
			$format                 = (int) $format;

			if ( isset( $supported_date_formats[ $format ] ) ) {
				$date_format = $supported_date_formats[ $format ]['format'];
			}

			return date( $date_format, $date );
		}

		/**
		 * Default timeline content filter.
		 *
		 * @since  1.0.0
		 * @since  1.0.5 Added `the_content` filter.
		 * @param  string $content The content that should filter be applied to.
		 * @return string
		 */
		public static function timeline_content_filter( $content = '' ) {
			return apply_filters( 'the_content', $content );
		}

		/**
		 * Plugin uninstall handler.
		 *
		 * @return bool
		 */
		public static function uninstall() {

			if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
				exit();
			}

			return true;
		}
	}
}
