<?php
/**
 * Metaboxes nativos para Itinerario (sin ACF).
 *
 * Campos:
 * - Precio desde
 * - Descripción general
 * - Extras incluidos (texto o lista)
 * - Itinerario día por día (repetible dinámico)
 *
 * @package Mi_Plugin_Itinerarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MPI_META_PRECIO_DESDE', '_mpi_precio_desde' );
define( 'MPI_META_DESCRIPCION_GENERAL', '_mpi_descripcion_general' );
define( 'MPI_META_EXTRAS_INCLUIDOS', '_mpi_extras_incluidos' );
define( 'MPI_META_SUBTITULO', '_mpi_subtitulo' );
define( 'MPI_META_DIAS', '_mpi_dias' );

/**
 * Añade los metaboxes al CPT itinerario.
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
 * Renderiza el contenido de los metaboxes.
 *
 * @param WP_Post $post Objeto del post actual.
 */
function mpi_render_metaboxes( $post ) {
	wp_nonce_field( 'mpi_save_metaboxes', 'mpi_metabox_nonce' );

	$precio_desde        = get_post_meta( $post->ID, MPI_META_PRECIO_DESDE, true );
	$descripcion_general = get_post_meta( $post->ID, MPI_META_DESCRIPCION_GENERAL, true );
	$subtitulo            = get_post_meta( $post->ID, MPI_META_SUBTITULO, true );
	$extras_incluidos    = get_post_meta( $post->ID, MPI_META_EXTRAS_INCLUIDOS, true );
	$dias                = get_post_meta( $post->ID, MPI_META_DIAS, true );

	// Por compatibilidad: si se guardó como JSON en el pasado.
	if ( is_string( $dias ) ) {
		$decoded = json_decode( $dias, true );
		if ( is_array( $decoded ) ) {
			$dias = $decoded;
		}
	}

	if ( ! is_array( $dias ) ) {
		$dias = array();
	}

	// Si no hay días, mostramos un bloque inicial vacío.
	if ( empty( $dias ) ) {
		$dias = array(
			array(
				'titulo'     => '',
				'descripcion' => '',
				'imagen_id'  => 0,
			),
		);
	}
	?>

	<p>
		<label for="mpi_precio_desde"><strong><?php esc_html_e( 'Precio desde', 'mi-plugin-itinerarios' ); ?></strong></label><br>
		<input type="text" id="mpi_precio_desde" name="mpi_precio_desde" value="<?php echo esc_attr( $precio_desde ); ?>" class="large-text">
	</p>

	<p>
		<label for="mpi_descripcion_general"><strong><?php esc_html_e( 'Descripción general', 'mi-plugin-itinerarios' ); ?></strong></label><br>
		<textarea id="mpi_descripcion_general" name="mpi_descripcion_general" rows="5" class="large-text"><?php echo esc_textarea( $descripcion_general ); ?></textarea>
	</p>

	<p>
		<label for="mpi_subtitulo"><strong><?php esc_html_e( 'Subtítulo', 'mi-plugin-itinerarios' ); ?></strong></label><br>
		<input type="text" id="mpi_subtitulo" name="mpi_subtitulo" value="<?php echo esc_attr( $subtitulo ); ?>" class="large-text">
	</p>

	<p>
		<label for="mpi_extras_incluidos"><strong><?php esc_html_e( 'Included extras', 'mi-plugin-itinerarios' ); ?></strong></label><br>
		<textarea id="mpi_extras_incluidos" name="mpi_extras_incluidos" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Una opción por línea (recomendado).', 'mi-plugin-itinerarios' ); ?>"><?php echo esc_textarea( $extras_incluidos ); ?></textarea>
		<p class="description"><?php esc_html_e( 'Puedes escribir texto libre o un listado (un extra por línea).', 'mi-plugin-itinerarios' ); ?></p>
	</p>

	<hr>

	<p>
		<strong><?php esc_html_e( 'Itinerario día por día', 'mi-plugin-itinerarios' ); ?></strong>
		<span class="description"><?php esc_html_e( '(El número de días se calcula por los bloques creados)', 'mi-plugin-itinerarios' ); ?></span>
	</p>

	<div id="mpi-dias-wrapper">
		<?php foreach ( $dias as $dia ) : ?>
			<?php
				$titulo = isset( $dia['titulo'] ) ? (string) $dia['titulo'] : '';
				$desc   = isset( $dia['descripcion'] ) ? (string) $dia['descripcion'] : '';
				$img_id = isset( $dia['imagen_id'] ) ? absint( $dia['imagen_id'] ) : 0;
			?>
			<div class="mpi-dia-item" style="margin-bottom: 16px; padding: 12px; border: 1px solid #e5e5e5; border-radius: 6px;">
				<p>
					<label><strong><?php esc_html_e( 'Título del día (opcional)', 'mi-plugin-itinerarios' ); ?></strong></label><br>
					<input type="text" name="mpi_dia_titulo[]" value="<?php echo esc_attr( $titulo ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Día 1 (opcional)', 'mi-plugin-itinerarios' ); ?>">
				</p>

				<p>
					<label><strong><?php esc_html_e( 'Descripción', 'mi-plugin-itinerarios' ); ?></strong></label><br>
					<textarea name="mpi_dia_descripcion[]" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Describe el día...', 'mi-plugin-itinerarios' ); ?>"><?php echo esc_textarea( $desc ); ?></textarea>
				</p>

				<p>
					<label><strong><?php esc_html_e( 'Imagen del día', 'mi-plugin-itinerarios' ); ?></strong></label><br>
					<input type="hidden" name="mpi_dia_imagen_id[]" value="<?php echo esc_attr( (string) $img_id ); ?>" class="mpi-dia-imagen-id">
					<button type="button" class="button mpi-dia-imagen-boton"><?php esc_html_e( 'Seleccionar imagen', 'mi-plugin-itinerarios' ); ?></button>
					<button type="button" class="button button-secondary mpi-dia-eliminar"><?php esc_html_e( 'Eliminar día', 'mi-plugin-itinerarios' ); ?></button>
					<div class="mpi-dia-imagen-preview" style="margin-top: 10px;">
						<?php
							if ( $img_id ) {
								echo wp_get_attachment_image( $img_id, array( 150, 150 ) );
							}
						?>
					</div>
				</p>
			</div>
		<?php endforeach; ?>
	</div>

	<p>
		<button type="button" class="button button-primary" id="mpi-dia-agregar"><?php esc_html_e( 'Añadir día', 'mi-plugin-itinerarios' ); ?></button>
	</p>

	<template id="tmpl-mpi-dia-item">
		<div class="mpi-dia-item" style="margin-bottom: 16px; padding: 12px; border: 1px solid #e5e5e5; border-radius: 6px;">
			<p>
				<label><strong><?php esc_html_e( 'Título del día (opcional)', 'mi-plugin-itinerarios' ); ?></strong></label><br>
				<input type="text" name="mpi_dia_titulo[]" value="" class="widefat" placeholder="<?php esc_attr_e( 'Día 1 (opcional)', 'mi-plugin-itinerarios' ); ?>">
			</p>

			<p>
				<label><strong><?php esc_html_e( 'Descripción', 'mi-plugin-itinerarios' ); ?></strong></label><br>
				<textarea name="mpi_dia_descripcion[]" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Describe el día...', 'mi-plugin-itinerarios' ); ?>"></textarea>
			</p>

			<p>
				<label><strong><?php esc_html_e( 'Imagen del día', 'mi-plugin-itinerarios' ); ?></strong></label><br>
				<input type="hidden" name="mpi_dia_imagen_id[]" value="" class="mpi-dia-imagen-id">
				<button type="button" class="button mpi-dia-imagen-boton"><?php esc_html_e( 'Seleccionar imagen', 'mi-plugin-itinerarios' ); ?></button>
				<button type="button" class="button button-secondary mpi-dia-eliminar"><?php esc_html_e( 'Eliminar día', 'mi-plugin-itinerarios' ); ?></button>
				<div class="mpi-dia-imagen-preview" style="margin-top: 10px;"></div>
			</p>
		</div>
	</template>

	<?php
}

