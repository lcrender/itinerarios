<?php
/**
 * Página de configuración del plugin Itinerarios
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MPI_OPTION_CF7_SHORTCODE', 'mpi_cf7_shortcode' );

/**
 * Sanitiza el shortcode CF7 guardado (no usar sanitize_text_field: puede vaciar o romper el valor).
 *
 * @param string $value Valor enviado.
 * @return string
 */
function mpi_sanitize_cf7_shortcode_option( $value ) {
	if ( ! is_string( $value ) ) {
		return '';
	}
	$value = str_replace( "\0", '', $value );
	$value = wp_check_invalid_utf8( $value, true );
	return trim( $value );
}

/**
 * URL absoluta de la página de ajustes del plugin (evita POST con action vacío).
 *
 * @return string
 */
function mpi_settings_page_url() {
	return admin_url( 'edit.php?post_type=itinerario&page=itinerarios-config' );
}

/**
 * Añade el submenú de configuración bajo Itinerarios
 */
function mpi_add_settings_menu() {
	add_submenu_page(
		'edit.php?post_type=itinerario',
		__( 'Configuración', 'mi-plugin-itinerarios' ),
		__( 'Configuración', 'mi-plugin-itinerarios' ),
		'manage_options',
		'itinerarios-config',
		'mpi_render_settings_page'
	);
}
add_action( 'admin_menu', 'mpi_add_settings_menu' );

/**
 * Renderiza la página de configuración
 */
function mpi_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$saved      = false;
	$save_error = '';

	if (
		isset( $_SERVER['REQUEST_METHOD'] )
		&& 'POST' === $_SERVER['REQUEST_METHOD']
		&& isset( $_POST['mpi_cf7_shortcode_submit'] )
	) {
		if ( ! isset( $_POST['mpi_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mpi_settings_nonce'] ) ), 'mpi_save_settings' ) ) {
			$save_error = __( 'La verificación de seguridad falló. Recarga la página e inténtalo de nuevo.', 'mi-plugin-itinerarios' );
		} else {
			$raw       = isset( $_POST['mpi_cf7_shortcode'] ) ? wp_unslash( $_POST['mpi_cf7_shortcode'] ) : '';
			$shortcode = mpi_sanitize_cf7_shortcode_option( $raw );
			// update_option puede devolver false si el valor no cambió; igual mostramos éxito.
			update_option( MPI_OPTION_CF7_SHORTCODE, $shortcode, false );
			$saved = true;
		}
	}

	$cf7_shortcode = get_option( MPI_OPTION_CF7_SHORTCODE, '[contact-form-7 id="123" title="Itinerary booking"]' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php esc_html_e( 'Configura aquí las opciones del plugin Itinerarios.', 'mi-plugin-itinerarios' ); ?></p>

		<?php if ( $saved && '' === $save_error ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Configuración guardada.', 'mi-plugin-itinerarios' ); ?></p></div>
		<?php endif; ?>

		<?php if ( '' !== $save_error ) : ?>
			<div class="notice notice-error is-dismissible"><p><?php echo esc_html( $save_error ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( mpi_settings_page_url() ); ?>" novalidate>
			<?php wp_nonce_field( 'mpi_save_settings', 'mpi_settings_nonce' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="mpi_cf7_shortcode"><?php esc_html_e( 'Shortcode de reserva (Contact Form 7)', 'mi-plugin-itinerarios' ); ?></label>
					</th>
					<td>
						<input type="text" name="mpi_cf7_shortcode" id="mpi_cf7_shortcode" value="<?php echo esc_attr( $cf7_shortcode ); ?>" class="large-text" placeholder='[contact-form-7 id="123" title="Itinerary booking"]'>
						<p class="description">
							<?php esc_html_e( 'Pega el shortcode de tu formulario de Contact Form 7. Cambia el id por el ID real de tu formulario (en Contacto → Formularios).', 'mi-plugin-itinerarios' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<button type="submit" name="mpi_cf7_shortcode_submit" value="1" class="button button-primary"><?php esc_html_e( 'Guardar cambios', 'mi-plugin-itinerarios' ); ?></button>
			</p>
		</form>

		<hr>
		<h2><?php esc_html_e( 'Uso del shortcode', 'mi-plugin-itinerarios' ); ?></h2>
		<p><?php esc_html_e( 'Para mostrar el listado de itinerarios en una página, inserta:', 'mi-plugin-itinerarios' ); ?></p>
		<code>[itinerarios_list]</code>
		<p class="description">
			<?php esc_html_e( 'Atributos opcionales: posts_per_page="6" orderby="date" order="DESC"', 'mi-plugin-itinerarios' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Para que el correo de reserva incluya el itinerario, en Contact Form 7 añade un campo oculto con nombre:', 'mi-plugin-itinerarios' ); ?>
			<code>itinerario-reserva</code>
		</p>
	</div>
	<?php
}

/**
 * Devuelve el shortcode CF7 guardado (para el filtro mpi_reserva_cf7_shortcode)
 *
 * @param string $shortcode Valor por defecto.
 * @return string
 */
function mpi_default_cf7_shortcode( $shortcode ) {
	$saved = get_option( MPI_OPTION_CF7_SHORTCODE, '' );
	if ( $saved !== '' ) {
		return $saved;
	}
	return $shortcode;
}
add_filter( 'mpi_reserva_cf7_shortcode', 'mpi_default_cf7_shortcode', 5 );
