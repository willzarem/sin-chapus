<?php
class ZgItemRatings {
	
	protected static $class_config 				= array();
	protected $current_screen					= NULL;
	private	  $plugin_ajax_nonce				= 'zg-item-ratings-ajax-nonce';
	protected $plugin_path						= ZGITEMRATINGS__PLUGIN_DIR;
	protected $plugin_url						= ZGITEMRATINGS__PLUGIN_URL;
	protected $plugin_textdomain				= ZGITEMRATINGS__DOMAIN;
	
	//Helper to set default config options for plugin
	protected function set_config_option_defaults( $args, $is_all_options = FALSE ) {
		
		//Init vars
		$class_config_defaults = array(
			'name'				=>	NULL,
			'max_rating_size'	=> 	5,
			'min_rating_size'	=> 	0,
			'rating_step_size'	=> 	1,
			'css_class'			=>	NULL,
			'disable_on_update'	=> 	TRUE,
		);
		$output = array();
		
		if( $is_all_options === TRUE ) {
			foreach( $args as $key => $options ) {
				$output[$key] = wp_parse_args( $options, $class_config_defaults );
			}
		} else {
			$output = wp_parse_args( $args, $class_config_defaults );
		}
		
		return $output;
		
	}
	
	function __construct( $config = array() ) {
		
		//Cache plugin congif options
		self::$class_config = $config;
		
		//Set textdomain
		add_action( 'after_setup_theme', array($this, 'plugin_textdomain') );
		
		//Init plugin
		add_action( 'init', array($this, 'init_options') );
		add_action( 'current_screen', array($this, 'init_plugin') );
		
		//Add Ajax actions
		add_action("wp_ajax_item-rate-update", array($this, 'ajax_item_rate_update'));
		
	}
	
	/**
	 * Setup plugin textdomain folder
	 * @public
	 */
	public function plugin_textdomain() {
		
		load_plugin_textdomain( $this->plugin_textdomain, FALSE, $this->plugin_path . '/languages/' );
		
	}
	
