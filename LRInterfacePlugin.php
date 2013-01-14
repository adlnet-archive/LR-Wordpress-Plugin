<?php
/*
Plugin Name: LR Interface Search
Plugin URI: http://adlnet.gov/
Description: Search bar for LR Interface
Author: ADL Tech Team
Version: 1
Author URI: http://adlnet.gov/
*/

//Load the individual widgets
include_once('LRInterfaceSearch.php');
include_once('LRInterfaceResults.php');

function registerWidgets(){

	register_widget("LRInterfaceSearch");
	register_widget("LRInterfaceResults");
}

function lr_enqueue_script(){

	wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', false );
}

add_action( 'widgets_init', 'registerWidgets' );
add_action( 'wp_enqueue_scripts', 'lr_enqueue_script' ); ?>