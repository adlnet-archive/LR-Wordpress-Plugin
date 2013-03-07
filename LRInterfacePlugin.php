<?php
/*
Plugin Name: LR Interface Search
Plugin URI: http://adlnet.gov/
Description: Search bar for LR Interface
Author: ADL Tech Team
Version: 1
Author URI: http://adlnet.gov/
*/

function sanitize_lr($str, $opt){

	return preg_replace('/[^a-zA-Z0-9'.$opt.']+/', '', $str);
}

//Load the individual widgets
include_once('widgets/LRInterfaceSearch.php');
include_once('widgets/LRInterfaceResults.php');
include_once('widgets/LRInterfaceStandards.php');
include_once('widgets/LRInterfaceTimeline.php');
include_once('widgets/LRInterfaceFeatured.php');
include_once('widgets/LRInterfaceFeaturedSearch.php');
include_once('widgets/LRInterfaceSubject.php');
include_once('widgets/LRInterfaceCount.php');
include_once('LRInterfacePluginSettings.php');

function registerWidgets(){

	register_widget("LRInterfaceSearch");
	register_widget("LRInterfaceResults");
	register_widget("LRInterfaceTimeline");
	register_widget("LRInterfaceStandards");
	register_widget("LRInterfaceFeatured");
	register_widget("LRInterfaceSubject");
	register_widget("LRInterfaceCount");
	register_widget("LRInterfaceFeaturedSearch");
}

function lr_enqueue_script(){

	wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', false );
	wp_enqueue_script( 'knockout', 'https://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.0.js', false );
	wp_enqueue_script( 'hexmd5', plugins_url( "scripts/hexmd5.js" , __FILE__ ) , false );
	wp_enqueue_script( 'lrinterface', plugins_url( "scripts/application.js" , __FILE__ ) , false );
	wp_enqueue_script( 'spinjs', plugins_url( "scripts/spin.min.js" , __FILE__ ) , false );
	wp_enqueue_script( 'placeholder', plugins_url( "scripts/Placeholders.js" , __FILE__ ) , false );
	wp_enqueue_script( 'nicescroll', plugins_url( "scripts/jquery.nicescroll.js" , __FILE__ ) , false );
	wp_enqueue_style( 'lrinterface', plugins_url( "styles/application.css" , __FILE__ ));
}

add_action( 'widgets_init', 'registerWidgets' );
add_action( 'wp_enqueue_scripts', 'lr_enqueue_script' ); ?>