	/**
	* Overloading
	* 
	* Make use of __call magic method to allow the same plugin method
	* to be used as teh callback for multiple instances of wordpress custom column filter/action
	* 
	* This tricks wordpress filter/action into thinking each callback is a unique method when in fact
	* one method is handling the logic and returning the value.
	*
	* WHY?
	* This was important to allow us to keep the config option array and thus easily manage multiple
	* RAting systems from one place. We loop over the config options array and init each instance as we go
	* using the same methods to do it thus we don't have to duplicate code via class extensions :)
	*
	* @access 	public
	* @author	Ben Moody
	*/
	function __call( $method, $args ) {
		
		//Init vars
		$method_explode = NULL;
		
		//Parse requested method name
		$method_explode = explode('-', $method);
		
		//Detect calls to add_custom_column method
		if( isset($method_explode[0]) && ($method_explode[0] === 'add_custom_column') ) {
			//Call add_custom_column method and pass args along
			return $this->add_custom_column( $method_explode[1], $args[0] );
		}
		
		//Detect calls to add_custom_column_content method
		if( isset($method_explode[0]) && ($method_explode[0] === 'add_custom_column_content') ) {
			//Call add_custom_column_content method and pass args along
			return $this->add_custom_column_content( $method_explode[1], $args[0], $args[1] );
		}
		
		//Detect calls to add_meta_box callback method
		if( isset($method_explode[0]) && ($method_explode[0] === 'add_custom_meta_box') ) {
			//Call add_custom_column method and pass args along
			return $this->add_custom_meta_box( $method_explode[1] );
		}
		
		//Detect calls to custom sortable columns method
		if( isset($method_explode[0]) && ($method_explode[0] === 'manage_sortable_columns') ) {
			//Call manage_sortable_columns method and pass args along
			return $this->manage_sortable_columns( $method_explode[1], $args[0] );
		}
		
		//Detect calls to custom sortable columns pre get posts method
		if( isset($method_explode[0]) && ($method_explode[0] === 'custom_column_sort_query') ) {
			//Call custom_column_sort_query method and pass args along
			return $this->custom_column_sort_query( $method_explode[1], $args[0] );
		}
		
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
		
		//Init vars
		$options 		= self::$class_config;
		
		if( !empty($options) && is_admin() ) {
		
			//Confirm we are on an active admin view
			if( $this->is_active_view() ) {
				
				//PLUGIN OPTIONS FRAMEWORK -- comment out if you dont need options
				$this->load_redux_options_framework();
				
				//Set plugin admin actions
				$this->set_admin_actions();
				
				//Enqueue admin scripts
				add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );
				
				
				
			}
			
		}
		
	}
	
	public function init_options() {
		
		//Init vars
		$options 		= self::$class_config;
		
		if( is_admin() ) {
		
			//PLUGIN OPTIONS FRAMEWORK -- comment out if you dont need options
			$this->load_redux_options_framework();
			
		}
		
	}
	
	/**
	* load_redux_options_framework
	* 
	* Loads Redux options framework as well as the unique config file for this plugin
	*
	* NOTE!!!!
	*			You WILL need to make sure some unique constants as well as the class
	*			name in the plugin config file 'inc/ReduxConfig/ReduxConfig.php'
	*
	* @access 	public
	* @author	Ben Moody
	*/
	protected function load_redux_options_framework() {
		
		//Init vars
		$framework_inc 		= $this->plugin_path . 'inc/ReduxFramework/ReduxCore/framework.php';
		$framework_config	= $this->plugin_path . 'inc/ReduxConfig/ReduxConfig.php';
		
		//Try and load redux framework
		if ( !class_exists('ReduxFramework') && file_exists($framework_inc) ) {
			require_once( $framework_inc );
		}
		
		//Try and load redux config for this plugin
		if ( file_exists($framework_config) ) {
			require_once( $framework_config );
		}
		
	}
	
	/**
	* is_active_view
	* 
	* Detects if current admin view has been set as 'active_post_type' in
	* plugin config options array.
	* 
	* @var		array	self::$class_config
	* @var		array	$active_views
	* @var		obj		$screen
	* @var		string	$current_screen
	* @return	bool	
	* @access 	protected
	* @author	Ben Moody
	*/
	protected function is_active_view() {
		
		//Init vars
		$options 		= self::$class_config;
		$active_views	= array();
		$screen			= get_current_screen();
		$current_screen	= NULL;
		
		//Cache all views plugin will be active on
		$active_views = $this->get_active_views( $options );
		
		//Cache the current view
		if( isset($screen) ) {
		
			//Is this an attachment screen (base:upload or post_type:attachment)
			if( ($screen->id === 'attachment') || ($screen->id === 'upload') ) {
				$current_screen = 'attachment';
			} else {
				
				//Cache post type for all others
				$current_screen = $screen->post_type;
				
			}
			
			//Cache current screen in class protected var
			$this->current_screen = $current_screen;
		}
		
		//Finaly lets check if current view is an active view for plugin
		if( in_array($current_screen, $active_views) ) {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}
	
	/**
	* get_active_views
	* 
	* Interates over plugin config options array merging all
	* 'active_post_type' values into single array
	* 
	* @param	array	$options
	* @var		array	$active_views
	* @return	array	$active_views
	* @access 	private
	* @author	Ben Moody
	*/
	private function get_active_views( $options = array() ) {
		
		//Init vars
		$active_views = array();
		
		//Loop options and cache each active post view
		foreach( $options as $option ) {
			if( isset($option['active_post_types']) ) {
				$active_views = array_merge($active_views, $option['active_post_types']);
			}
		}
		
		return $active_views;
	}
	
	/**
	 * Helper to set all actions for plugin
	 */
	private function set_admin_actions() {
		
		//Loop options and init custom columns for each active view
		$this->init_custom_admin_columns();
		
		//Loop options and init custom meta boxes
		$this->init_custom_meta_boxes();
		
	}
	
	/**
	 * Helper to enqueue all scripts/styles for admin views
	 */
	public function enqueue_admin_scripts() {
		
		//Init vars
		$js_inc_path 	= ZGITEMRATINGS__PLUGIN_URL . 'inc/js/';
		$css_inc_path 	= ZGITEMRATINGS__PLUGIN_URL . 'inc/css/';
		
		wp_enqueue_script( 'jquery' );
		
		//Enqueue scripts for Rate It js plugin
		wp_register_script( 'rate-it',
			$js_inc_path . 'rate-it/jquery.rateit.min.js',
			array('jquery'),
			'1.0.16'
		);
		wp_enqueue_script( 'rate-it' );
		
		//Enqueue this plugin's script
		wp_register_script( 'zg-item-ratings',
			$js_inc_path . 'zg-item-ratings-script.js',
			array('rate-it'),
			'1.0'
		);
		wp_enqueue_script( 'zg-item-ratings' );
		
		//Enqueue stylesheet for rate it plugin - can be de-enqueued and replaced by themes :)
		wp_enqueue_style( 'zg-item-ratings-stars', 
			$css_inc_path . 'zg-item-ratings-stars.css', 
			array(), 
			'1.0' 
		);
		
		//Enqueue stylesheet for plugin
		wp_enqueue_style( 'zg-item-ratings', 
			$css_inc_path . 'zg-item-ratings-style.css', 
			array(), 
			'1.0' 
		);
		
		//Localize vars
		$this->localize_script();
		
	}
	
	/**
	* localize_script
	* 
	* Helper to localize all vars required for plugin JS.
	* 
	* @var		string	$object
	* @var		array	$js_vars
	* @access 	private
	* @author	Ben Moody
	*/
	private function localize_script() {
		
		//Init vars
		$object 	= 'ZgItemRatingsVars';
		$js_vars	= array();
		
		//Localize vars for ajax requests
		$js_vars['ajaxUrl']				= admin_url( 'admin-ajax.php' );
		$js_vars['ajaxNonce'] 			= wp_create_nonce( $this->plugin_ajax_nonce );
		
		//Localize vars for rate update ajax action
		$js_vars['rateUpdateAction'] = 'item-rate-update';
		
		//Localize plugin config options
		$js_vars['pluginConfigOptions'] = $this->set_config_option_defaults( self::$class_config, TRUE );
		
		//Translation text for ajax rating update error
		$js_vars['ajaxRateUpdateErrorText'] = _x( 
			'There was a problem updating the rating. Please try again.', 
			'Alert message for users', 
			ZGITEMRATINGS__DOMAIN 
		);
		
		wp_localize_script( 'zg-item-ratings', $object, $js_vars );
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
		$options 		= self::$class_config;
		
		//Loop plugin config options and init custom columns for each
		foreach( $options as $option ) {
			
			//Setup actions and filters for requested post type views
			foreach( $option['active_post_types'] as $view ) {
				switch( $view ) {
					case 'attachment':
						//Note we append option mete key value to create a unique method name for overloading - see $this->__call
						add_filter('manage_media_columns', array($this, 'add_custom_column-' .$option['meta_key'] ), 10);  
						add_action('manage_media_custom_column', array($this, 'add_custom_column_content-' .$option['meta_key']), 10, 2);
						//Make sure column is sortable
						add_filter('manage_upload_sortable_columns', array($this, 'manage_sortable_columns-' .$option['meta_key']) );
						//Add query filter to pre get posts to set sort query vars
						add_action('pre_get_posts', array($this, 'custom_column_sort_query-' .$option['meta_key']) );
						break;
					case 'post';
						//Note we append option mete key value to create a unique method name for overloading - see $this->__call
						add_filter('manage_post_posts_columns', array($this, 'add_custom_column-' .$option['meta_key'] ), 10);  
						add_action('manage_post_posts_custom_column', array($this, 'add_custom_column_content-' .$option['meta_key']), 10, 2);
						//Make sure column is sortable
						add_filter('manage_edit-post_sortable_columns', array($this, 'manage_sortable_columns-' .$option['meta_key']) );
						//Add query filter to pre get posts to set sort query vars
						add_action('pre_get_posts', array($this, 'custom_column_sort_query-' .$option['meta_key']) );
						break;
					default:
						//Note we append option mete key value to create a unique method name for overloading - see $this->__call
						add_filter('manage_'. $view .'_posts_columns', array($this, 'add_custom_column-' .$option['meta_key'] ), 10);  
						add_action('manage_'. $view .'_posts_custom_column', array($this, 'add_custom_column_content-' .$option['meta_key']), 10, 2);
						//Make sure column is sortable
						add_filter('manage_edit-'. $view .'_sortable_columns', array($this, 'manage_sortable_columns-' .$option['meta_key']) );
						//Add query filter to pre get posts to set sort query vars
						add_action('pre_get_posts', array($this, 'custom_column_sort_query-' .$option['meta_key']) );
						break;
				}
			}
			
		}
		
	}
	
	/**
	* add_custom_column
	* 
	* Called By Filters: 'manage_media_columns', 'manage_post_posts_columns', 'manage_'. [post_type] .'_posts_columns'
	* 
	* @param	array	$defaults
	* @return	array	$defaults
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_custom_column( $config_option_key, $defaults ) {
		
		//Init vars
		$column_slug	= NULL;
		$column_name	= NULL;
		
		//Set column params
		foreach( self::$class_config as $option ) {
			if( $option['meta_key'] === $config_option_key ) {
			
				$column_slug = strtolower($option['meta_key']);
				$column_name = $option['name'];
				
				//Add custom column
				$defaults[$column_slug]  = $column_name;
				break;
			}
		}
		  
	    return $defaults; 
		
	}
	
	/**
	* manage_sortable_columns
	* 
	* Called By Filters: 'manage_[post_type]_sortable_columns'
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function manage_sortable_columns( $column_key, $columns ) {
		
		//Init vars
		$column_name	= NULL;
		
		//Addend custom column into sort array
		$column_name = strtolower( $column_key );
		$columns[ $column_name ] = $column_name;
		
		return $columns;
	}
	
	/**
	* custom_column_sort_query
	* 
	* Called By Action: 'pre_get_posts'
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function custom_column_sort_query( $column_key, $query ) {
		
		//Init vars
		$column_name	= NULL;
		
		if( ! is_admin() )  
			return;  
		
		$column_name = strtolower( $column_key );
		
		$orderby = $query->get( 'orderby');  
		
		if( $column_name == $orderby ) {  
			$query->set('meta_key',$column_name);  
			$query->set('orderby','meta_value_num');  
		} 
		
	}
	
	/**
	* add_custom_column
	* 
	* Called By Actions: 'manage_media_custom_column', 'manage_post_posts_custom_column', ''manage_'. [post_type] .'_posts_custom_column''
	* 
	* @param	string	$column_name
	* @param	int		$post_ID
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_custom_column_content( $config_option_key, $column_name, $post_ID ) {

		//Init vars
		$meta_key		= NULL;
		$column_slug	= NULL;
		$rating			= NULL;
		$css_class   	= NULL;
		$option 		= NULL;
		$output 		= NULL;
		
		//Set column params
		foreach( self::$class_config as $option ) {
			
			//Set option defaults
			$option = $this->set_config_option_defaults( $option );
			
			if( $option['meta_key'] === $config_option_key ) {
				
				$meta_key 			= $option['meta_key'];
				
				$column_slug 		= strtolower($option['meta_key']);
				
				$css_class 			= $option['css_class'];
				
				$disable_on_update 	= $option['disable_on_update'];
				
				break;
			}
		}

		if ($column_name == $column_slug) {  
	        
	        //Cache current rating for item
	        $rating = $this->get_rating_value( $post_ID, $meta_key );
	        
	        $output = $this->get_rating_star_html(
	        	array(
					'post_ID'			=> $post_ID,
					'column_slug'		=> $column_slug,
					'rating'			=> $rating,
					'disable_on_update'	=> $disable_on_update,
					'css_class'			=> $css_class
				)
	        );
	        
	        //Echo out content
	        echo apply_filters( 'zg_item_ratings_column_stars', $output, $column_name, $option, $post_ID );
	    }
		
	}
	
	/**
	* init_custom_meta_boxes
	* 
	* Loops all plugin config options and Sets params and calls add_meta_box
	* for each instance of the plugin
	* 
	* @var		array	$options
	* @access 	private
	* @author	Ben Moody
	*/
	private function init_custom_meta_boxes() {
		
		//Init vars
		$options 		= self::$class_config;
		
		//Loop plugin config options and init meta boxes for each
		foreach( $options as $option ) {
			
			//Setup actions and filters for requested post type views
			foreach( $option['active_post_types'] as $view ) {
				
				//Set parameters
				$id 		= strtolower( $option['meta_key'] );
				$title		= $option['name'];
				$post_type 	= $view;
				$context 	= 'side';
				$priority 	= 'high';
				
				add_meta_box(
					$id,
					$title,
					array($this, 'add_custom_meta_box-' .$option['meta_key'] ),
					$post_type,
					$context,
					$priority
				);
				
			}
			
		}
		
	}
	
	/**
	* add_custom_meta_box
	* 
	* Callback function for add_meta_box() called in $this->init_custom_meta_boxes
	*
	* Gets star rating value and sets html output
	* 
	* @param	string	$config_option_key
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_custom_meta_box( $config_option_key ) {
		
		//Init vars
		global $post;
		$post_ID 		= NULL;
		$meta_key		= NULL;
		$rating			= NULL;
		$option 		= NULL;
		$output 		= NULL;
		
		$post_ID = $post->ID;
		
		//Set column params
		foreach( self::$class_config as $option ) {
			
			//Set option defaults
			$option = $this->set_config_option_defaults( $option );
			
			if( $option['meta_key'] === $config_option_key ) {
				
				$meta_key 			= $option['meta_key'];
				
				$column_slug 		= strtolower($option['meta_key']);
				
				$css_class 			= $option['css_class'];
				
				$disable_on_update 	= $option['disable_on_update'];
				
				//Cache current rating for item
		        $rating = $this->get_rating_value( $post_ID, $meta_key );
		        
		        $output = $this->get_rating_star_html(
		        	array(
						'post_ID'			=> $post_ID,
						'column_slug'		=> $column_slug,
						'rating'			=> $rating,
						'disable_on_update'	=> $disable_on_update,
						'css_class'			=> $css_class
					)
		        );
		        
		        //Echo out content
		        echo apply_filters( 'zg_item_ratings_metabox_stars', $output, $option, $post_ID );
				
				break;
			}
		}
		
	}
	
	/**
	* get_rating_star_html
	* 
	* Helper to create html required for star rating element
	* 
	* @access 	protected
	* @author	Ben Moody
	*/
	protected function get_rating_star_html( $args = array() ) {
		
		//Init vars
		$output = NULL;
		$defaults = array(
			'post_ID'			=> NULL,
			'column_slug'		=> NULL,
			'rating'			=> NULL,
			'disable_on_update'	=> NULL,
			'css_class'			=> NULL
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		extract( $args );
		
		ob_start();
        ?>
        <div data-itemid="<?php esc_attr_e($post_ID); ?>" data-ratinggroupid="<?php esc_attr_e($column_slug); ?>" data-rateit-value="<?php esc_attr_e($rating); ?>" data-disablerating="<?php esc_attr_e($disable_on_update); ?>" class="zg-item-ratings-rateit <?php esc_attr_e($post_ID); ?> <?php esc_attr_e($column_slug); ?> <?php esc_attr($css_class); ?>"></div>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
		
		return $output;
	}
	
	/**
	* ajax_item_rate_update
	* 
	* Ajax action to update a post's (item's) rate meta value
	*
	* Note that the method loops the plugin config options array to make sure that
	* the key used to identify the rating group is valid, then uses the meta key from the
	* options array to store the rate value for the post.
	*
	* Note the json encode result array
	* 
	* @var		string	$nonce				//Nonce key stored in class global
	* @var		string	$config_option_key	//The config option key for current rating group (basically 'meta_key' value in config)
	* @var		mixed	$result
	* @param	mixed	$result
	* @access 	public
	* @author	Ben Moody
	*/
	public function ajax_item_rate_update() {
		
		//Init vars
		$nonce 				= $this->plugin_ajax_nonce;
		$config_option_key	= NULL;	
		$result				= FALSE;
		
		if ( !wp_verify_nonce( $_REQUEST['zgItemRateNonce'], $nonce)) {
	    	exit();
	    } 
		
		//Cache config option key for this rating group
		if( isset($_POST['ratingGroup']) ) {
			
			$config_option_key = esc_attr( $_POST['ratingGroup'] );
			
			//Confirm this is a valid config option
			foreach( self::$class_config as $option ) {
				
				//Lower case meta key for comparison
				$option_meta_key_lower = strtolower($option['meta_key']);
				

				if( $option_meta_key_lower === $config_option_key ) {

					//Great, this is a valid meta key lets cache the post meta value key
					$post_meta_key = $option['meta_key'];
					
					//Cache the post ID and the new rating value
					$post_ID 	= esc_attr( $_POST['itemID'] );
					$rate_value	= esc_attr( $_POST['rateValue'] );
					
					//Call method to save meta data
					$result = $this->update_rating_value( $post_ID, $rate_value, $post_meta_key );
					
					//end loop
					break;
				}
			}
			
		}
		
		//Test result and echo value for ajax call
		if( $result !== FALSE ) {
			
			$result = array(
				'result' 		=> 'OK',
				'rateValue'		=> $rate_value
			);
			
		} else {
			
			$result = array(
				'result' 		=> 'ERROR',
				'rateValue'		=> $rate_value
			);
			
		}
		
		echo json_encode($result);
		exit;
	}
	
	/**
	* update_rating_value
	* 
	* Helper to update rate meta value for a post
	* 
	* @access 	protected
	* @author	Ben Moody
	*/
	protected function update_rating_value( $post_ID, $rate_value, $post_meta_key ) {
		
		//Init vars
		$result = NULL;
		
		//Update post meta data
		$result = update_post_meta( $post_ID, $post_meta_key, $rate_value );
		
		return $result;
	}
	
	/**
	* get_rating_value
	* 
	* Helper to GET rate meta value for a post
	* 
	* @access 	protected
	* @author	Ben Moody
	*/
	protected function get_rating_value( $post_ID, $post_meta_key ) {
		
		//Init vars
		$result = NULL;
		
		$result = get_post_meta( $post_ID, $post_meta_key, TRUE );
		
		return $result;
	}
	
	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation( $network_wide ) {
		
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		
	}
	
}



