<?php
/**
 * Custom Post Type: Itinerario
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra el CPT Itinerarios
 */
function mpi_register_cpt() {
	$labels = array(
		'name'               => _x( 'Itinerarios', 'post type general name', 'mi-plugin-itinerarios' ),
		'singular_name'      => _x( 'Itinerario', 'post type singular name', 'mi-plugin-itinerarios' ),
		'menu_name'          => _x( 'Itinerarios', 'admin menu', 'mi-plugin-itinerarios' ),
		'add_new'            => _x( 'Añadir nuevo', 'itinerario', 'mi-plugin-itinerarios' ),
		'add_new_item'       => __( 'Añadir nuevo itinerario', 'mi-plugin-itinerarios' ),
		'edit_item'          => __( 'Editar itinerario', 'mi-plugin-itinerarios' ),
		'new_item'           => __( 'Nuevo itinerario', 'mi-plugin-itinerarios' ),
		'view_item'          => __( 'Ver itinerario', 'mi-plugin-itinerarios' ),
		'search_items'       => __( 'Buscar itinerarios', 'mi-plugin-itinerarios' ),
		'not_found'          => __( 'No se encontraron itinerarios', 'mi-plugin-itinerarios' ),
		'not_found_in_trash' => __( 'No hay itinerarios en la papelera', 'mi-plugin-itinerarios' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		/* Archive: /itineraries/ — Single: /itinerary/nombre-del-post/ */
		'rewrite'            => array( 'slug' => 'itinerary' ),
		'capability_type'    => 'post',
		'has_archive'        => 'itineraries',
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-location-alt',
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
	);

	register_post_type( 'itinerario', $args );
}
add_action( 'init', 'mpi_register_cpt' );
