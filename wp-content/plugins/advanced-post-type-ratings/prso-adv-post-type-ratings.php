<?php
/*
 * Plugin Name: Advanced Post Type Ratings by Benjamin Moody & Zeitguys Inc.
 * Plugin URI: http://www.BenjaminMoody.com & http://www.zeitguys.com
 * Description: Provides star ratings option for all post types, includes thumbnail zoom for media list view.
 * Author: Benjamin Moody
 * Version: 1.01
 * Author URI: http://www.BenjaminMoody.com
 * License: GPL2+
 * Text Domain: zg_item_ratings
 * Domain Path: /languages/
 */

//Define plugin constants
define( 'ZGITEMRATINGS__MINIMUM_WP_VERSION', '3.0' );
define( 'ZGITEMRATINGS__VERSION', '1.01' );
define( 'ZGITEMRATINGS__DOMAIN', 'zg-item-ratings-plugin' );

//Plugin admin options will be available in global var with this name, also is database slug for options
define( 'ZGITEMRATINGS__OPTIONS_NAME', 'zg_item_ratings' );

define( 'ZGITEMRATINGS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZGITEMRATINGS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//Include plugin classes
require_once( ZGITEMRATINGS__PLUGIN_DIR . 'class.zg-item-ratings.php'               );

//Set Activation/Deactivation hooks
register_activation_hook( __FILE__, array( 'ZgItemRatings', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'ZgItemRatings', 'plugin_deactivation' ) );

/**
* zg_item_ratings_init
*
* Set config options for plugin and create instance of plugin class
* 
* Example of config array:
	$config_options = array(
		array(
			'meta_key'			=>	'META_KEY_POST_RATING',
			'name'				=>	'Item Ratings',
			'disable_on_update'	=>	FALSE,
			'active_post_types'	=>	array(
				'page',
				'attachment'
			)
		),
		array(
			'meta_key'			=>	'META_KEY_POST_RATING_2',
			'name'				=>	'Item Ratings 2',
			'disable_on_update'	=>	FALSE,
			'active_post_types'	=>	array(
				'attachment'
			)
		)
	);
*
* @access 	public
* @author	Ben Moody
*/
zg_item_ratings_init();
function zg_item_ratings_init() {
	
	//Init vars
	global $zg_item_ratings_options;
	$config_options = array();
	
	//Cache plugin options array
	$zg_item_ratings_options = get_option( 'zg_item_ratings' );
	
	//Build config array for each instance of rater
	if( isset($zg_item_ratings_options['ratings_instances']) ) {
		foreach( $zg_item_ratings_options['ratings_instances'] as $rater_title ) {
		
			$_title_slug = hash("crc32b", $rater_title);
			
			//Set post types option
			$_post_types = array();
			if( isset($zg_item_ratings_options["zgir_pt_{$_title_slug}"]) ) {
				foreach( $zg_item_ratings_options["zgir_pt_{$_title_slug}"] as $rater_post_type ) {
					$_post_types[] = $rater_post_type;
				}
			}
			
			//Cache min / max levels
			$_min_level = NULL;
			$_max_level = NULL;
			if( isset($zg_item_ratings_options["zgir_lv_{$_title_slug}"][1], $zg_item_ratings_options["zgir_lv_{$_title_slug}"][2]) ) {
				$_min_level = $zg_item_ratings_options["zgir_lv_{$_title_slug}"][1];
				$_max_level = $zg_item_ratings_options["zgir_lv_{$_title_slug}"][2];
			}
			
			$config_options[] = array(
				'meta_key'			=>	'zgir_'.$_title_slug,
				'name'				=>	$rater_title,
				'disable_on_update'	=>	FALSE,
				'max_rating_size'	=> 	(int) $_max_level,
				'min_rating_size'	=> 	(int) $_min_level,
				'active_post_types'	=>	$_post_types
			);
		}
	}
	
	//Instatiate plugin class and pass config options array
	new ZgItemRatings( $config_options );
		
}

//Include Item ratings zoom extension plugin
require_once( ZGITEMRATINGS__PLUGIN_DIR . 'inc/plugins/zg-item-ratings-zoom/zg-item-ratings-zoom.php'               );

