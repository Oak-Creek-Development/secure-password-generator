<?php

if ( ! defined( 'ABSPATH' ) ) die();

if ( ! class_exists( 'OCD_Password_Generator_Settings' ) ) :
class OCD_Password_Generator_Settings {

	function __construct() {

		add_action( 'admin_menu',            array( $this, 'admin_menu'             )        );
		add_action( 'admin_init',            array( $this, 'settings_init'          )        );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_register_scripts' ), 10, 1 );

		add_filter( 'plugin_action_links_secure-password-generator/secure-password-generator.php', [ $this, 'settings_link' ] );

	}

	function admin_register_scripts( $hook ) {

		if ( 'settings_page_ocdpw_settings' !== $hook ) return;

		wp_enqueue_script( 'ocdpw', OCDPW_DIR_URL . 'admin/js/secure-password-generator.js',   array( 'jquery' ), OCDPW_VERSION, true );
		wp_enqueue_style(  'ocdpw', OCDPW_DIR_URL . 'admin/css/secure-password-generator.css', array(          ), OCDPW_VERSION       );

	}

	function settings_link( $links ) {

		$url = add_query_arg( 'page', 'ocdpw_settings', get_admin_url( null, 'options-general.php' ) );

		$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'ocdpw' ) . '</a>';

		return $links;

	}

	function admin_menu() {

		add_options_page(
			esc_html__( 'Secure Password Generator Settings', 'ocdpw' ),
			esc_html__( 'Password Generator', 'ocdpw' ),
			'manage_options',
			'ocdpw_settings',
			array( $this, 'settings_page' )
		);

	}

	function settings_page() {

		if ( ! current_user_can( 'manage_options' ) ) return;

		?>
		<div class="wrap ocdpw">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				
			<form action="options.php" method="post">
				<?php
					settings_fields( 'ocdpw_settings' );
					do_settings_sections( 'ocdpw_settings' );
					submit_button( esc_html__( 'Save Settings', 'ocdpw' ) );
				?>
			</form>

			<h2 class="shortcode"><?php esc_html_e( 'Shortcode Settings', 'ocdpw' ); ?></h2>
			<input type="text" id="shortcode" value="" onClick="this.select();" readonly />
			<button class="button button-secondary" id="copy"><?php esc_html_e( 'Copy Shortcode', 'ocdpw' ); ?></button>
			<span id="copy-success" style="display: none;"><?php esc_html_e( 'Shortcode copied to clipboard.', 'ocdpw' ); ?></span>
			<span id="copy-fail" style="display: none;"><?php esc_html_e( 'Shortcode not copied to clipboard. Please copy it manually.', 'ocdpw' ); ?></span>
			<p><?php esc_html_e( 'Use the controls below to create a shortcode with your desired options.', 'ocdpw' ); ?></p>
			<table class="form-table" role="presentation"><tbody>
				<tr>
					<th><?php esc_html_e( 'Options', 'ocdpw' ); ?></th>
					<td><fieldset>
						<p><strong><?php esc_html_e( 'Rows', 'ocdpw' ); ?></strong></p>
						<input type="number" class="option" id="rows" value="6" min="1" />
						<p class="description"><?php esc_html_e( 'Note: Will be double on mobile layout.', 'ocdpw' ); ?></p>
						<!-- <p><strong><?php esc_html_e( 'Test', 'ocdpw' ); ?></strong></p>
						<input type="text" class="option" id="test" /> -->
						<p><strong><?php esc_html_e( 'Show Controls', 'ocdpw' ); ?></strong></p>
						<select class="option" id="controls">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</fieldset></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Exclusions', 'ocdpw' ); ?></th>
					<td><fieldset>
						<p><strong><?php esc_html_e( 'Character Groups', 'ocdpw' ); ?></strong></p>
						<div>
							<label for="similar">
								<input type="checkbox" class="exclude group" id="similar" value="<?php echo esc_attr( OCDPW_CHARS_SIMILAR ); ?>" />
								<?php esc_html_e( 'Exclude Similar Characters:', 'ocdpw' ); ?> &quot;<?php echo OCDPW_CHARS_SIMILAR; ?>&quot;
							</label>
							<br />
							<label for="ambiguous">
								<input type="checkbox" class="exclude group" id="ambiguous" value="<?php echo esc_attr( OCDPW_CHARS_AMBIGUOUS ); ?>" />
								<?php esc_html_e( 'Exclude Ambiguous Characters:', 'ocdpw' ); ?> &quot;<?php echo OCDPW_CHARS_AMBIGUOUS; ?>&quot;
							</label>
						</div>
						<p><strong><?php esc_html_e( 'Exclude Individual Characters', 'ocdpw' ); ?></strong></p>
						<?php foreach ( OCDPW_CHARS as $set => $chars ) : ?>
							<?php $chars = str_split( $chars ); ?>
							<div class="set">
								<?php foreach ( $chars as $char ) : $char = esc_attr( $char ); ?>
									<label for="exclude_<?php echo $char; ?>">
										<input type="checkbox" class="exclude individual" id="exclude_<?php echo $char; ?>" value="<?php echo $char; ?>" />
										<span><?php echo $char; ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</fieldset></td>
				</tr>
			</tbody></table>
		</div>
		<?php

	}

	function settings_init() {

		register_setting( 'ocdpw_settings', 'ocdpw_settings' );

		add_settings_section(
			'global_settings',
			esc_html__( 'Global Settings', 'ocdpw' ),
			function(){ echo '<p>' . esc_html__( 'These settings affect every instance throughout the entire site, regardless of individual shortcode options.', 'ocdpw' ) . '</p>'; },
			'ocdpw_settings'
		);

		add_settings_field(
			'include_jquery',
			esc_html__( 'Include jQuery', 'ocdpw' ),
			array( $this, 'input_select' ),
			'ocdpw_settings',
			'global_settings',
			array(
				'name'    => 'include_jquery',
				'desc'    => esc_html__( 'Force loading of jQuery if it is not included in your theme.', 'ocdpw' ),
				'options' => array(
					'no'  => esc_html__( 'No', 'ocdpw' ),
					'yes' => esc_html__( 'Yes', 'ocdpw' ),
				),
			)
		);

	}

	function get_val( $args ) {

		$val = get_option( 'ocdpw_settings' );
		if ( empty( $val[ $args['name'] ] ) ) {
			$val = '';
		} else {
			$val = $val[ $args['name'] ];
		}

		return $val;

	}

	function get_atts( $args ) {

		$atts  = '';
		$atts .= ' id="' . esc_attr( $args['name'] ) . '"';
		$atts .= ' name="' . esc_attr( 'ocdpw_settings[' . $args['name'] . ']' ) .'"';
		$atts .= empty( $args['class'] )    ? '' : ' class="' . esc_attr( $args['class'] ) . '"';
		$atts .= empty( $args['required'] ) ? '' : ' required="required"';
		$atts .= empty( $args['min'] )      ? '' : ' min="' . esc_attr( $args['min'] ) . '"';
		$atts .= empty( $args['max'] )      ? '' : ' max="' . esc_attr( $args['max'] ) . '"';
		$atts .= empty( $args['step'] )     ? '' : ' step="' . esc_attr( $args['step'] ) . '"';

		return $atts;

	}

	function input_text( $args ) {

		$val = $this->get_val( $args );
		if ( empty( $val ) && ! empty( $args['default'] ) ) $val = $args['default'];

		$atts = $this->get_atts( $args );
		$atts .= empty( $val ) ? '' : ' value="' . esc_attr( $val ) . '"';

		echo '<input type="text"' . $atts . '>';
		if ( ! empty( $args['desc'] ) ) echo '<p class="description">' . $args['desc'] . '</p>';

	}

	function input_number( $args ) {

		$val = $this->get_val( $args );
		if ( empty( $val ) && ! empty( $args['default'] ) ) $val = $args['default'];

		$atts = $this->get_atts( $args );
		$atts .= empty( $val ) ? '' : ' value="' . esc_attr( $val ) . '"';

		echo '<input type="number"' . $atts . '>';
		if ( ! empty( $args['desc'] ) ) echo '<p class="description">' . $args['desc'] . '</p>';

	}

	function input_select( $args ) {

		$val = $this->get_val( $args );
		if ( empty( $val ) && isset( $args['default'] ) && in_array( $args['default'], array_keys( $args['options'] ) ) ) $val = $args['default'];

		$atts = $this->get_atts( $args );

		echo '<select' . $atts . '>';
		foreach ( $args['options'] as $k => $v ) {
			echo '<option value="' . $k . '" ' . selected( $k, $val, false ) . '>' . $v . '</option>';
		}
		echo '</select>';
		if ( ! empty( $args['desc'] ) ) echo '<p class="description">' . $args['desc'] . '</p>';

	}

}
endif;

?>
