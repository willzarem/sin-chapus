<?php

//Define plugin constants
define( 'ZGITEMRATINGSZOOM__VERSION', '1.0' );
define( 'ZGITEMRATINGSZOOM__DOMAIN', 'zg-item-ratings-zoom-plugin' );
define( 'ZGITEMRATINGSZOOM__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZGITEMRATINGSZOOM__PLUGIN_URL', plugin_dir_url( __FILE__ ) );


//Init plugin during 'admin_init' action to ensure parent is instatiated
add_action( 'admin_init', 'zg_item_ratings_zoom_init' );
function zg_item_ratings_zoom_init() {
		
	//Include plugin classes
	require_once( ZGITEMRATINGSZOOM__PLUGIN_DIR . 'class.zg-item-ratings-zoom.php' );
	
	//Instatiate plugin class and pass config options array
	new ZgItemRatingsZoom();
	
}

