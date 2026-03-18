<?php
/**
 * Template single para Itinerario
 *
 * Muestra detalle: título, contenido, programa, galería, link externo y botón Reservar.
 * El botón Reservar abre un formulario Contact Form 7 con campo oculto del itinerario.
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();

	$post_id     = get_the_ID();
	$programa    = get_post_meta( $post_id, '_mpi_programa', true );
	$galeria_ids = get_post_meta( $post_id, '_mpi_galeria', true );
	$link_externo = get_post_meta( $post_id, '_mpi_link_externo', true );

	// Galería: IDs separados por coma a array de IDs numéricos
	$galeria_arr = array();
	if ( ! empty( $galeria_ids ) ) {
		$galeria_arr = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', $galeria_ids ) ) ) );
	}

	/**
	 * Shortcode del formulario de reserva (Contact Form 7).
	 * Cambiar el id="123" por el ID real de tu formulario en CF7.
	 * Para incluir el itinerario en el envío, crea en CF7 un campo oculto con nombre "itinerario-reserva".
	 */
	$cf7_shortcode = apply_filters( 'mpi_reserva_cf7_shortcode', '[contact-form-7 id="123" title="Reserva itinerario"]' );
	?>

	<article class="mpi-single-itinerario mpi-container">
		<header class="mpi-single-itinerario__header">
			<h1 class="mpi-single-itinerario__title"><?php echo esc_html( get_the_title() ); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="mpi-single-itinerario__thumbnail">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php endif; ?>

		<div class="mpi-single-itinerario__content entry-content">
			<?php the_content(); ?>
		</div>

		<?php if ( ! empty( $programa ) ) : ?>
			<section class="mpi-single-itinerario__programa">
				<h2><?php esc_html_e( 'Programa', 'mi-plugin-itinerarios' ); ?></h2>
				<div class="mpi-programa-text"><?php echo wp_kses_post( nl2br( esc_html( $programa ) ) ); ?></div>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $galeria_arr ) ) : ?>
			<section class="mpi-single-itinerario__galeria">
				<h2><?php esc_html_e( 'Galería', 'mi-plugin-itinerarios' ); ?></h2>
				<div class="mpi-galeria">
					<?php
					foreach ( $galeria_arr as $img_id ) {
						if ( ! $img_id ) {
							continue;
						}
						$img = wp_get_attachment_image( $img_id, 'medium' );
						if ( $img ) {
							echo '<div class="mpi-galeria__item">' . wp_kses_post( $img ) . '</div>';
						}
					}
					?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $link_externo ) ) : ?>
			<section class="mpi-single-itinerario__link">
				<a href="<?php echo esc_url( $link_externo ); ?>" class="mpi-btn mpi-btn--secondary" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Más información', 'mi-plugin-itinerarios' ); ?>
				</a>
			</section>
		<?php endif; ?>

		<section class="mpi-single-itinerario__reserva">
			<button type="button" class="mpi-btn mpi-btn--primary mpi-btn-reserva" data-itinerario-id="<?php echo esc_attr( (string) $post_id ); ?>" data-itinerario-titulo="<?php echo esc_attr( get_the_title() ); ?>">
				<?php esc_html_e( 'Reservar plaza', 'mi-plugin-itinerarios' ); ?>
			</button>
		</section>

		<!-- Modal / panel del formulario de reserva -->
		<div id="mpi-modal-reserva" class="mpi-modal" aria-hidden="true">
			<div class="mpi-modal__overlay"></div>
			<div class="mpi-modal__content">
				<button type="button" class="mpi-modal__close" aria-label="<?php esc_attr_e( 'Cerrar', 'mi-plugin-itinerarios' ); ?>">&times;</button>
				<h2 class="mpi-modal__title"><?php esc_html_e( 'Reservar plaza', 'mi-plugin-itinerarios' ); ?></h2>
				<div class="mpi-modal__form">
					<?php echo do_shortcode( $cf7_shortcode ); ?>
					<!-- En Contact Form 7 crea un campo oculto con nombre "itinerario-reserva". El JS del plugin lo rellena con el título del itinerario al abrir el modal. -->
				</div>
			</div>
		</div>
	</article>

	<?php
}

get_footer();
