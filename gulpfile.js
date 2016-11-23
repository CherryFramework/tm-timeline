'use strict';

const gulp = require( 'gulp' ),
	path = require( 'path' ),
	exec = require( 'child_process' ).exec,
	WP_PATH = process.env.WP_PATH || `${__dirname}${path.sep}.pub`,
	DIST_PATH = `${__dirname}${path.sep}.dist${path.sep}timeline`,
	WP_PLUGINS_PATH = [ 'wp-content', 'plugins' ].join( path.sep ),
	WP_PUB_PATH = path.join( WP_PATH, WP_PLUGINS_PATH, 'timeline' );

let rm = `rm -rf`;
if ( 'win32' === process.platform ) {
	rm = `RD /S /Q`;
}

gulp.task( 'dist.clear', ( next ) => {
	return exec( `${rm} ${DIST_PATH}`, ( error, stdout, stderr ) => {
		if ( '' !== stdout ) {
			console.log( stdout );
		}
		if ( '' !== stderr ) {
			console.log( stderr );
		}
		if ( null !== error ) {
			console.log( error );
		} else {
			next();
		}
	} );
} );

gulp.task( 'dist.admin', () => {
	return gulp.src( `${__dirname}${path.sep}admin${path.sep}**${path.sep}*` )
		.pipe( gulp.dest( `${DIST_PATH}${path.sep}admin${path.sep}` ) );
} );

gulp.task( 'dist.classes', () => {
	return gulp.src( `${__dirname}${path.sep}classes${path.sep}**${path.sep}*` )
		.pipe( gulp.dest( `${DIST_PATH}${path.sep}classes${path.sep}` ) );
} );

gulp.task( 'dist.css', () => {
	return gulp.src( `${__dirname}${path.sep}css${path.sep}**${path.sep}*` )
		.pipe( gulp.dest( `${DIST_PATH}${path.sep}css${path.sep}` ) );
} );

gulp.task( 'dist.js', () => {
	return gulp.src( `${__dirname}${path.sep}js${path.sep}**${path.sep}*` )
		.pipe( gulp.dest( `${DIST_PATH}${path.sep}js${path.sep}` ) );
} );

gulp.task( 'dist.views', () => {
	return gulp.src( `${__dirname}${path.sep}views${path.sep}**${path.sep}*` )
		.pipe( gulp.dest( `${DIST_PATH}${path.sep}views${path.sep}` ) );
} );

gulp.task( 'dist.php', () => {
	return gulp.src( [
		`${__dirname}${path.sep}timeline.php`,
		`${__dirname}${path.sep}functions.php`,
		`${__dirname}${path.sep}index.php`,
		`${__dirname}${path.sep}uninstall.php`
	] ).pipe( gulp.dest( DIST_PATH ) );
} );

gulp.task( 'dist', [
	'dist.clear',
	'dist.admin',
	'dist.classes',
	'dist.css',
	'dist.js',
	'dist.views',
	'dist.php'
] );

gulp.task( 'publish', [ 'dist' ], function() {
	return gulp.src( `${DIST_PATH}${path.sep}**` )
		.pipe( gulp.dest( WP_PUB_PATH ) );
} );

gulp.task( 'default', [ 'publish' ] );
