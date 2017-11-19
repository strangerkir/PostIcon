<?php

//Settings page class
class PostIconSettingsPage{

	private $posts = array();

	function __construct(){
		add_action ( 'admin_menu', array( $this, 'addOptionsPage' ) );
		add_action( 'admin_init', array( $this, 'addPostIconSettings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'addScriptsStyles' ) );
		add_action( 'wp_ajax_add_icon', array( $this, 'setPostIcon') );
		add_action( 'wp_ajax_pi_get_post_meta', array( $this, 'getPostIcon') );
	}

	//Register options page in WP admin menu
	function addOptionsPage(){
		add_options_page( 'Post icon', 'Post icon', 'manage_options', 'post_icon_options', array( $this, 'showPostIconOptions' ) );
	}

	//Return dashicon on AJAX query
	function getPostIcon(){
		$postid = intval( $_GET['post_id'] );
		$icon_data['post_icon'] = get_post_meta( $postid, 'post_icon', true );
		$icon_data['post_icon_position'] = get_post_meta( $postid, 'post_icon_position', true );
		echo json_encode( $icon_data );
		wp_die();
	}

	//Register settings 
	function addPostIconSettings(){
		register_setting( 'post_icon_options_main', 'post_icon_enabled', 'boolval' );
		add_settings_section( 'post_icon_options_main', '', '', 'post_icon_options_page' );
		add_settings_field( 'pi_enabled', 'Enable post Icon?', array( $this, 'fillPostIconEnabled' ), 'post_icon_options_page', 'post_icon_options_main' );
		add_settings_field( 'pi_posts', 'Select post: ', array( $this, 'fillPostIconPosts'), 'post_icon_options_page', 'post_icon_options_main' );
		add_settings_field( 'pi_icons', 'Select icon: ', array( $this, 'fillPostIcons' ), 'post_icon_options_page', 'post_icon_options_main' );
		add_settings_field( 'pi_position', 'Icon position: ', array( $this, 'fillPostIconPosition' ), 'post_icon_options_page', 'post_icon_options_main'  );
	}

	//Show settings page parts
	function fillPostIconPosition(){
		?>

		<label><input type="radio" name="pi_left_right" value = "0">Left</label>
		<label><input type="radio" name="pi_left_right" value = "1">Right</label>

		<?php
	}

	//Show settings page parts
	function fillPostIcons(){
		?>
		<input class="regular-text" id="post_icon_dashicons_picker" type="text" />
		<span class="dashicons dashicon-preview"></span>
		<input class="button dashicons-picker" type="button" value="Choose Icon" data-target="#post_icon_dashicons_picker" />
		<?php
	}

	//Show settings page parts
	function fillPostIconPosts(){
		?>
		<select id="pi_post_selector">
		<?php 
			$posts_arr = $this -> getAllPostsData();
			foreach( $posts_arr as $post ):
				$selected = ( empty( get_post_meta( $post['ID'], 'post_icon' ) ) ) ? 0 : 1;
		?>
			<option value="<?php echo $post['ID']; ?>" <?php selected(1, $selected, true); ?> > <?php echo $post['ID'] . ' - ' . $post['post_title']; ?></option>

			
		<?php 
			endforeach;
		?>
		</select>

		<?php
	}

	//Show settings page parts
	function fillPostIconEnabled(){
			$enabled = (int) get_option( 'post_icon_enabled' );
		?>
			<input type="checkbox" name='post_icon_enabled' value="1" <?php checked( 1, $enabled, true ); ?> >

		<?php
	}

	//Change post icon on AJAX query
	function setPostIcon(){
		if( ! current_user_can( 'manage_options' ) ){
			wp_die( 'You can\'t do it. Bye!' );
		}
		$post_id = intval( $_POST['selected_post'] );
		$icon_name = filter_var( $_POST['icon'], FILTER_SANITIZE_STRING );
		$icon_position = boolval( $_POST['icon_position'] );
		update_post_meta( $post_id, 'post_icon', $icon_name );
		update_post_meta( $post_id, 'post_icon_position', $icon_position );
		wp_die( $icon_name . ' ' . $icon_position );
	}

	//Get ID and post title from DB
	private function getAllPostsData(){
		global $wpdb;

		if( empty ( $this->posts ) ){
			$posts = wp_cache_get( 'pi_all_posts', 'post' );
			if( ! is_array( $posts ) ){
				$posts = $wpdb -> get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' LIMIT 100", ARRAY_A );
				wp_cache_set( 'pi_all_posts', $posts, 'post', 120);
			}
		}
		else {
			$posts = $this -> posts;
		}

		return $posts;

	}

	
	//Show settings page content
	function showPostIconOptions(){
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>
			<form action="options.php" method="POST">
			<?php
				settings_fields( 'post_icon_options_main' );
				do_settings_sections( 'post_icon_options_page' );
				$all_posts = $this -> getAllPostsData();
			?>
				
			<div class ="pi_icons"></div>
			<?php
				submit_button( 'Save', 'primary', 'pi_save_button' );
			?>
				</div>
			</form>
		</div>
		<?php
	}

	//Add scripts and styles
	function addScriptsStyles(){
		wp_enqueue_script( 'dashicons-picker', plugins_url( 'post_icon/vendor/dashicons-picker/js/dashicons-picker.js' ), array( 'jquery' ) );
		wp_enqueue_style( 'dashicons-picker',  plugins_url( 'post_icon/vendor/dashicons-picker/css/dashicons-picker.css' ), array( 'dashicons' ) );
		wp_enqueue_script( 'post_icon_script', plugins_url( 'post_icon/js/post_icon.js' ), array( 'jquery' ) );
	}
} 
?>