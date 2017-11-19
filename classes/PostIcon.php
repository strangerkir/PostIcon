<?php
	//Main class
	class PostIcon{
		function __construct(){
			register_activation_hook( __FILE__, array( 'PostIcon', 'postIconAddOptions' ) );
			register_deactivation_hook( __FILE__, array( 'PostIcon', 'postIconDeleteOptions' ) );
			register_uninstall_hook( __FILE__, array( 'PostIcon', 'postIconDeleteIcons' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'addPostIconAssets' ) );
			add_filter( 'the_title', array( $this, 'addDashicon'), 100, 2 );
		}

		//Enable Post Icon on activation
		function postIconAddOptions(){
			add_option( 'post_icon_enabled', true );
		}

		//Delete Post Icon option on deactivation;
		function postIconDeleteOptions(){
			remove_option( 'post_icon_enabled' );
		}

		//Clean metafields
		function postIconDeleteIcons(){
			//clean all post_icon and post_icon_position meta fields here.
		}

		//Just in case. No really need to do this.
		function addPostIconAssets(){
			wp_enqueue_style( 'dashicons' );
		}

		//Add span with dashicon class to title
		function addDashicon( $title, $id){
			if( ! get_option( 'post_icon_enabled' ) ){
				return $title;
			}
			$current_post_icon = get_post_meta( $id, 'post_icon', true );
			$current_post_icon_position = (bool) get_post_meta( $id, 'post_icon_position', true );

			if( ! empty( $current_post_icon ) ) {
				$span = '<span class="dashicons ' . $current_post_icon . '" ></span>';
				if( $current_post_icon_position == 1){
					$title .= '<span class="dashicons ' . $current_post_icon . '" ></span>';
				}
				else{
					$title = '<span class="dashicons left ' . $current_post_icon . '"></span>' . $title;
				}
			}
			return $title;

		}
	}
?>
