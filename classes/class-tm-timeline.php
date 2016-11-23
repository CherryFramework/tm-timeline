<?php
/**
 * Provide front-end related functionality
 *
 * @package    Tm_Timeline
 * @subpackage Tm_Timeline
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2016 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Register class if it does not exists already
 */
if ( false === class_exists( 'Tm_Timeline' ) ) {

	// Load Tm_Timeline_View if class was not initialized yet.
	if ( false === class_exists( 'Tm_Timeline_View' ) ) {
		require tm_timeline_plugin_path( 'classes/class-tm-timeline-view.php' );
	}

	/**
	 * Class contains front-end related functionality
	 */
	class Tm_Timeline {

		/**
		 * Determine if initialization is required
		 *
		 * @var Boolean Initialized flag
		 */
		private static $_initialized = false;

		/**
		 * Views renderer
		 *
		 * @var Tm_Timeline_View View renderer instance
		 */
		private static $_view;

		/**
		 * Initialize plugin frontend
		 */
		public static function initialize() {
			// Initialize only if not initialized already
			if ( false === self::$_initialized ) {

				$views_path  = tm_timeline_plugin_path( 'views' );
				self::$_view = new Tm_Timeline_View( $views_path );

				self::init_post_type();
				self::init_shortcode();
				self::init_filters();
				self::init_shortcode_assets();

				self::$_initialized = false;
			}
		}

		/**
		 * Initialize custom post type
		 */
		public static function init_post_type() {
			$post_labels = array(
				'name'      => __( 'Timeline Posts', 'tm-timeline' ),
				'singular'  => __( 'Timeline Post', 'tm-timeline' ),
				'menu_name' => __( 'TM Timeline', 'tm-timeline' ),
			);

			register_post_type(
				'timeline_post', array(
					'labels'              => $post_labels,
					'capability_type'     => 'post',
					'description'         => 'Timeline post item',
					'exclude_from_search' => false,
					'public'              => true,
					'publicly_queryable'  => true,
					'show_ui'             => true,
					'menu_position'       => 25,
					'menu_icon'           => 'dashicons-clock',
					'rewrite'             => array(
						'slug' => 'timeline-post',
					),
					'taxonomies'          => array(
						'timeline_post_tag',
					),
					'supports'            => array(
						'title',
						'editor',
						'custom_fields',
					),
				)
			);

			$tag_labels = array(
				'name'     => __( 'Tags' ),
				'singular' => __( 'Tag' ),
			);

			register_taxonomy(
				'timeline_post_tag', 'timeline_post', array(
					'labels'              => $tag_labels,
					'description'         => 'Timeline post tag',
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'show_tagcloud'       => true,
					'show_ui'             => true,
					'show_admin_column'   => true,
					'rewrite'             => array(
						'slug' => 'tm-timeline',
					),
				)
			);
		}

		/**
		 * Initialize shortcode
		 */
		public static function init_shortcode() {
			add_shortcode(
				'tm-timeline', array(
					'Tm_Timeline',
					'shortcode_frontend',
				)
			);
		}

		/**
		 * Plugin uninstall handler
		 *
		 * @return Boolean If uninstall completed successfully
		 */
		public static function uninstall() {
			if ( false === defined( 'WP_UNINSTALL_PLUGIN' ) ) {
				exit();
			}

			return true;
		}

		/**
		 * Get default shortcode configuration
		 *
		 * @return array
		 */
		public static function get_default_attrs() {
			return array(
				'layout'        => 1, // Horizontal layout
				'visible-items' => 5, // 5 visible items
				'date-format'   => 2, // `Y.m.d` date format
				'tag'           => '', // Tag slug, empty value mean that no filtering will be performed
				'anchors'       => true, // Post title as anchor to the post
				'order'         => 'DESC', // Sort order (ASC|DESC)
			);
		}

		/**
		 * Get supported layouts list
		 *
		 * @return array
		 */
		public static function get_supported_layouts() {
			return array(
				0 => array(
					'title' => __( 'Horizontal', 'tm-timeline' ),
					'view'  => 'horizontal',
				),
				1 => array(
					'title' => __( 'Vertical', 'tm-timeline' ),
					'view'  => 'vertical',
				),
				2 => array(
					'title' => __( 'Vertical (chess order)', 'tm-timeline' ),
					'view'  => 'vertical-chessorder',
				),
			);
		}

		/**
		 * Get supported date formats
		 *
		 * @return array
		 */
		public static function get_supported_date_formats() {
			return array(
				array(
					'title'  => 'YYYY - MM - DD',
					'format' => 'Y-m-d',
				),
				array(
					'title'  => 'YYYY / MM / DD',
					'format' => 'Y/m/d',
				),
				array(
					'title'  => 'YYYY . MM . DD',
					'format' => 'Y.m.d',
				),
				array(
					'title'  => 'DD - MM - YYYY',
					'format' => 'd-m-Y',
				),
				array(
					'title'  => 'DD / MM / YYYY',
					'format' => 'd/m/Y',
				),
				array(
					'title'  => 'DD . MM . YYYY',
					'format' => 'd.m.Y',
				),
				array(
					'title'  => 'MM',
					'format' => 'm',
				),
				array(
					'title'  => 'YYYY',
					'format' => 'Y',
				),
			);
		}

		/**
		 * Shortcode rendering function
		 *
		 * @param array $atts Shortcode attributes.
		 *
		 * @return string
		 */
		public static function shortcode_frontend( $atts = array() ) {

			// Passed arguments are not valid, return empty output
			if ( false === is_array( $atts ) ) {
				return '';
			}

			$defaults = Tm_Timeline::get_default_attrs();
			$args     = shortcode_atts( $defaults, $atts, 'tm-timeline' );

			// `$args['anchors']` is a string, sadly
			if ( 'false' === $args['anchors'] ) {
				$args['anchors'] = false;
			} else {
				$args['anchors'] = true;
			}

			$supported_layouts = self::get_supported_layouts();
			$view              = $supported_layouts[ $defaults['layout'] ]['view'];
			$layout            = (int) $args['layout'];
			$pages             = array();

			if ( isset( $supported_layouts[ $layout ] ) &&
				isset( $supported_layouts[ $layout ]['view'] )
			) {
				$view = $supported_layouts[ $layout ]['view'];
			}

			$qargs = array(
				'post_type'      => 'timeline_post',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
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
				$qargs['order'] = in_array( $args['order'], array( 'ASC', 'DESC' ) ) ? esc_html( $args['order'] ) : 'DESC';
			}

			// Get posts
			$query = new WP_Query( $qargs );

			if ( 0 === $layout ) {
				$pages = self::get_pages( $query->posts, $args['visible-items'] );
			}

			// Return the rendered shortcode
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
		 * Calculate pages based on visible_items count
		 *
		 * @param array   $timeline_events Collection of all timeline posts.
		 * @param integer $visible_items   Limit the visible items (only for horizontal layout).
		 *
		 * @return array
		 */
		private static function get_pages( array $timeline_events, $visible_items = - 1 ) {
			$pages = array();
			$total = sizeof( $timeline_events );

			// If no visible items, show all
			if ( 0 >= $visible_items ) {
				$visible_items = $total;
			}

			if ( $total === $visible_items ) {
				// We got only one page
				$pages = array(
					$timeline_events
				);
			} else {
				$pages = array_chunk( $timeline_events, $visible_items, true );
			}

			return $pages;
		}

		/**
		 * Add shortcode js/css into the queue
		 */
		public static function init_shortcode_assets() {
			wp_enqueue_script(
				'timeline-js',
				tm_timeline_plugin_url( '/js/tm-timeline.js' ),
				array(
					'jquery'
				),
				'1.0.0',
				true
			);

			wp_enqueue_style(
				'timeline-fontawesome-css',
				tm_timeline_plugin_url( '/css/font-awesome.min.css' )
			);

			wp_enqueue_style(
				'timeline-css',
				tm_timeline_plugin_url( '/css/tm-timeline.css' )
			);
		}

		/**
		 * Setup date and content filters.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Changed the priority for `tm_timeline_format_content` filter (10 => 11).
		 */
		public static function init_filters() {
			add_filter( 'tm_timeline_format_date', array( 'Tm_Timeline', 'timeline_date_filter' ), 10, 2 );
			add_filter( 'tm_timeline_format_content', array( 'Tm_Timeline', 'timeline_content_filter' ), 11, 1 );
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

			$result = date( $date_format, $date );

			return $result;
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
	}
}
