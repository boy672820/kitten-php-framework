<?php

if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

require_once( ABSPATH . 'config.php' );

define( 'INC', 'includes' );
require_once( ABSPATH . INC . '/settings.php' );
require_once( ABSPATH . INC . '/functions.php' );
require_once( ABSPATH . INC . '/class-db.php' );
require_once( ABSPATH . INC . '/template.php' );
require_once( ABSPATH . INC . '/template-loader.php' );
require_once( ABSPATH . INC . '/router.php' );
