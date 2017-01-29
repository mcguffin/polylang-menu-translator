<?php

/*
Plugin Name: Polylang Menu Translator
Plugin URI: http://wordpress.org/
Description: Enter description here.
Author: Jörn Lund
Version: 1.0.0
Author URI: 
License: GPL3

Text Domain: polylang-menu-translator
Domain Path: /languages/
*/

/*  Copyright 2017  Jörn Lund

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
Plugin was generated by WP Plugin Scaffold
https://github.com/mcguffin/wp-plugin-scaffold
Command line args were: `"Polylang Menu Translator" admin+css+js git`
*/


namespace PolylangMenuTranslator;

define( 'POLYLANG_MENU_TRANSLATOR_FILE', __FILE__ );
define( 'POLYLANG_MENU_TRANSLATOR_DIRECTORY', plugin_dir_path(__FILE__) );

require_once POLYLANG_MENU_TRANSLATOR_DIRECTORY . 'include/vendor/autoload.php';

Core\Core::instance();




if ( is_admin() || defined( 'DOING_AJAX' ) ) {


/*
	Admin\Admin::instance();
	Admin\Tools::instance();
	Admin\Settings::instance();

	// Compatibility plugins
	$compat = glob(plugin_dir_path(__FILE__) . 'include/compat/*.php');
	foreach ( $compat as $compat_file ) {
		require_once $compat_file;
	}
*/
}

