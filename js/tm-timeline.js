/**
 * Horizontal layout slides
 */
( function( $ ) {

	$( document ).ready( function() {

		var $horizontalTimelines = $( '.tm_timeline.tm_timeline-layout-horizontal' ),
			$horizontalTimeline,
			$slides,
			$pages,
			$node,
			$control,
			$current;

		$horizontalTimelines.each( function() {
			$horizontalTimeline = $( this );

			$slides = $horizontalTimeline.find( '.tm_timeline__page' );
			$pages  = $horizontalTimeline.find( '.tm_timeline__pages' );

			// Attach event listener to slides controls
			$horizontalTimeline.find( '.tm_timeline__control' ).on( 'click', function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				$control = $( this );
				$horizontalTimeline = $( this ).parents().filter( '.tm_timeline' );
				$slides = $horizontalTimeline.find( '.tm_timeline__page' );
				$pages = $horizontalTimeline.find( '.tm_timeline__pages' );

				// Get current page
				$current = $( this ).parents().filter( '.tm_timeline' ).find( '.tm_timeline__page-current' );
				if ( $control.hasClass( 'tm_timeline__control-slide-right' ) ||
					 $control.hasClass( 'tm_timeline__control-slide-left' ) ) {

					// If user activated left navigation arrow, perform slide left
					if ( $control.hasClass( 'tm_timeline__control-slide-left' ) ) {
						$node = $current.prev();

						// Current slide was first, return back to the end
						if ( $node.length <= 0 ) {
							$node = $slides.last();
						}
					}

					// If user activated right navigation arrow, perform slide right
					if ( $control.hasClass( 'tm_timeline__control-slide-right' ) ) {
						$node = $current.next();

						// Current slide was last, return back to the beginning
						if ( $node.length <= 0 ) {
							$node = $slides.first();
						}
					}

					// Deactivate current node & activate the next one
					$current.removeClass( 'tm_timeline__page-current' );

					if ( $control.hasClass( 'tm_timeline__control-slide-right' ) ) {
						$node.addClass( 'tm_timeline__page-current-animate-right' );
					} else {
						$node.addClass( 'tm_timeline__page-current-animate-left' );
					}

					setTimeout( function() {
						$node.removeClass( 'tm_timeline__page-current-animate-left' );
						$node.removeClass( 'tm_timeline__page-current-animate-right' );
						$node.addClass( 'tm_timeline__page-current' );
					}, 10 );
				}
				return false;
			} );
		} );

	} );

}( jQuery ) );
