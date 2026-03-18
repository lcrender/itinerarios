<?php
/**
 * Plugin Name: Itinerarios
 * Plugin URI: https://locomotorarender.com
 * Description: Gestiona itinerarios turísticos con CPT, metaboxes, listado y detalle con reserva vía Contact Form 7.
 * Version: 1.0.0
 * Author: Locomotora Render
 * Author URI: https://locomotorarender.com
 * License: GPL v2 or later
 * Text Domain: mi-plugin-itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MPI_VERSION', '1.0.0' );
define( 'MPI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MPI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Carga los archivos del plugin
 */
function mpi_load_plugin() {
	require_once MPI_PLUGIN_DIR . 'includes/cpt.php';
	require_once MPI_PLUGIN_DIR . 'includes/metaboxes.php';
	require_once MPI_PLUGIN_DIR . 'includes/settings.php';
	require_once MPI_PLUGIN_DIR . 'includes/shortcodes.php';
	require_once MPI_PLUGIN_DIR . 'includes/templates-loader.php';
}

// Asegura que todo esté disponible también durante la activación.
mpi_load_plugin();

/**
 * Activación: flush rewrite rules
 */
function mpi_activate() {
	mpi_register_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mpi_activate' );

/**
 * Desactivación: flush rewrite rules
 */
function mpi_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mpi_deactivate' );
