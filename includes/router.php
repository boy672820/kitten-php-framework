<?php

$slug = ! empty( $_GET[ 'page' ] ) ? $_GET[ 'page' ] : 'index';

template_loader( $slug );
