<?php
/**
 * Template single para Itinerario
 *
 * Muestra detalle: imagen principal, nombre, precio, descripción general, itinerario por días, extras e información de reserva.
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

		$post_id               = get_the_ID();
		$descripcion_general  = get_post_meta( $post_id, MPI_META_DESCRIPCION_GENERAL, true );
		$extras_incluidos     = get_post_meta( $post_id, MPI_META_EXTRAS_INCLUIDOS, true );
		$dias                  = get_post_meta( $post_id, MPI_META_DIAS, true );

		// Normaliza por si se guardó como JSON en el pasado.
		if ( is_string( $dias ) ) {
			$decoded = json_decode( $dias, true );
			if ( is_array( $decoded ) ) {
				$dias = $decoded;
			}
		}

		if ( ! is_array( $dias ) ) {
			$dias = array();
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
			<h1 class="mpi-single-itinerario__title">
				<span class="mpi-single-itinerario__itinerary-prefix"><?php echo esc_html__( 'Itinerary', 'mi-plugin-itinerarios' ); ?></span>
				<span class="mpi-single-itinerario__itinerario-name"><?php echo esc_html( get_the_title() ); ?></span>
			</h1>
		</header>

		<div class="mpi-single-itinerario__content entry-content">
			<?php if ( ! empty( $descripcion_general ) ) : ?>
				<section class="mpi-single-itinerario__descripcion-general">
					<div class="mpi-single-itinerario__descripcion-general-text">
						<?php echo nl2br( esc_html( $descripcion_general ) ); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $dias ) ) : ?>
				<section class="mpi-single-itinerario__dias">
					<div class="mpi-timeline">
						<?php foreach ( $dias as $i => $dia ) : ?>
							<?php
								$titulo_dia = isset( $dia['titulo'] ) ? (string) $dia['titulo'] : '';
								$desc_dia   = isset( $dia['descripcion'] ) ? (string) $dia['descripcion'] : '';
								$img_id     = isset( $dia['imagen_id'] ) ? absint( $dia['imagen_id'] ) : 0;
								$numero_dia = $i + 1;
								$day_label  = sprintf( 'Day %d', $numero_dia );
								$card_title = '' !== trim( $titulo_dia ) ? $titulo_dia : $day_label;
							?>
							<div class="mpi-timeline__item">
								<div class="mpi-timeline__marker-col">
									<div class="mpi-timeline__marker"><?php echo esc_html( $day_label ); ?></div>
								</div>

								<div class="mpi-timeline__card">
									<div class="mpi-timeline__card-inner">
										<div class="mpi-timeline__card-text">
											<h3 class="mpi-timeline__card-title"><?php echo esc_html( $card_title ); ?></h3>

											<?php if ( ! empty( $desc_dia ) ) : ?>
												<div class="mpi-timeline__card-desc">
													<?php echo nl2br( esc_html( $desc_dia ) ); ?>
												</div>
											<?php endif; ?>
										</div>

										<?php if ( $img_id ) : ?>
											<div class="mpi-timeline__card-image">
												<?php echo wp_get_attachment_image( $img_id, 'medium_large', false, array( 'loading' => 'lazy' ) ); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $extras_incluidos ) ) : ?>
				<?php
					$extras_lines = array_filter( array_map( 'trim', preg_split( '/\R/', (string) $extras_incluidos ) ) );
					$extras_lines = array_values( $extras_lines );
				?>
				<section class="mpi-single-itinerario__extras">
					<h2><?php esc_html_e( 'Included extras', 'mi-plugin-itinerarios' ); ?></h2>

					<?php if ( count( $extras_lines ) > 1 ) : ?>
						<ul class="mpi-extras-list">
							<?php foreach ( $extras_lines as $extra ) : ?>
								<li><?php echo esc_html( $extra ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<div class="mpi-single-itinerario__extras-text">
							<?php echo nl2br( esc_html( $extras_incluidos ) ); ?>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>
		</div>

		<section class="mpi-single-itinerario__reserva">
			<button type="button" class="mpi-btn mpi-btn--primary mpi-btn-reserva" data-itinerario-id="<?php echo esc_attr( (string) $post_id ); ?>" data-itinerario-titulo="<?php echo esc_attr( get_the_title() ); ?>">
				<?php esc_html_e( 'Reserve', 'mi-plugin-itinerarios' ); ?>
			</button>
		</section>

		<!-- Modal / panel del formulario de reserva -->
		<div id="mpi-modal-reserva" class="mpi-modal" aria-hidden="true">
			<div class="mpi-modal__overlay"></div>
			<div class="mpi-modal__content">
				<button type="button" class="mpi-modal__close" aria-label="<?php esc_attr_e( 'Cerrar', 'mi-plugin-itinerarios' ); ?>">&times;</button>
				<h2 class="mpi-modal__title"><?php esc_html_e( 'Reserve', 'mi-plugin-itinerarios' ); ?></h2>
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