/**
 * Encola scripts necesarios para el editor dinámico de días.
 */
function mpi_enqueue_admin_metabox_assets( $hook ) {
	// $hook_suffix suele ser 'post.php' o 'post-new.php' en edición/creación.
	// get_current_screen() no siempre está listo en admin_enqueue_scripts, por eso usamos $typenow.
	global $typenow;
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$is_itinerario = false;
	if ( isset( $typenow ) && 'itinerario' === $typenow ) {
		$is_itinerario = true;
	} elseif ( isset( $_GET['post_type'] ) && 'itinerario' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) {
		// post-new.php suele traer post_type en query.
		$is_itinerario = true;
	} elseif ( isset( $_GET['post'] ) ) {
		// post.php en edición trae el ID en post.
		$post_id = absint( $_GET['post'] );
		if ( $post_id ) {
			$is_itinerario = 'itinerario' === get_post_type( $post_id );
		}
	}
	if ( ! $is_itinerario ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery' );

	$inline_js = <<<JS
function mpiInitDiasMetabox() {
  var wrapper = document.getElementById('mpi-dias-wrapper');
  var addBtn = document.getElementById('mpi-dia-agregar');
  var template = document.getElementById('tmpl-mpi-dia-item');
  if (!wrapper || !addBtn || !template) return;

  function clearDayItem(item) {
    var titleInput = item.querySelector('input[name="mpi_dia_titulo[]"]');
    var descInput = item.querySelector('textarea[name="mpi_dia_descripcion[]"]');
    var hiddenImg = item.querySelector('input[name="mpi_dia_imagen_id[]"]');
    var preview = item.querySelector('.mpi-dia-imagen-preview');
    if (titleInput) titleInput.value = '';
    if (descInput) descInput.value = '';
    if (hiddenImg) hiddenImg.value = '';
    if (preview) preview.innerHTML = '';
  }

  addBtn.addEventListener('click', function() {
    var node = template.content.cloneNode(true);
    wrapper.appendChild(node);
  });

  document.addEventListener('click', function(e) {
    var target = e.target;
    if (!target) return;

    // Eliminar día
    if (target.classList && target.classList.contains('mpi-dia-eliminar')) {
      var item = target.closest('.mpi-dia-item');
      if (!item) return;
      var items = wrapper.querySelectorAll('.mpi-dia-item');

      // Evita quedar sin estructura: limpia el último si sólo queda 1.
      if (items.length <= 1) {
        clearDayItem(item);
      } else {
        item.remove();
      }
      return;
    }

    // Seleccionar imagen (wp.media)
    if (target.classList && target.classList.contains('mpi-dia-imagen-boton')) {
      e.preventDefault();
      var item = target.closest('.mpi-dia-item');
      if (!item || !window.wp || !wp.media) return;

      var hiddenImg = item.querySelector('input.mpi-dia-imagen-id');
      var preview = item.querySelector('.mpi-dia-imagen-preview');
      if (!hiddenImg || !preview) return;

      var frame = wp.media({
        title: 'Seleccionar imagen',
        button: { text: 'Usar esta imagen' },
        multiple: false
      });

      frame.on('select', function() {
        var attachment = frame.state().get('selection').first().toJSON();
        var id = attachment && attachment.id ? attachment.id : '';
        hiddenImg.value = id;

        var url = '';
        if (attachment && attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
          url = attachment.sizes.thumbnail.url;
        } else if (attachment && attachment.url) {
          url = attachment.url;
        }

        if (url) {
          preview.innerHTML = '<img src=\"' + url + '\" style=\"max-width:150px;height:auto;display:block;\" alt=\"Imagen del día\" />';
        } else {
          preview.innerHTML = '';
        }
      });

      frame.open();
    }
  }, true);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mpiInitDiasMetabox);
} else {
  mpiInitDiasMetabox();
}
JS;

	wp_add_inline_script( 'jquery', $inline_js );
}
add_action( 'admin_enqueue_scripts', 'mpi_enqueue_admin_metabox_assets' );

