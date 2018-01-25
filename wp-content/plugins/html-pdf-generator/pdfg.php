<?php 
/*
Plugin Name: HTML PDF Generator
Plugin URI: https://desirepress.com/plugin/html-pdf-generator/
Description: Convert any kind of HTML Content into PDF using shortcode.
Author: DesirePress
Version: 1.0.0
Author URI: https://desirepress.com
License: GPL2
-------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

// Include PDFG Shortcode & TCPDF
require_once 'includes/tcpdf_include.php';
require_once 'pdfg-shortcode.php';

// Include CSS/JS Files 
function pdfg_enqueue_scripts() {
	if (!is_admin()) {
		wp_enqueue_script('pdfg-front-js', plugins_url('assets/js/pdfg.front.js', __FILE__),array('jquery'),'1.0', true);
		wp_enqueue_style('pdfg-front-css', plugins_url('assets/css/pdfg-front.css', __FILE__),'1.0', true);
	}
}
add_action( 'wp_enqueue_scripts', 'pdfg_enqueue_scripts' ); 