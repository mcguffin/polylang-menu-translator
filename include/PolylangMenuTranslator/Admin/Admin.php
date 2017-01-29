<?php

namespace PolylangMenuTranslator\Admin;
use PolylangMenuTranslator\Core;


class Admin extends Core\Singleton {

	private $core;

	/**
	 *	Private constructor
	 */
	protected function __construct() {

		$this->core = Core\Core::instance();

		add_action( 'admin_init', array( $this , 'admin_init' ) );
	}


	/**
	 * Admin init
	 */
	function admin_init() {
	}

	/**
	 * Enqueue options Assets
	 */
	function enqueue_assets() {
		wp_enqueue_style( 'polylang_menu_translator-admin' , $this->core->get_asset_url( '/css/admin.css' ) );

		wp_enqueue_script( 'polylang_menu_translator-admin' , $this->core->get_asset_url( 'js/admin.js' ) );
		wp_localize_script('polylang_menu_translator-admin' , 'polylang_menu_translator_admin' , array(
		) );
	}

}

