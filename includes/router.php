<?php

$page_slug = 'index';

if ( ! empty( $_GET[ 'page' ] ) ) {

	$page_slug = $_GET[ 'page' ];

}

else {

	$uris = explode( '/', $_SERVER[ 'REQUEST_URI' ] );
	$page_slug = $uris[ 1 ];

}

template_loader( $page_slug );
