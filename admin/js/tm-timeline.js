/**
 * Generate shortcode
 */
( function( $ ) {
	'use strict';

	$( document ).ready( function() {

		var ArrayProto = Array.prototype,
			$shortcodeForm = $( '#timeline-shorcode-form' ),
			$shortcodeFormSubmit = $( '#timeline-shorcode-generate' ),
			$shortcodeResultWrapper = $( '#timeline-shorcode-result-wrapper' ),
			$shortcodeResult = $( '#timeline-shortcode-result' ),
			shortcode = '[tm-timeline%options%]',
			options = [],

		// Fields:
			$optLayout = $( '#timeline-layout' ),
			$optVisibleItems = $( '#timeline-visible-items' ),
			$optDateFormat = $( '#timeline-date-format' ),
			$optTag = $( '#timeline-tag-slug' ),
			$optAnchors = $( '#timeline-anchors' ),
			$optOrder = $( '#timeline-order' ),

		// Values:
			optLayout,
			optVisibleItems,
			optDateFormat,
			optTag,
			optAnchors,
			optOrder;

		/**
		 * Convert object to array
		 * @param {Object} obj
		 * @returns {Array}
		 */
		function toArray( obj ) {
			return ArrayProto.slice.call( obj, 0 );
		}

		/**
		 * Show shortcode textarea
		 */
		function showResult() {
			$shortcodeResultWrapper.removeClass( 'hidden' );
		}

		/**
		 * Hide shortcode textarea
		 */
		function hideResult() {
			$shortcodeResultWrapper.addClass( 'hidden' );
		}

		/**
		 * Generate shortcode
		 * @param {jqEvent} [event]
		 */
		function generateShortcodeCb( event ) {
			if ( event ) {
				event.preventDefault();
			}

			// Clean the options
			options = [];

			// Hide shortcode textarea by default
			hideResult();

			/**
			 * Push option into shortcode options
			 * @param {String} key
			 * @param {String|Number} value
			 * @param {Boolean} [isOptional] If option is optional and it's value is empty, do not add the option
			 * @returns {Boolean}
			 */
			function opt( key, value, isOptional ) {
				isOptional = isOptional || false;

				if ( isOptional ) {
					if ( '' === value ) {
						return false;
					}
				}

				options.push(
					toArray( arguments ).slice( 0, 2 ).join( '="' ) + '"'
				);

				return true;
			}

			// Get timeline layout
			optLayout = parseInt( $optLayout.val(), 10 );

			// Get visible items limit
			optVisibleItems = $optVisibleItems.val();

			// If value is set, convert it to integer
			if ( '' !== optVisibleItems ) {
				optVisibleItems = parseInt( optVisibleItems, 10 );
			}

			// Get timeline date format
			optDateFormat = $optDateFormat.val();

			// Get tag
			optTag = $optTag.val();

			// Get post title as anchor to post
			optAnchors = $optAnchors.prop( 'checked' );

			// Get sort order
			optOrder = $optOrder.val();

			// Add options
			opt( $optLayout.attr( 'name' ), optLayout );
			opt( $optVisibleItems.attr( 'name' ), optVisibleItems, true );
			opt( $optDateFormat.attr( 'name' ), optDateFormat );
			opt( $optTag.attr( 'name' ), optTag, true );
			opt( $optAnchors.attr( 'name' ), optAnchors );
			opt( $optOrder.attr( 'name' ), optOrder );

			// Generate shortcode code and set the textarea value
			$shortcodeResult.val(
				shortcode.replace( '%options%', ' ' + options.join( ' ' ) )
			);

			// Show the textarea after a slight delay, so user can notice the update
			setTimeout( showResult, 100 );
		}

		// Attach event listeners
		$shortcodeForm.on( 'submit', generateShortcodeCb );
		$shortcodeFormSubmit.on( 'click', generateShortcodeCb );

	} );
}( jQuery ) );