/**
 * Guarda los valores de los metaboxes.
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

	// Precio desde.
	if ( isset( $_POST['mpi_precio_desde'] ) ) {
		update_post_meta( $post_id, MPI_META_PRECIO_DESDE, sanitize_text_field( wp_unslash( $_POST['mpi_precio_desde'] ) ) );
	}

	// Descripción general.
	if ( isset( $_POST['mpi_descripcion_general'] ) ) {
		update_post_meta( $post_id, MPI_META_DESCRIPCION_GENERAL, sanitize_textarea_field( wp_unslash( $_POST['mpi_descripcion_general'] ) ) );
	}

	// Subtítulo (para la vista listados).
	if ( isset( $_POST['mpi_subtitulo'] ) ) {
		update_post_meta( $post_id, MPI_META_SUBTITULO, sanitize_text_field( wp_unslash( $_POST['mpi_subtitulo'] ) ) );
	}

	// Extras incluidos.
	if ( isset( $_POST['mpi_extras_incluidos'] ) ) {
		update_post_meta( $post_id, MPI_META_EXTRAS_INCLUIDOS, sanitize_textarea_field( wp_unslash( $_POST['mpi_extras_incluidos'] ) ) );
	}

	// Días: se calcula por el número de bloques creados (longitud de los arrays enviados).
	$titulos = isset( $_POST['mpi_dia_titulo'] ) ? (array) $_POST['mpi_dia_titulo'] : array();
	$descs   = isset( $_POST['mpi_dia_descripcion'] ) ? (array) $_POST['mpi_dia_descripcion'] : array();
	$imgs    = isset( $_POST['mpi_dia_imagen_id'] ) ? (array) $_POST['mpi_dia_imagen_id'] : array();

	$count = max( count( $titulos ), count( $descs ), count( $imgs ) );
	$dias  = array();

	for ( $i = 0; $i < $count; $i++ ) {
		$titulo = isset( $titulos[ $i ] ) ? sanitize_text_field( wp_unslash( $titulos[ $i ] ) ) : '';
		$desc   = isset( $descs[ $i ] ) ? sanitize_textarea_field( wp_unslash( $descs[ $i ] ) ) : '';
		$img_id = isset( $imgs[ $i ] ) ? absint( wp_unslash( $imgs[ $i ] ) ) : 0;

		$dias[] = array(
			'titulo'      => $titulo,
			'descripcion' => $desc,
			'imagen_id'   => $img_id,
		);
	}

	if ( ! empty( $dias ) ) {
		update_post_meta( $post_id, MPI_META_DIAS, $dias );
	} else {
		delete_post_meta( $post_id, MPI_META_DIAS );
	}
}
add_action( 'save_post_itinerario', 'mpi_save_metaboxes' );

