<?php
class ZgItemRatingsZoom extends ZgItemRatings {
	
	private $parent_options 		= array();
	private $custom_icon_col_name	= 'zg-zoom-icon';
	private	$ajax_nonce_key			= 'zg-item-ratings-zoom-get-image';
	
	function __construct() {
		
		//Cache parent plugin config options array
		$this->parent_options = self::$class_config;
		
		//Add ajax call
		add_action( 'wp_ajax_zg-item-ratings-zoom-get-image', array($this, 'get_zoom_image') );
		
		//Init plugin
		add_action( 'current_screen', array($this, 'init_plugin') );
		
	}
	
	/**
	* init_plugin
	* 
	* Used By Action: 'current_screen'
	* 
	* Detects current view and decides if plugin should be activated
	*
	* @access 	public
	* @author	Ben Moody
	*/
	public function init_plugin() {
		
		//Confirm we are on an active admin view
		if( $this->is_active_view() && $this->is_attachment_view() ) {
			
			//Set plugin admin actions
			$this->set_admin_actions();
			
			//Enqueue admin scripts
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );
	
		}
		
	}
	
	/**
	* is_attachment_view
	* 
	* Helper to detect is current admin view is one related to the media library
	* 
	* @return	bool	$result
	* @access 	private
	* @author	Ben Moody
	*/
	private function is_attachment_view() {
		
		//Init vars
		$screen	= get_current_screen();
		$result	= FALSE;
		
		if( isset($screen) ) {
		
			//Is this an attachment screen (base:upload or post_type:attachment)
			if( ($screen->id === 'attachment') || ($screen->id === 'upload') ) {
				$result = TRUE;
			}
			
		}
		
		return $result;
	}
	
	/**
	 * Helper to set all actions for plugin
	 */
	private function set_admin_actions() {
		
		//Loop options and init custom columns for each active view
		$this->init_custom_admin_columns();
		
	}
	
	/**
	 * Helper to enqueue all scripts/styles for admin views
	 */
	public function enqueue_admin_scripts() {
		
		//Init vars
		$js_inc_path 	= ZGITEMRATINGSZOOM__PLUGIN_URL . 'inc/js/';
		$css_inc_path 	= ZGITEMRATINGSZOOM__PLUGIN_URL . 'inc/css/';
		
		wp_enqueue_script( 'jquery' );
		
		//Enqueue this plugin's script
		wp_register_script( 'zg-item-ratings-zoom',
			$js_inc_path . 'zg-item-ratings-zoom-script.js',
			array('jquery'),
			'1.0'
		);
		wp_enqueue_script( 'zg-item-ratings-zoom' );
		
		//Enqueue stylesheet for plugin
		wp_enqueue_style( 'zg-item-ratings-zoom', 
			$css_inc_path . 'zg-item-ratings-zoom-style.css', 
			array(), 
			'1.0' 
		);
		
		//Localize ajax image load args
		wp_localize_script( 'zg-item-ratings-zoom',
			'zgItemRatingZoomVars',
			array(
				'ajax_url'	=> admin_url( 'admin-ajax.php' ),
				'action'	=> 'zg-item-ratings-zoom-get-image',
				'nonce'		=> wp_create_nonce( $this->ajax_nonce_key )
			)
		);
		
	}
	
	/**
	* get_zoom_image
	* 
	* CAlled by ajax action 'wp_ajax_zg-item-ratings-zoom-get-image'
	*
	* Gets attachement image img html required to display zoomed full size image
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function get_zoom_image() {
		
		//Init vars
		$nonce 		= NULL;
		$post_ID	= NULL;
		$image_src 	= NULL;
		$image_html	= NULL;
		$result		= FALSE;
		$data		= array();
		
		if( isset($_POST['zgItemRatingZoomNonce']) ) {
			
			$nonce = $_POST['zgItemRatingZoomNonce'];
			
			if ( ! wp_verify_nonce( $nonce, $this->ajax_nonce_key ) )
				die();
			
			//Check for post id in ajax request
			if( isset($_POST['zgItemRatingZooPostID']) ) {
			
				$post_ID = esc_attr( $_POST['zgItemRatingZooPostID'] );
				
				//Try and get attachment image
				$image_src = wp_get_attachment_image_src( $post_ID, 'full', FALSE );
				if( isset($image_src[0]) ) {
					$image_html = "<img src='{$image_src[0]}' />";
					
					$result = TRUE;
				}
				
			}
			
			//Test result and echo value for ajax call
			if( $result !== FALSE ) {
				
				$data = array(
					'imageHtml'		=> $image_html
				);
				wp_send_json_success( $data );
				
			} else {
				
				$result = array(
					'imageHtml'		=> NULL
				);
				wp_send_json_error( $data );
				
			}
			
		}
		
	}
	
	/**
	* init_custom_admin_columns
	* 
	* Loops all plugin config options and foreach one loops the
	* 'active_post_types' options calling the correct posts columns action
	* and filter based on the post type provided
	* 
	* @var		array	$options
	* @access 	private
	* @author	Ben Moody
	*/
	private function init_custom_admin_columns() {
		
		//Init vars
		$options 		= $this->parent_options;
		
		//Remove default 'icon' col as we will replace it
		add_filter( 'manage_media_columns', array($this, 'replace_icon_col') );
		
		//Add content to our new zg-zoom-icon column
		add_action('manage_media_custom_column', array($this, 'customize_media_thumbnail_column'), 10, 2);
		
	}
	
	/**
	* replace_icon_col
	* 
	* Called by 'manage_media_columns'
	*
	* Removes the default 'icon' columns and replaces it with our custom plugin icon column
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function replace_icon_col( $columns ) {
		
		//Init vars
		$new_columns = array();
		
		//Remove icon col from array
		unset( $columns['icon'] );
		
		//Add our own 'icon' col
		foreach( $columns as $key => $title ) {
			
			$new_columns[$key] = $title;
			
			if( $key === 'cb' ) {
				//Add our column in after cb col
				$new_columns[$this->custom_icon_col_name] = '';
			}
			
		}
		
		$columns = $new_columns;
		
		return $columns;
	}
	
	/**
	* customize_media_thumbnail_column
	* 
	* Called by 'manage_media_custom_column'
	*
	* Builds the html for our new custom 'icon' column
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function customize_media_thumbnail_column( $column_name, $post_ID ) {
		
		//Init vars
		$thumb			= NULL;
		$full_size		= NULL;
		$full_size_html	= NULL;
		$output 		= NULL;
		
		//Detect our column
		if( $column_name === $this->custom_icon_col_name ) {
			
			$thumb = wp_get_attachment_image( $post_ID, array( 80, 60 ), true );
			
			//Get full size image
			//$full_size = wp_get_attachment_image_src( $post_ID, 'full', false );
			if( isset($full_size[0]) ) {
				//$full_size_html = "<img src='{$full_size[0]}' />";
			}
			
			if( $thumb ) {
				
				$html = $this->get_rating_star_html(
		        	array(
						'post_ID'			=> $post_ID
					)
		        );
				
				ob_start();
		        ?>
		        <div class="zg-zoom-thumbnail-item" data-post-id="<?php esc_attr_e( $post_ID ); ?>">
			        <a href="<?php echo get_edit_post_link( $post_ID, true ); ?>" >
						<?php echo $thumb; ?>
					</a>
					<div class="zg-zoom-tooltip">
						<?php //echo $full_size_html; ?>
						<!-- Ajax loader !-->
						<div class="zg-zoom-loading"></div>
						<div class="zg-zoom-rating"><?php echo $this->get_rating_html( $post_ID ); ?></div>
					</div>
		        </div>
		        <?php
		        $output = ob_get_contents();
		        ob_end_clean();
		        
			}
			
			
		}
		
		echo $output;
	}
	
	private function get_rating_html( $post_ID ) {
		
		//INit vars
		$options 		= $this->parent_options;
		$rating_html	= NULL;
		$output			= "<ul class='zg-zoom-rating-items'>";
		
		//Loop all rating config options
		foreach( $options as $option ) {
			
			if( in_array('attachment', $option['active_post_types']) ) {
				//Set option defaults
				$option = $this->set_config_option_defaults( $option );
				
				$name				= $option['name'];
				
				$meta_key 			= $option['meta_key'];
				
				$column_slug 		= strtolower($option['meta_key']);
				
				$css_class 			= $option['css_class'];
				
				$disable_on_update 	= $option['disable_on_update'];
				
				//Cache current rating for item
		        $rating = $this->get_rating_value( $post_ID, $meta_key );
		        
		        $rating_html = $this->get_rating_star_html(
		        	array(
						'post_ID'			=> $post_ID,
						'column_slug'		=> $column_slug,
						'rating'			=> $rating,
						'disable_on_update'	=> $disable_on_update,
						'css_class'			=> $css_class
					)
		        );
		        
		        $output.= "<li><p>{$name}</p>{$rating_html}</li>";
			}
			
		}
		
		$output.= "</ul>";
		
		return $output;
	}
	
}



