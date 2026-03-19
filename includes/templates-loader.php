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
 * Añade una URL alternativa para el archive del CPT:
 * - /itineraries/ -> archive de 'itinerario'
 *
 * @return void
 */
function mpi_add_rewrite_rules() {
	add_rewrite_rule( '^itineraries/?$', 'index.php?post_type=itinerario', 'top' );
}
add_action( 'init', 'mpi_add_rewrite_rules' );

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
