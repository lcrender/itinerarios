# Mi Plugin Itinerarios

Plugin de WordPress para gestionar itinerarios turísticos. Incluye CPT, metaboxes nativos, listado por shortcode, detalle con galería y programa, y reserva mediante Contact Form 7.

**Autor:** [Locomotora Render](https://locomotorarender.com)

---

## Instalación

1. Copia la carpeta `mi-plugin-itinerarios` dentro de `wp-content/plugins/`.
2. En el escritorio de WordPress ve a **Plugins** y activa **Mi Plugin Itinerarios**.
3. (Opcional) Instala y activa **Contact Form 7** si quieres usar el botón de reserva.
4. Ve a **Ajustes > Enlaces permanentes** y guarda los cambios para refrescar las reglas de reescritura.

---

## Uso del shortcode

Inserte en cualquier página o entrada el shortcode del listado:

```
[itinerarios_list]
```

**Atributos opcionales:**

| Atributo          | Descripción              | Por defecto |
|-------------------|--------------------------|-------------|
| `posts_per_page`  | Número de itinerarios     | 10          |
| `orderby`         | Ordenar por (date, title) | date       |
| `order`           | ASC o DESC                | DESC        |

**Ejemplos:**

```
[itinerarios_list]
[itinerarios_list posts_per_page="6"]
[itinerarios_list posts_per_page="12" orderby="title" order="ASC"]
```

---

## Botón "Reservar plaza" y Contact Form 7

El template del itinerario incluye un botón **Reservar plaza** que abre un modal con un formulario de Contact Form 7.

### Configurar el ID del formulario

Por defecto el plugin usa el shortcode `[contact-form-7 id="123" title="Reserva itinerario"]`. Debes cambiar `123` por el ID real de tu formulario en CF7.

**Opción 1 – Filtro en el tema (functions.php) o en un plugin:**

```php
add_filter( 'mpi_reserva_cf7_shortcode', function() {
    return '[contact-form-7 id="456" title="Reserva itinerario"]';
} );
```

Sustituye `456` por el ID de tu formulario (lo ves en **Contacto > Formularios** al editar el formulario).

### Incluir el itinerario en el envío del formulario

Para que el correo de CF7 incluya el nombre o ID del itinerario reservado:

1. En Contact Form 7, edita el formulario de reserva.
2. Añade un **campo oculto** con el nombre exacto: `itinerario-reserva`.
3. En la pestaña **Correo**, en el cuerpo del mensaje puedes usar la etiqueta `[itinerario-reserva]` para mostrar ese valor.

El plugin rellena automáticamente ese campo con el **título del itinerario** cuando el usuario abre el modal y envía el formulario.

---

## Estructura del plugin

```
mi-plugin-itinerarios/
├── mi-plugin-itinerarios.php   # Entrada del plugin
├── includes/
│   ├── cpt.php                 # Custom Post Type itinerario
│   ├── metaboxes.php           # Programa, galería, link externo
│   ├── shortcodes.php          # [itinerarios_list]
│   └── templates-loader.php    # template_include + enqueue
├── templates/
│   ├── archive-itinerario.php  # Listado por URL
│   └── single-itinerario.php   # Detalle + reserva
├── assets/
│   ├── css/style.css
│   └── js/script.js
└── README.md
```

---

## Licencia

GPL v2 or later.
