<?php
/**
 * Plugin Name: Unveil Lazy Load
 * Description: This plugin makes lazy-image-load possible to decrease number of requests and improve page loading time, and uses jQuery plugins <a href="https://github.com/luis-almeida/unveil">Unveil.js</a> to only load an image when it's visible in the viewport.
 * Version: 0.1
 * Author: Daisuke Maruyama
 * Author URI: http://marubon.info/
 * Plugin URI: https://github.com/marubon
 * License: GPL2
 * 
 * Uses jquery.unveil by Luís Almeida: https://github.com/luis-almeida/unveil 
 * License of jquery.unveil is based on its license. please refer to the above url.
 */

if ( ! class_exists( 'Unveil_Images' ) ) :

class Unveil_Images {

	const version = '0.1';

	function __construct() {
		if ( is_admin() )
			return;

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_filter( 'the_content', array( __CLASS__, 'add_dummy_image' ), 99 ); 
		add_filter( 'post_thumbnail_html', array( __CLASS__, 'add_dummy_image' ), 11 );
		add_filter( 'get_avatar', array( __CLASS__, 'add_dummy_image' ), 11 );
	}

	function load_scripts() {
		
		wp_enqueue_script( 'unveil',  self::get_url( 'js/jquery.unveil.min.js' ), array( 'jquery' ), self::version, true );

	    global $wp_scripts;
	  
		$wp_scripts -> add_data(
			'unveil',
			'data',
			'jQuery(document).ready(function(){jQuery("img").unveil();});'
		);
	}
  
    function add_dummy_image( $content ) {
		
		if( is_feed() || is_preview() || is_smartphone() )
			return $content;

		if ( strpos( $content, 'data-src' ) !== false )
			return $content;
	  
	  	$dummy_image = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
	  	  
		$content = preg_replace( '#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', sprintf( '<img${1}src="%s" data-src="${2}"${3}><noscript><img${1}src="${2}"${3}></noscript>', $dummy_image ), $content );

		return $content;
	}

	function get_url( $path = '' ) {
		return plugins_url( ltrim( $path, '/' ), __FILE__ );
	}
  
 	function is_smartphone() {
    	$useragents = array(
            'iPhone',         // iPhone
            'iPod',           // iPod touch
            'Android',        // Android
            'blackberry',     // blackberry
            'webOS',          // Palm
	        'Windows Phone' //Windows Phone
        );
  
    	foreach ($useragents as $useragent) {	  
		  	if(strpos($_SERVER['HTTP_USER_AGENT'], $useragent) !== false){
			  	return true;
	  		}
    	}

    	return false;
	}

}

$unveil_image = new Unveil_Images();

endif;