<?php
/**
 * Metaboxes nativos para Itinerario (sin ACF)
 *
 * Campos: programa, galeria, link_externo
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MPI_META_PROGRAMA', '_mpi_programa' );
define( 'MPI_META_GALERIA', '_mpi_galeria' );
define( 'MPI_META_LINK_EXTERNO', '_mpi_link_externo' );

/**
 * Añade los metaboxes al CPT itinerario
 */
function mpi_add_metaboxes() {
	add_meta_box(
		'mpi_itinerario_datos',
		__( 'Datos del itinerario', 'mi-plugin-itinerarios' ),
		'mpi_render_metaboxes',
		'itinerario',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'mpi_add_metaboxes' );

/**
 * Renderiza el contenido de los metaboxes
 *
 * @param WP_Post $post Objeto del post actual.
 */
function mpi_render_metaboxes( $post ) {
	wp_nonce_field( 'mpi_save_metaboxes', 'mpi_metabox_nonce' );

	$programa     = get_post_meta( $post->ID, MPI_META_PROGRAMA, true );
	$galeria      = get_post_meta( $post->ID, MPI_META_GALERIA, true );
	$link_externo = get_post_meta( $post->ID, MPI_META_LINK_EXTERNO, true );
	?>
	<p>
		<label for="mpi_programa"><strong><?php esc_html_e( 'Programa', 'mi-plugin-itinerarios' ); ?></strong></label><br>
		<textarea id="mpi_programa" name="mpi_programa" rows="6" class="large-text"><?php echo esc_textarea( $programa ); ?></textarea>
	</p>
	<p>
		<label for="mpi_galeria"><strong><?php esc_html_e( 'Galería (IDs de imágenes separadas por coma)', 'mi-plugin-itinerarios' ); ?></strong></label><br>
		<input type="text" id="mpi_galeria" name="mpi_galeria" value="<?php echo esc_attr( $galeria ); ?>" class="large-text">
	</p>
	<p>
		<label for="mpi_link_externo"><strong><?php esc_html_e( 'Link externo', 'mi-plugin-itinerarios' ); ?></strong></label><br>
		<input type="url" id="mpi_link_externo" name="mpi_link_externo" value="<?php echo esc_url( $link_externo ); ?>" class="large-text">
	</p>
	<?php
}

/**
 * Guarda los valores de los metaboxes
 *
 * @param int $post_id ID del post.
 */
function mpi_save_metaboxes( $post_id ) {
	if ( ! isset( $_POST['mpi_metabox_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mpi_metabox_nonce'] ) ), 'mpi_save_metaboxes' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( get_post_type( $post_id ) !== 'itinerario' ) {
		return;
	}

	if ( isset( $_POST['mpi_programa'] ) ) {
		update_post_meta( $post_id, MPI_META_PROGRAMA, sanitize_textarea_field( wp_unslash( $_POST['mpi_programa'] ) ) );
	}
	if ( isset( $_POST['mpi_galeria'] ) ) {
		update_post_meta( $post_id, MPI_META_GALERIA, sanitize_text_field( wp_unslash( $_POST['mpi_galeria'] ) ) );
	}
	if ( isset( $_POST['mpi_link_externo'] ) ) {
		$url = esc_url_raw( wp_unslash( $_POST['mpi_link_externo'] ) );
		update_post_meta( $post_id, MPI_META_LINK_EXTERNO, $url );
	}
}
add_action( 'save_post_itinerario', 'mpi_save_metaboxes' );
