<?php 
/*
	Plugin Name: Secure Password Generator
	Plugin URI: https://www.oakcreekdev.com/tools/secure-password-generator/
	Description: Adds a secure password generator to your WordPress website. Use shortcode: [secure_pw_gen][/secure_pw_gen]
	Tags: password generator, security, special characters, strong password, secure password
	Author: Jeremy Kozan
	Author URI: https://www.oakcreekdev.com/about-us/team/jeremy-kozan/
	Requires at least: 5.1
	Tested up to: 6.1.1
	Stable tag: 1.0.1
	Version: 1.0.1
	Requires PHP: 7.1
	Text Domain: ocdpw
	Domain Path: /languages
	License: GPL v2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/
	
	Copyright 2023 Oak Creek Development. All rights reserved.
*/

if ( ! defined( 'ABSPATH' ) ) die();

if ( ! class_exists( 'OCD_Password_Generator' ) ) :
class OCD_Password_Generator {
	
	function __construct() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	function register_shortcodes() {
		add_shortcode( 'secure_pw_gen', array( $this, 'shortcode' ) );
	}

	function register_scripts() {

		//TODO: detect if jquery has been de-registered and re-register it, based on settings

		wp_register_script( 'ocdpw', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'secure-password-generator.js', array( 'jquery' ), null, true );
		wp_register_style( 'ocdpw', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'secure-password-generator.css', array(), null );
	}

	function shortcode() {
		wp_enqueue_script( 'ocdpw' );
		wp_enqueue_style( 'ocdpw' );

		$output = '<div class="ocdpw" style="display: none;">';
			$output .= '<p class="ocdpw-count">' . __( 'Characters selected', 'ocdpw' ) . ': <span class="ocdpw-bad">0</span></p>';
			$output .= '<p class="ocdpw-number">' . __( 'Number selected', 'ocdpw' ) . ': </p>';
			$output .= '<p class="ocdpw-special">' . __( 'Special character selected', 'ocdpw' ) . ': </p>';
			$output .= '<p class="ocdpw-lower">' . __( 'Lowercase character selected', 'ocdpw' ) . ': </p>';
			$output .= '<p class="ocdpw-upper">' . __( 'Uppercase character selected', 'ocdpw' ) . ': </p>';
			$output .= '<span class="ocdpw-good">' . __( 'Yes', 'ocdpw' ) . '</span>';
			$output .= '<span class="ocdpw-bad">' . __( 'No', 'ocdpw' ) . '</span>';
			$output .= '<p class="ocdpw-dupe">' . __( 'This shortcode has already been placed on this page. You can only use it once per page.', 'ocdpw' ) . '</p>';
		$output .= '</div>';

		return $output;
	}
	
}
$OCD_Password_Generator = new OCD_Password_Generator();
endif;
