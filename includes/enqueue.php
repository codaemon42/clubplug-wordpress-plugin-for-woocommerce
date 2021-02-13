<?php
add_action('wp_enqueue_scripts', 'naims_enqueue_links');
function naims_enqueue_links(){
    wp_enqueue_script(
        'mohaazon-main-js',
        plugin_dir_url( CLUBPLUG ) . 'assets/js/main.js',
        ['jquery'],
        '1.0.0',
        true
    );

    wp_enqueue_style( 
        'mohaazon-main-css', 
        plugin_dir_url( CLUBPLUG ) . 'assets/main.css', 
        '', 
        false, 
        'all' 
    );
}

add_action( 'admin_enqueue_scripts', 'misha_include_js' );
function misha_include_js() {
 
	// I recommend to add additional conditions just to not to load the scipts on each page
 
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
 
 	wp_enqueue_script( 'myuploadscript', plugin_dir_url( CLUBPLUG ) . 'assets/js/admin-main.js', array( 'jquery' ) );
}