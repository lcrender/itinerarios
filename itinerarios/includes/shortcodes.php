<?php
/**
 * Shortcodes del plugin
 *
 * [itinerarios_list] - Listado de itinerarios
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode [itinerarios_list]
 *
 * Atributos opcionales:
 * - posts_per_page (int, default 10)
 * - orderby (string, default date)
 * - order (ASC/DESC, default DESC)
 *
 * @param array $atts Atributos del shortcode.
 * @return string HTML del listado.
 */
function mpi_shortcode_itinerarios_list( $atts ) {
	$atts = shortcode_atts(
		array(
			'posts_per_page' => 10,
			'orderby'        => 'date',
			'order'          => 'DESC',
		),
		$atts,
		'itinerarios_list'
	);

	$query = new WP_Query(
		array(
			'post_type'      => 'itinerario',
			'post_status'    => 'publish',
			'posts_per_page' => absint( $atts['posts_per_page'] ),
			'orderby'        => sanitize_key( $atts['orderby'] ),
			'order'          => strtoupper( $atts['order'] ) === 'ASC' ? 'ASC' : 'DESC',
		)
	);

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No hay itinerarios publicados.', 'mi-plugin-itinerarios' ) . '</p>';
	}

	// Cargar estilos solo cuando se usa el shortcode.
	wp_enqueue_style(
		'mpi-style',
		MPI_PLUGIN_URL . 'assets/css/style.css',
		array(),
		MPI_VERSION
	);

	ob_start();
	?>
	<div class="mpi-itinerarios-list">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			$permalink = get_permalink();
			?>
			<article class="mpi-itinerario-card">
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php echo esc_url( $permalink ); ?>" class="mpi-itinerario-card__image">
						<?php the_post_thumbnail( 'medium_large' ); ?>
					</a>
				<?php endif; ?>
				<div class="mpi-itinerario-card__content">
					<h3 class="mpi-itinerario-card__title">
						<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
					</h3>
					<div class="mpi-itinerario-card__excerpt">
						<?php the_excerpt(); ?>
					</div>
					<a href="<?php echo esc_url( $permalink ); ?>" class="mpi-itinerario-card__btn mpi-btn mpi-btn--primary">
						<?php esc_html_e( 'Ver más', 'mi-plugin-itinerarios' ); ?>
					</a>
				</div>
			</article>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'itinerarios_list', 'mpi_shortcode_itinerarios_list' );
