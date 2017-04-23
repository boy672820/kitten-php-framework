<?php

function template_loader( $page_slug ) {
	$template = TEMPLATEPATH . '/' . $page_slug . '.php';

	if ( empty( $page_slug ) ) {
		include_once( TEMPLATEPATH . '/index.php' );
	}
	else if ( ! empty( $page_slug ) && file_exists( $template ) ) {
		include_once( $template );
	}
	else {
		include_once( TEMPLATEPATH . '/404.php' );
	}
}
