<?php 
/*
	Plugin Name: Secure Password Generator
	Plugin URI: https://www.oakcreekdev.com/tools/secure-password-generator/
	Description: Adds a secure random password generator to your WordPress website. Use shortcode: [secure_pw_gen][/secure_pw_gen]
	Tags: password generator, security, special characters, strong password, secure password
	Author: Jeremy Kozan
	Author URI: https://www.oakcreekdev.com/about-us/team/jeremy-kozan/
	Contributors: @oakcreekdev
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

		$this->constants();
		$this->includes();
		
		add_action( 'init', array( $this, 'init' ) );

	}

	function constants() {

		define( 'OCDPW_VERSION',  '1.0.1'                        );
		define( 'OCDPW_DIR',      trailingslashit( __DIR__ )     );
		define( 'OCDPW_DIR_URL',  plugin_dir_url( __FILE__ )     );
		define( 'OCDPW_SETTINGS', get_option( 'ocdpw_settings' ) );

		define( 'OCDPW_CHARS_SIMILAR',   '!01iloIO'      );
		define( 'OCDPW_CHARS_AMBIGUOUS', '~(){}[]:;,.<>' );
		define( 'OCDPW_CHARS', array(
			'special' => '~!@#$%^&*()_-+={}[]:;,.<>?',
			'number'  => '012345689',
			'lower'   => 'abcdefghijklmnopqrstuvwxyz',
			'upper'   => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		) );

	}

	function includes() {

		require_once 'admin/settings.php';
		new OCD_Password_Generator_Settings();

	}

	function init() {

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 999 );

		add_shortcode( 'secure_pw_gen', array( $this, 'shortcode' ) );

	}

	function register_scripts() {

		if ( isset( OCDPW_SETTINGS['include_jquery'] ) && 'yes' === OCDPW_SETTINGS['include_jquery'] ) {

			$wp_scripts = wp_scripts();
			if ( empty( $wp_scripts->registered['jquery'] ) ) {
				wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js', array(), '3.6.3' );
			}

		}

		wp_register_script( 'ocdpw', OCDPW_DIR_URL . 'js/secure-password-generator.js', array( 'jquery' ), OCDPW_VERSION, true );

		wp_register_style( 'ocdpw', OCDPW_DIR_URL . 'css/secure-password-generator.css', array(), OCDPW_VERSION );

	}

	/*function parse_shortcode_config( $str ) {

		// TODO Remember
		// dont use quoet enclosure in shortcodeatts (keep args in content)
		// dont exploge on = and try to strip quotes.... just explode on space, then use strpose to make sure str starts with eg exclude=
		// better yet, use content area ONLY for exclude so no arg key is needed
		// put other args as normal atts

		// then use this if u want to be able to use quote chars in pw
		// https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
		
		$quote_chars = [
			'"', '&quot;',  '&#x22;',   '&#34;',
			'”', '&rdquo;', '&#x201d;', '&#8221;',
			'″', '&Prime;', '&#x2033;', '&#8243;',
		];

		$temp_r = explode( ' ', str_replace( $quote_chars, '', html_entity_decode( $str ) ) );
		$config_r = [];
		foreach ( $temp_r as $item ) {
			if ( empty( $item ) ) continue;

			$item = explode( '=', $item, 2 );
			$config_r[strtolower( $item[0] )] = $item[1];
		}

		return $config_r;

	}*/

	function shortcode( $atts = [], $content = '', $tag ) {

		// use microtime for a unique id because using a static variable or a class property to store an increment is problematic
		// certain plugins cause weird behavior (looking at you Divi)
		usleep(1);
		$instance_id = 'ocdpw_' . str_replace( array('.', ' '), '', microtime() );

		if ( isset( OCDPW_SETTINGS['include_jquery'] ) && 'yes' === OCDPW_SETTINGS['include_jquery'] ) {
			wp_enqueue_script( 'jquery' );
		}
		wp_enqueue_script( 'ocdpw' );
		wp_enqueue_style( 'ocdpw' );

		$atts = shortcode_atts(
			array(
				'width'    => 32,
				'controls' => 'true',
		), $atts, $tag );

		$chars_r = OCDPW_CHARS;
		foreach ( $chars_r as $set => $chars ) {
			$chars_r[$set] = [];
			$chars = str_split( $chars );
			foreach ( $chars as $char ) {
				if ( ! str_contains( $content, $char ) ) {
					$chars_r[$set][] = $char;
				}
			}
		}

		$data = array(
			'atts'  => $atts,
			'chars' => $chars_r,
			'msg'   => array(
				'good'    => esc_html__( 'Yes', 'ocdpw' ),
				'bad'     => esc_html__( 'No', 'ocdpw' ),
				'count'   => esc_html__( 'Characters selected:', 'ocdpw' ),
				'lower'   => esc_html__( 'Lowercase character:', 'ocdpw' ),
				'upper'   => esc_html__( 'Uppercase character:', 'ocdpw' ),
				'number'  => esc_html__( 'Number:', 'ocdpw' ),
				'special' => esc_html__( 'Special character:', 'ocdpw' ),
			),
		);

		$output = '<div class="ocdpw" data-instance="' . $instance_id . '" style="display: none;">';
			$output .= '<div class="ocdpw-random"></div>';
			$output .= '<div class="ocdpw-feedback"></div>';
		$output .= '</div>';
		$output .= '<noscript>' . esc_html__( 'Your browser does not support JavaScript! This password generator requires jQuery.', 'ocdpw' ) . '</noscript>';
		$output .= '<script>var ' . $instance_id . ' = ' . json_encode( $data ) . '</script>';
		
		return $output;

	}
	
}
new OCD_Password_Generator();
endif;
