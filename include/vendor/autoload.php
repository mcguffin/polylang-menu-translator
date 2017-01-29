<?php

namespace PolylangMenuTranslator;

function __autoload( $class ) {
	if ( strpos( $class, 'PolylangMenuTranslator\\' ) === false ) {
		// not our plugin.
		return;
	}
	$ds = DIRECTORY_SEPARATOR;
	$file = POLYLANG_MENU_TRANSLATOR_DIRECTORY . 'include' . $ds . str_replace( '\\', $ds, $class ) . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	} else {
		throw new \Exception( sprintf( 'Class `%s` could not be loaded. File `%s` not found.', $class, $file ) );
	}
}


spl_autoload_register( 'PolylangMenuTranslator\__autoload' );