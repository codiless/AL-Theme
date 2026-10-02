# Crear temas de especialidad — AL Base 1.0.3

## Distribuir responsabilidades

Usa un **plugin de proyecto** para CPT, taxonomías, registro de meta, relaciones, reservas, formularios y lógica de negocio. Así esos datos siguen existiendo al cambiar de theme. Usa un **child theme** para colores, tipografía, patrones, templates y tarjetas. ACF puede facilitar la administración de campos, pero no debe convertirse en requisito del motor de listado.

Ejemplo: un proyecto turístico registra `project_tour` en su plugin; el child añade `tour-card`; Content Listing consulta ese CPT con el mismo motor que las entradas. Un proyecto de recetas añade `recipe-card` y sus propios campos, sin crear otro bloque de consulta para repetir Grid/List/etc.

## 1. Crear el child theme

La carpeta debe estar junto a `al-base`:

```text
wp-content/themes/
├── al-base/
└── project-theme/
    ├── style.css
    ├── functions.php
    ├── theme.json
    ├── assets/css/project.css
    └── components/cards/tour-card.php
```

Cabecera mínima de `project-theme/style.css`:

```css
/*
Theme Name: Project Theme
Template: al-base
Version: 1.0.0
Text Domain: project-theme
*/
```

`Template` debe coincidir exactamente con el nombre de carpeta del padre. Mantén instalado el padre y activa el child. No copies todo el padre: sobrescribe solo los componentes que cambien.

En `functions.php`, carga el CSS del proyecto después del CSS base:

```php
<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
    $file = get_stylesheet_directory() . '/assets/css/project.css';
    wp_enqueue_style(
        'project-theme',
        get_stylesheet_directory_uri() . '/assets/css/project.css',
        array( 'al-base' ),
        file_exists( $file ) ? (string) filemtime( $file ) : '1.0.0'
    );
}, 20 );
```

Esta hoja es del frontend. Si necesitas estilos de contenido equivalentes en Gutenberg, registra una hoja específica con `add_editor_style()` durante `after_setup_theme`. No cargues resets, estilos globales de botones, navegación o formularios del frontend en el editor.

## 2. Cambiar tokens

Ejemplo de `theme.json` del child:

```json
{
  "version": 3,
  "settings": {
    "layout": { "contentSize": "760px", "wideSize": "1280px" },
    "custom": { "layout": { "gutter": "clamp(1rem, 3vw, 1.75rem)" } }
  }
}
```

Modifica paleta, fuentes, espacios y radios en `theme.json` cuando correspondan al sistema visual. Al reemplazar colecciones de presets, incluye los slugs que el markup y los estilos existentes necesiten; no supongas que una lista parcial conservará todos los presets del padre. Después comprueba las variables generadas y los patrones existentes.

Los breakpoints personalizados de theme.json documentan valores; las media/container queries del CSS utilizan números estáticos. Cambiar un token de breakpoint no reescribe esas condiciones.

## 3. Registrar datos públicos

El siguiente ejemplo registra campos para contenido ya existente. No crea el CPT ni su formulario de administración; eso corresponde al plugin del proyecto.

```php
add_filter( 'al_listing_fields', function ( $fields ) {
    $fields['tour_duration'] = array(
        'label' => __( 'Duración', 'project-theme' ),
        'key' => 'project_duration',
        'source' => 'meta',
    );
    $fields['tour_price'] = array(
        'label' => __( 'Precio', 'project-theme' ),
        'key' => 'project_price',
        'source' => 'meta',
        'sortable' => true,
        'numeric' => true,
    );
    $fields['related_tours'] = array(
        'label' => __( 'Tours relacionados', 'project-theme' ),
        'key' => 'project_related_ids',
        'source' => 'acf',
        'relationship' => true,
    );
    return $fields;
} );
```

El editor recibe el registro al cargar la página. Recárgalo después de cambiar el registro. `source: acf` utiliza meta como alternativa si ACF no está instalado; eso solo funciona si el dato existe en esa clave meta. La alternativa no crea campos ni reproduce la interfaz de ACF.

Para precios ordenables guarda números sin símbolo monetario. Aplica formato al presentar. Para relaciones guarda IDs o arrays de IDs. Para datos calculados de presentación puedes utilizar:

```php
$fields['duration_label'] = array(
    'label' => __( 'Duración', 'project-theme' ),
    'callback' => function ( $post_id, $field ) {
        $days = absint( get_post_meta( $post_id, 'project_days', true ) );
        return $days ? sprintf( __( '%d días', 'project-theme' ), $days ) : '';
    },
);
```

Este fragmento va dentro del filtro, antes de devolver `$fields`. El callback se usa para mostrar datos y bindings; no calcula relaciones ni un campo de ordenación de SQL.

## 4. Añadir una tarjeta

Registra la variante en el child o el plugin:

```php
add_filter( 'al_card_variants', function ( $variants ) {
    $variants['tour-card'] = __( 'Tarjeta de tour', 'project-theme' );
    return $variants;
} );
```

