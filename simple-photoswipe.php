<?php
/*
Plugin Name: Simple Photoswipe
Plugin URI: https://github.com/zitscher/simple-photoswipe
Description: A simple plugin for galleries using PhotoSwipe from Dmitry Semenov.
Author: Tobias Cichon
Author URI:
Version: 0.1
License: MIT
*/

defined('ABSPATH') or die("No script kiddies please!");

function enqueue_scripts() {
	$plugin_path =  plugins_url() . '/simple-photoswipe' ;

	wp_enqueue_style(  'photoswipe-core-css',		$plugin_path . '/lib/photoswipe.css');
	wp_enqueue_style(  'photoswipe-default-skin',	$plugin_path . '/lib/default-skin/default-skin.css');
	wp_enqueue_script( 'photoswipe', 				$plugin_path . '/lib/photoswipe.min.js');
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');

// append photoswipe template to DOM
include_once('photoswipe-template.php');

// change wp gallery output to match with photoswipe
include_once('custom_wp_gallery_output.php');

// show admin photoswipe options
include_once('admin-options.php');

// inject photoswipe default ui
include_once('photoswipe-ui.php');

// initialize photoswipe
include_once('photoswipe-init.php');