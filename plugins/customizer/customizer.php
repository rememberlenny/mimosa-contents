<?php
/**
 * @package Customizer
 * @version 0.5
 */
/*
Plugin Name: Customizer
Plugin URI: http://wp-customizer.com/
Description: Customizer extends functionality of Customize feature introduced in WordPress 3.4. Allows adding and editing Theme Customization Sections directly from the Customize Panel.
Author: Sławomir Amielucha
Version: 0.6
Author URI: http://amielucha.com/
*/

// Add settings link on plugins page of admin panel
function customizer_link($links, $file) {
	static $this_plugin;

  if (!$this_plugin) { $this_plugin = plugin_basename(__FILE__); }
	if ($file == $this_plugin) { $settings_link = '<a href="options-general.php?page=customizer_options_panel">Settings</a>';
  	array_unshift($links, $settings_link);
	}
	return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links", 'customizer_link', 10, 2 );

//Get Plugin Settings
require 'customizer_options.php';

add_action( 'customize_controls_enqueue_scripts', 'customizer_init' );
//available hooks: customize_register, customize_controls_init, customize_controls_enqueue_scripts, wp_ajax_customize_save?

function customizer_init() {

	//get Customizer options
	$options = get_option('customizer_options');

	//include customizer.css and customizer.js
	if ( $options['disable_customizer'] != 'on' ) {
		wp_enqueue_style( 'customizer_css', plugins_url() . '/customizer/customizer.css' );
		wp_enqueue_script( 'jquery_cookie', plugins_url() . '/customizer/jquery.cookie.js', array( 'jquery' ), '20120520', true );
		wp_enqueue_script( 'jquery_validate', plugins_url() . '/customizer/jquery.validate.js', array( 'jquery' ), '20120531', true );
		wp_enqueue_script( 'suggest');

		wp_enqueue_script( 'customizer_js', plugins_url() . '/customizer/customizer.js', array( 'jquery' ), '20120520', true );
	}
}

// + add new section
add_action ('customize_register','customizer_section_add');

function customizer_section_add($wp_customize) {
	$options = get_option( "customizer_array" );
	if($options){
		foreach ($options as $o) {

				//If option is serialized
				if ( $o["prefix1"] && $o["prefix1"] !='undefined' ) {
					$option_name = $o["prefix1"].'['.$o["id1"].']';
				} else {
					$option_name = $o["id1"];
				}

				if ( $o["action"] == 's' && $o["id"] && $o["title"] && $o["id"] !='undefined' && $o["title"] !='undefined' ) {									// it means that you create a new section ( or modify existing )
					$wp_customize->add_section( $o["id"], array(
						'title'          => $o["title"],
						'priority'       => $o["priority"],
						'description'       => $o["desc"],
					) );
				} else if ( $o["action"] == 'c' ):

				/*$wp_customize->add_setting(  $o["id"].$o["id1"], array(
					// @todo: replace with a new accept() setting method
					// 'sanitize_callback' => 'sanitize_hexcolor',
					'theme_supports' => array( 'custom-header', 'header-text' ),
					'type'           => 'option',
					'default'        => 'off',
					'capability'     => 'edit_theme_options',
				) );*/

				/* Add Controls of the selected type */
				/* CHECKBOX */
				if (get_option( $option_name )) {
					$default = get_option( $option_name );
				} else $default = '';

				if ( $o["type1"] == 'checkbox' ) {

					$wp_customize->add_setting(  $option_name, array(
						'type'           => 'option',
						/*'default'        => '',*/
						'capability'     => 'edit_theme_options',
					) );

					$wp_customize->add_control( $o["id1"], array(
							'settings' => $option_name,
							'label'    => $o["label1"],
							'section'  => $o["id"],
							'type'     => 'checkbox',
						) );

				/* TEXT */
				} elseif ( $o["type1"] == 'text' ) {

					$wp_customize->add_setting(  $option_name, array(
						'type'           => 'option',
						'capability'     => 'edit_theme_options',
					) );

					$wp_customize->add_control( $o["id1"], array(
							'settings' => $option_name,
							'label'    => $o["label1"],
							'section'  => $o["id"],
							'type'     => 'text',
						) );
				/* RADIO and SELECT */
				} elseif ( $o["type1"] == 'radio' || $o["type1"] == 'select' ) {

					$typevals = explode(",", $o["typeval1"]);
					$typevals_processed = array();
					foreach ( $typevals as $typeval ) {
						//Process the lengthy string:
						if (strpos($typeval, ':') !== false) {
							$typeval   = explode(":",$typeval);
							$typeval_l = trim($typeval[0]);
							$typeval_r = trim($typeval[1]);
							$typevals_processed[$typeval_l] = $typeval_r;
						} else {
							$typeval = trim($typeval);
							$typevals_processed[$typeval] = $typeval;
						}
					}

					$wp_customize->add_setting(  $option_name, array(
						'type'           => 'option',
						'capability'     => 'edit_theme_options',
					) );

					$wp_customize->add_control( $o["id1"], array(
							'settings' => $option_name,
							'label'    => $o["label1"],
							'section'  => $o["id"],
							'type'     => $o["type1"],
							'choices'    => $typevals_processed,
						) );


				/* IMAGE */
				} elseif ( $o["type1"] == 'image' ){
					$wp_customize->add_setting(  $option_name, array(
						'type'           => 'option',
						'capability'     => 'edit_theme_options',
					) );

					$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $o["id1"], array(
						'label'   => $o["label1"],
						'section' => $o["id"],
						'settings'   => $option_name,
					) ) );

				/* COLOR PICKER */
				} elseif ( $o["type1"] == 'color' ){
					$wp_customize->add_setting(  $option_name, array(
						'type'           => 'option',
						'default'        => '#BADA55',
						'capability'     => 'edit_theme_options',
					) );

					$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $o["id1"], array(
						'label'   => $o["label1"],
						'section' => $o["id"],
						'settings'   => $option_name,
					) ) );

				}
				endif;
		}
	}
}

function customizer_ajax_link() {
	$value = plugins_url()."/customizer/customizer_ajax.php";

	// send a cookie
	setcookie("customizerCookie",$value, time()+3600*24);
}
add_action( 'customize_controls_enqueue_scripts', 'customizer_ajax_link' );