Crea `components/cards/tour-card.php` en el child. Este ejemplo deliberadamente define una presentación propia de título/duración; no implementa todos los toggles del inspector de la tarjeta default:

```php
<?php
defined( 'ABSPATH' ) || exit;
$item = $args['item'] ?? null;
$attributes = $args['attributes'] ?? array();

// Esta variante muestra posts. Los términos utilizan la tarjeta base.
if ( 'term' === ( $args['kind'] ?? 'post' ) ) {
    get_template_part( 'components/cards/default', null, $args );
    return;
}
if ( ! $item instanceof WP_Post ) {
    return;
}
$heading = 'h' . max( 2, min( 6, absint( $attributes['headingLevel'] ?? 2 ) ) );
$duration = al_dynamic_value( $item->ID, 'tour_duration' );
?>
<article class="al-card al-card--tour-card">
    <?php do_action( 'al_before_card', $item, $attributes ); ?>
    <<?php echo $heading; ?> class="al-card__title">
        <a href="<?php echo esc_url( get_permalink( $item ) ); ?>">
            <?php echo esc_html( get_the_title( $item ) ); ?>
        </a>
    </<?php echo $heading; ?>>
    <?php if ( '' !== $duration ) : ?>
        <p class="al-card__meta"><?php echo esc_html( $duration ); ?></p>
    <?php endif; ?>
    <?php do_action( 'al_card_meta', $item, $attributes ); ?>
    <?php do_action( 'al_after_card', $item, $attributes ); ?>
</article>
```

No hay WP_Query en la tarjeta. Si debe respetar elementos, orden, badge, extracto, etc., utiliza `components/cards/default.php` como referencia e implementa ese contrato. Registrar una variante sin crear su template puede dejar el listado sin tarjeta: el dispatcher valida el registro, no la existencia del archivo.

Después selecciona la variante en Content Listing, conserva el Post type del proyecto y elige Grid/List/etc. La variante no registra un nuevo layout ni una nueva fuente por sí sola.

## 5. Extender consultas sin duplicar el motor

Utiliza primero los atributos existentes. Si falta una restricción concreta, añade un filtro acotado:

```php
add_filter( 'al_listing_query_args', function ( $query_args, $attributes, $context_id ) {
    if ( 'project_tour' !== ( $attributes['postType'] ?? '' ) ) {
        return $query_args;
    }
    $query_args['meta_query'][] = array(
        'key' => 'project_bookable',
        'value' => '1',
        'compare' => '=',
    );
    return $query_args;
}, 10, 3 );
```

Este ejemplo afecta **todos** los Content Listing del CPT project_tour. Si debe afectar solo uno, limita también por `listingId`. No altera los Query Loop nativos, que no pasan por este filtro. Conserva publish, has_password, límites y la relación coherente entre items y total de páginas.

La base no tiene un registro de nuevas fuentes o layouts equivalente al registro de tarjetas. Si necesitas una nueva fuente/control, evalúa primero APIs nativas y después una extensión explícita del proyecto. No basta con enviar un source inventado: una cadena desconocida no crea una rama de consulta nueva.

## 6. Bindings y componentes

Para mostrar un campo registrado en un párrafo:

```html
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"al-base/field","args":{"key":"tour_duration"}}}}} -->
<p></p>
<!-- /wp:paragraph -->
```

Comprueba contexto postId, compatibilidad del atributo enlazado y resultado publicado. El inspector de AL Base no es un gestor visual completo de bindings para todos los bloques. Para meta nativo usa el registro del plugin y las APIs correspondientes de WordPress.

Sobrescribe header, footer, breadcrumbs, paginación o layout copiando su ruta relativa al child. Al sobrescribir `components/listings/default.php`, conserva `data-al-listing`, `.al-listing__items`, `.al-pagination` y el status si quieres conservar Load more. Cambiar ese contrato requiere actualizar su JavaScript.

Para desactivar búsqueda del header:

```php
add_filter( 'al_header_search_enabled', '__return_false' );
```

Para añadir CTA:

```php
add_action( 'al_header_cta', function () {
    echo '<a class="wp-element-button" href="' . esc_url( home_url( '/contacto/' ) ) . '">'
        . esc_html__( 'Contactar', 'project-theme' ) . '</a>';
} );
```

Confirma que la página destino existe. No edites el walker del padre para integrar un plugin; utiliza `al_primary_menu_args` y revisa el contrato de navegación.

## 7. Entregar una especialidad

Documenta CPT/taxonomías y plugin responsable, claves meta y unidades, campos públicos, variantes, templates sobrescritos, configuración de caché y dependencias opcionales. Prueba con ACF desactivado, relaciones vacías, contenido sin imagen, títulos largos, idiomas y móvil. Consulta [validación](validation.md) para las pruebas que aún necesitan el entorno del proyecto.

Evita guardar información estructural del proyecto únicamente en opciones privadas del theme. No pongas schemas Recipe/Tour/etc. en AL Base: deben pertenecer a la integración de especialidad y coordinarse con el plugin SEO para evitar duplicados.
