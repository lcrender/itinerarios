<?php
/**
 * Template de archivo para Itinerarios
 *
 * Se carga cuando se visita la URL del listado (archive) de itinerarios.
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="mpi-archive mpi-container">
	<header class="mpi-archive__header">
		<h1 class="mpi-archive__title"><?php esc_html_e( 'Itinerarios', 'mi-plugin-itinerarios' ); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="mpi-itinerarios-list">
			<?php
			while ( have_posts() ) {
				the_post();
				$permalink = get_permalink();
				$post_id    = get_the_ID();
				$precio_desde = get_post_meta( $post_id, MPI_META_PRECIO_DESDE, true );
				$subtitulo    = get_post_meta( $post_id, MPI_META_SUBTITULO, true );

				$dias_meta = get_post_meta( $post_id, MPI_META_DIAS, true );
				if ( is_string( $dias_meta ) ) {
					$decoded = json_decode( $dias_meta, true );
					if ( is_array( $decoded ) ) {
						$dias_meta = $decoded;
					}
				}
				$dias_count = is_array( $dias_meta ) ? count( $dias_meta ) : 0;
				?>
				<article class="mpi-itinerario-card">
					<div class="mpi-itinerario-card__image-wrap">
						<?php if ( ! empty( $precio_desde ) ) : ?>
							<div class="mpi-itinerario-card__price">
								<?php echo esc_html__( 'Desde', 'mi-plugin-itinerarios' ) . ' ' . esc_html( $precio_desde ); ?>
							</div>
						<?php endif; ?>

						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php echo esc_url( $permalink ); ?>" class="mpi-itinerario-card__image">
								<?php the_post_thumbnail( 'medium_large' ); ?>
							</a>
						<?php endif; ?>
					</div>
					<div class="mpi-itinerario-card__content">
						<h2 class="mpi-itinerario-card__title">
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						</h2>

						<?php if ( $dias_count > 0 ) : ?>
							<div class="mpi-itinerario-card__days">
								<?php
									echo esc_html( sprintf( _n( '%d día', '%d días', $dias_count, 'mi-plugin-itinerarios' ), $dias_count ) );
								?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $subtitulo ) ) : ?>
							<div class="mpi-itinerario-card__subtitle">
								<?php echo esc_html( $subtitulo ); ?>
							</div>
						<?php endif; ?>

						<a href="<?php echo esc_url( $permalink ); ?>" class="mpi-itinerario-card__btn mpi-btn mpi-btn--primary">
							<?php esc_html_e( 'Ver detalle', 'mi-plugin-itinerarios' ); ?>
						</a>
					</div>
				</article>
				<?php
			}
			?>
		</div>
		<?php
		the_posts_pagination();
	else :
		?>
		<p><?php esc_html_e( 'No hay itinerarios publicados.', 'mi-plugin-itinerarios' ); ?></p>
	<?php endif; ?>
</main>

<?php
get_footer();
