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
				?>
				<article class="mpi-itinerario-card">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php echo esc_url( $permalink ); ?>" class="mpi-itinerario-card__image">
							<?php the_post_thumbnail( 'medium_large' ); ?>
						</a>
					<?php endif; ?>
					<div class="mpi-itinerario-card__content">
						<h2 class="mpi-itinerario-card__title">
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						</h2>
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
