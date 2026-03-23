<?php
/**
 * Carga de templates del plugin vía template_include
 *
 * Sobrescribe archive y single de itinerario con templates del plugin.
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filtra template_include para usar templates del plugin en itinerario
 *
 * @param string $template Ruta del template que WordPress va a cargar.
 * @return string Ruta del template (plugin o original).
 */
function mpi_template_include( $template ) {
	if ( is_singular( 'itinerario' ) ) {
		$plugin_template = MPI_PLUGIN_DIR . 'templates/single-itinerario.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}

	if ( is_post_type_archive( 'itinerario' ) ) {
		$plugin_template = MPI_PLUGIN_DIR . 'templates/archive-itinerario.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'mpi_template_include', 99 );

/**
 * Ruta relativa al sitio (sin slash inicial/final) para comprobar redirecciones legacy.
 *
 * @return string
 */
function mpi_get_public_request_path() {
	global $wp;
	if ( isset( $wp->request ) && is_string( $wp->request ) && $wp->request !== '' ) {
		return $wp->request;
	}

	$raw = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = wp_parse_url( $raw, PHP_URL_PATH );
	if ( ! is_string( $path ) || $path === '' ) {
		return '';
	}

	$home_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( is_string( $home_path ) && $home_path !== '' && $home_path !== '/' ) {
		$home_path = untrailingslashit( $home_path );
		if ( strpos( $path, $home_path ) === 0 ) {
			$path = (string) substr( $path, strlen( $home_path ) );
		}
	}

	return trim( $path, '/' );
}

/**
 * Redirige URLs antiguas en español a las slugs en inglés (301).
 *
 * - /itinerario/             -> /itineraries/
 * - /itinerario/post-slug/   -> /itinerary/post-slug/
 *
 * @return void
 */
function mpi_redirect_legacy_itinerario_urls() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$req = mpi_get_public_request_path();
	if ( $req === '' ) {
		return;
	}

	if ( 'itinerario' === $req ) {
		wp_safe_redirect( get_post_type_archive_link( 'itinerario' ), 301 );
		exit;
	}

	if ( preg_match( '#^itinerario/([^/]+)$#', $req, $m ) ) {
		$post = get_page_by_path( $m[1], OBJECT, 'itinerario' );
		if ( $post instanceof WP_Post ) {
			wp_safe_redirect( get_permalink( $post ), 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'mpi_redirect_legacy_itinerario_urls', 1 );

/**
 * Encola estilos y scripts solo en archivo/single de itinerario
 */
function mpi_enqueue_assets() {
	if ( ! is_singular( 'itinerario' ) && ! is_post_type_archive( 'itinerario' ) ) {
		return;
	}

	wp_enqueue_style(
		'mpi-style',
		MPI_PLUGIN_URL . 'assets/css/style.css',
		array(),
		MPI_VERSION
	);

	wp_enqueue_script(
		'mpi-script',
		MPI_PLUGIN_URL . 'assets/js/script.js',
		array( 'jquery' ),
		MPI_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'mpi_enqueue_assets' );
