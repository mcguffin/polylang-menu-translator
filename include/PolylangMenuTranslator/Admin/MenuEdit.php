<?php

namespace PolylangMenuTranslator\Admin;
use PolylangMenuTranslator\Core;


class MenuEdit extends Core\Singleton {

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
		if ( function_exists( 'pll_the_languages' ) ) {
			add_action( 'load-nav-menus.php', array( $this, 'load_nav_menus' ) );
			add_action( 'admin_footer-nav-menus.php', array( $this, 'admin_footer' ) );
		}
	}

	function load_nav_menus() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'translate-menu' ) {
			if ( isset( $_GET['id'] ) && ! empty( absint( $_GET['id'] ) ) && 
				isset( $_GET['language'] ) && term_exists( $_GET['language'], 'language' ) ) {
				$nav_menu_id		= absint( $_GET['id'] );
				$target_language	= $_GET['language'];
			}
			if  ( isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'translate-menu-' . $nav_menu_id ) ) {
				// check nonce!!!
				$this->translate_nav_menu( $nav_menu_id, $target_language );
				exit();
			// redirect
			} else {
				exit('Nonce failed');
			}
		}
	}
	
	private function translate_nav_menu( $nav_menu_id, $target_language ) {
		
		$nav_menu = get_term( $nav_menu_id );

		$translated_menu_id = wp_create_nav_menu( $nav_menu->name . sprintf( ' (%s)', $target_language ) );
		
		$menu_items = wp_get_nav_menu_items( $nav_menu_id );
		
		$menu_map = array();

		foreach ( $menu_items as $menu_item ) {

			$new_menu_item_data = array(
				'menu-item-object-id'	=> $menu_item->object_id,
				'menu-item-object' 		=> $menu_item->object,
				'menu-item-parent-id' 	=> $menu_item->menu_item_parent,
				'menu-item-position' 	=> $menu_item->menu_order,
				'menu-item-type' 		=> $menu_item->type,
				'menu-item-title' 		=> $menu_item->title,
				'menu-item-description'	=> $menu_item->description,
				'menu-item-attr-title'	=> $menu_item->attr_title,
				'menu-item-target'		=> $menu_item->target,
				'menu-item-classes'		=> $menu_item->classes,
				'menu-item-xfn'			=> $menu_item->xfn,
			);

			switch( $menu_item->type ) {
				case 'post_type':

					if ( pll_is_translated_post_type( $menu_item->object ) ) {

						$post				= get_post( $menu_item->object_id );

						$translated_post_id	= pll_get_post( $menu_item->object_id, $target_language );

						$translated_post	= get_post( $translated_post_id );

						if ( $translated_post ) {
							$new_menu_item_data['menu-item-object-id'] = absint( $translated_post_id );
							if ( $menu_item->title == $post->title ) {
								$new_menu_item_data['menu-item-title'] = $translated_post->post_title;
							}
						}
					}
					break;

				case 'custom':
					$new_menu_item_data['menu-item-url'] = $menu_item->url;
					break;

				case 'post_type_archive':
/*
					if ( pll_is_translated_post_type( $menu_item->object ) ) {
						// humm.
						$new_url = PLL()->links_model->add_language_to_link( $link, $target_language );
					}
*/
					break;

				case 'taxonomy':
					if ( pll_is_translated_taxonomy( $menu_item->object ) ) {

						$term				= get_term( $menu_item->object_id );

						$translated_term_id	= pll_get_term( $menu_item->object_id, $target_language );

						if ( $translated_term_id ) {
							$translated_term	= get_term( $translated_term_id );
							$new_menu_item_data['menu-item-object-id'] = absint( $translated_term_id );
							if ( $menu_item->title == $term->title ) {
								$new_menu_item_data['menu-item-title'] = $translated_term->post_title;
							}
						}
					}
					break;
			}

			if ( $menu_item->menu_item_parent && isset( $menu_map[ absint( $menu_item->menu_item_parent ) ] ) ) {
				$new_menu_item_data[ 'menu-item-parent-id' ] = $menu_map[ absint( $menu_item->menu_item_parent ) ];
			}

			$menu_map[ absint( $menu_item->ID ) ] = wp_update_nav_menu_item( $translated_menu_id, 0, $new_menu_item_data );

			$url = add_query_arg( array( 
				'menu'		=> $translated_menu_id,
				'action'	=> 'edit',
			), admin_url( 'nav-menus.php' ) );

			wp_redirect( $url );
			
		}
	
	}
	
	function admin_footer() {
		global $nav_menu_selected_id, $add_new_screen, $locations_screen;

		if ( ! $locations_screen && ! $add_new_screen && ! empty( $nav_menu_selected_id ) ) {
			?>
				<div id="translate-nav-menu">
					<input type="hidden" name="translate-menu[action]" value="translate-menu" />
					<?php
					wp_dropdown_categories( array(
						'taxonomy'			=> 'language',
						'show_option_none'	=> false,
						'name'				=> 'translate-menu[language]',
						'value_field'		=> 'slug',
					) );
					?>
					<?php wp_nonce_field( 'translate-menu-' . $nav_menu_selected_id, 'translate-menu[nonce]' ); ?>
					<button class="button-secondary" name="translate-menu[id]" value="<?php echo absint( $nav_menu_selected_id ); ?>"><?php _e( 'Translate Current Menu', 'polylang-menu-translator' ) ?></button>
				</div>
				<script type="text/javascript">
					(function($){
						$('#post-body').append( $('#translate-nav-menu') );
						
						$(document).on('click','#translate-nav-menu button',function(e) {
							e.preventDefault();
							var data = {};
							$('[name^="translate-menu"]').each(function(i) {
								var key;
								try {
									key = $(this).attr('name').match(/translate-menu\[(\w+)\]/)[1];
									data[ key ] = $(this).val();
								} catch(err){}
							})
							document.location.search = $.param(data);
						});
						
					})(jQuery);
				</script>
			<?php
		}
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

