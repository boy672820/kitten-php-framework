<?php

function home_url( $path = '', $print = true ) {
	$url = $_SERVER[ 'REQUEST_SCHEME' ] . '://' . $_SERVER[ 'HTTP_HOST' ] . $path;
	if ( $print )
		echo $url;
	else
		return $url;
}

function resource_path_url( $resource_file = '', $print = true ) {
	$path = '';

	if ( ! empty( $resource_file ) ) {

		$resource_extension = explode( '.', $resource_file );
		$resource_extension = trim( $resource_extension[ count( $resource_extension ) - 1 ] );

		switch( $resource_extension ) {
			// Cascading style sheets file
			case 'css':
				$path = '/stylesheets/' . $resource_file;
				break;
			// Javascripts file`
			case 'js':
				$path = '/javascripts/' . $resource_file;
				break;
			// Images file`
			case 'jpg': case 'png': case 'gif': case 'bmp':
				$path = '/images/' . $resource_file;
				break;
			default:
				$path = $resource_file;
		}

		if ( $print )
			home_url( '/templates/assets/' . $path );
		else
			return home_url( '/templates/assets/' . $path, $print );
	}
}
