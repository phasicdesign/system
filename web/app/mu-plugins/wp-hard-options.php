<?php
/*
Plugin Name: WP Hard Options
Plugin URI: https://timnash.co.uk/wordpress-hard-coded-options/
Description: Checks Hard Coded WP Options
Version: 0.7
Author: Tim Nash
Author URI: https://timnash.co.uk
License: GPL2
*/
class WP_Hard_Options{

	public static $instance;

	/**
	 * Construct
	 *
	 * @since 0.1
	 * @param null
	 * @return null
	 *
	 **/
	function __construct() {
		self::$instance = $this;

		//Allows you to specify alternate prefix in wp-config.php or elsewhere
		if(!defined( 'WP_OPTIONS_PREFIX' )){
			define( 'WP_OPTIONS_PREFIX', 'WP_OPTIONS' );
		}

		$settings = $this->get_settings();
		$this->set_settings($settings);

		$constants = $this->get_constants();
		$this->set_constants($constants);


	}

	function get_settings() {
		$settings = array();
		if (realpath(ABSPATH . '../../config/settings.php')) {
			$settings = include realpath(ABSPATH . '../../config/settings.php');
		}
    	if(empty( $settings )) {
    		return false;
    	}
    	else {
    		//Use if you want to return a second prefix for example
    		return apply_filters('wp_hard_options', $settings );
    	}
	}

	function set_settings($options) {

		if ($options == false)
			return;

		foreach ($options as $key => $value) {
			$noptions['WP_OPTIONS_'.strtoupper($key)] = $value;
		}
		if(!empty($noptions) && is_array($noptions)){
			foreach( $noptions as $option => $value ){
				define($option, $value);
			}
		}
	}


	/**
	 * Get Constants
	 * Cycle through all constants, return those with the wp_options prefix
	 *
	 * @since 0.1
	 * @param null (since 0.4)
	 * @return false | array($dump)
	 *
	 **/
	function get_constants (){
		$dump = array();
    	foreach ( get_defined_constants() as $key => $value ) {
        	if (substr( $key,0, strlen( WP_OPTIONS_PREFIX ) ) == WP_OPTIONS_PREFIX ) {
        		$dump[$key] = $value;
        	}
    	}
    	if(empty( $dump )) {
    		return false;
    	}
    	else {
    		//Use if you want to return a second prefix for example
    		return apply_filters('wp_hard_options', $dump );
    	}
	}

	function set_constants($options) {

		if(!empty($options) && is_array($options)){
			foreach( $options as $option => $value ){
				$wp_option = explode( WP_OPTIONS_PREFIX .'_' , $option);
				// var_dump($wp_option);
				$name = end($wp_option);
				$method_name = strtolower( $name );
				if( $method_name != WP_OPTIONS_PREFIX ){

					// var_dump($method_name);
					$filter_name = 'pre_option_'.$method_name;
					//Add filer pre_option_xxxx using call to catch xxxx and return constant
					add_filter( $filter_name, array($this, $method_name ) );
				}
			}
		}
	}

	/**
	 * Get Option Name
	 * Returns the option name when passed a constant
	 *
	 * @since 0.6
	 * @param string($constant)
	 * @return string($name)
	 *
	 **/
	function get_option_name( $constant ){
		$name = end(explode(WP_OPTIONS_PREFIX.'_', $constant));
		//force back to lower
		$name = strtolower( $name );
		return apply_filters('wp_hard_options_option_name', $name );
	}

	/**
	 * Get Constant Name
	 * Returns the constant Key
	 *
	 * @since 0.5
	 * @param string($method)
	 * @return string($constant)
	 *
	 **/
	function get_constant_name( $method )
	{
		$constant = strtoupper(WP_OPTIONS_PREFIX.'_'. $method);
		return apply_filters('wp_hard_options_constant_name', $constant );
	}

	/**
	 * Is Hard Option
	 * Check if the option is hard coded, currently this won't tell you if option exists in DB
	 *
	 * @since 0.6
	 * @param string($option)
	 * @return bool(true/false)
	 * @todo flag if is in db;
	 *
	 **/
	function is_hard_option( $option, $db = false )
	{
		if(defined( $this->get_constant_name( $option ))){
			//woot we can return option
			return true;
		}
		return false;
	}

	/**
	 * Magic Method Madness
	 * Create method and any arguments return defined constant content
	 *
	 * @since 0.1
	 * @param string($method), mixed($arg)
	 * @return false | string($option)
	 *
	 **/
	function __call( $method, $arg = false ){
		$value = false;
		//Define if we are going to cache/retrieve from cache for a single method
		$cache = apply_filters( 'wp_hard_options_cache_'.$method , true);
		//Check if we have already determined if we should be using cache, and check defaults override
		if( $cache && ( defined( 'WP_HARD_OPTIONS_CACHE' ) && WP_HARD_OPTIONS_CACHE == false )){
			$cache = false;
		}
		if( $cache ){
			//Check if it's cached, if we are using the DB as a caching engine this has just become self defeating!
			$value = wp_cache_get( $method, 'options' );
		}
		if(!$value){
			$option = $this->get_constant_name( $method );
			if( defined( $option )){
				$value = constant( $option );
				if( $cache ){
					//set the cache for next time
					wp_cache_add( $method, $value, 'options' );
				}
			}
			else{
				return false;
			}
		}
		//Return it back complete with previous filters
		return apply_filters( 'option_' . $method, maybe_unserialize( $value ) );

	}
}

new WP_Hard_Options;
