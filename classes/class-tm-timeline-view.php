<?php
/**
 * Provide views functionality.
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
if ( ! class_exists( 'Tm_Timeline_View' ) ) {

	/**
	 * Simple views rendering utility class.
	 */
	class Tm_Timeline_View {
		/**
		 * View files extension.
		 *
		 * @const string
		 */
		const EXT = '.php';

		/**
		 * Views base directory path.
		 *
		 * @var string
		 */
		private $_view_path;

		/**
		 * Stores variables passed to view.
		 *
		 * @var array
		 */
		private $_vars = array();

		/**
		 * Class constructor.
		 *
		 * @param String $view_path Views base directory path.
		 *
		 * @throws Exception If directory path is not valid.
		 */
		public function __construct( $view_path = '' ) {

			if ( false === is_dir( $view_path ) ||
				false === is_readable( $view_path )
			) {
				throw new Exception( 'Invalid views path!' );
			}

			$this->_view_path = $view_path;
		}

		/**
		 * Render and return view file content.
		 *
		 * @since 1.0.0
		 * @since 1.0.5 Extended array of template files to search for.
		 * @param string $filename       View file path or name.
		 * @param array  $vars           Variables that should be exported into
		 *                               the view scope. `$filepath` variable is accessible
		 *                               by default and has a value of current view file
		 *                               path.
		 *
		 * @return string Rendered result
		 */
		public function render( $filename = '', array $vars = array() ) {
			$filename = basename( $filename );
			$filepath = false;

			if ( false === is_admin() &&
				false === strstr( $this->_view_path, 'admin' )
			) {
				// Array of template files to search for.
				$template_names = array();
				$template_names[] = 'tm-timeline_' . $filename . self::EXT;
				$template_names[] = 'tm-timeline/' . $filename . self::EXT;

				$filepath = locate_template( $template_names );
			}

			// If no file path found, use default value.
			if ( false === $filepath ||
				empty( $filepath )
			) {
				$filepath = $this->_view_path . DIRECTORY_SEPARATOR . $filename . self::EXT;
			}

			// Allow 3rd party plugins to set the view.
			$filepath = apply_filters( 'tm_timeline_view_file', $filepath );

			// Validate view file path
			if ( false === $this->validate_path( $filepath ) ) {
				return new Exception(
					sprintf( 'Invalid view path! Path: %s', $filepath )
				);
			}

			/**
			 * Filter a variables that passed to view file.
			 *
			 * @since 1.0.5
			 */
			$vars = apply_filters( 'tm_timeline_render_vars', $vars, $filename );

			// Export vars to the view
			$this->set_vars( $vars );
			unset( $vars, $filename, $view );

			ob_start();
			include $filepath;
			$content = ob_get_clean();

			return $content;
		}

		/**
		 * Validate a path.
		 *
		 * @param  string $filepath Path to a file.
		 * @return bool
		 */
		private function validate_path( $filepath = '' ) {
			return (
				false === empty( $filepath ) &&
				false !== $filepath &&
				file_exists( $filepath ) &&
				is_readable( $filepath )
			);
		}

		/**
		 * Set view variables.
		 *
		 * @param  array $vars Variables collection.
		 * @return Tm_Timeline_View
		 */
		public function set_vars( array $vars = array() ) {
			$this->_vars = $vars;

			return $this;
		}

		/**
		 * Return view variables.
		 *
		 * @return array
		 */
		public function get_vars() {
			return $this->_vars;
		}

		/**
		 * Check if view variable exists.
		 *
		 * @param  string $name Variable name.
		 * @return bool
		 */
		public function has_var( $name = '' ) {

			if ( empty( $name ) ) {
				return false;
			}

			return isset( $this->_vars[ $name ] );
		}

		/**
		 * Get view variable value.
		 *
		 * @param  string $name Variable name.
		 * @param  mixed  $default_value Default value returned if no value found.
		 * @return mixed
		 */
		public function get_var( $name = '', $default_value = null ) {

			if ( false === $this->has_var( $name ) ) {
				return $default_value;
			}

			return $this->_vars[ $name ];
		}

		/**
		 * Set view variable value.
		 *
		 * @param  string $name  Variable name.
		 * @param  mixed  $value Variable value.
		 * @return $this
		 */
		public function set_var( $name = '', $value = null ) {

			if ( empty( $name ) ) {
				return false;
			}

			$this->_vars[ $name ] = $value;

			return $this;
		}

		/**
		 * Magic method for accessing view variables.
		 *
		 * @param  string $name Variable name.
		 * @return mixed Variable value
		 */
		public function __get( $name = '' ) {
			return $this->get_var( $name );
		}

		/**
		 * Magic method for setting the view variable value.
		 *
		 * @param string $name  Variable name.
		 * @param mixed  $value Variable value.
		 */
		public function __set( $name = '', $value = null ) {
			$this->set_var( $name, $value );
		}
	}

}
