# AL Base

Theme híbrido de WordPress, independiente del sector, con PHP, Gutenberg y `theme.json`. Requiere WordPress 6.6+, PHP 8.1+ y Node 20.19+ para desarrollo. Los assets compilados ya están incluidos: **activar el theme no requiere Node, Vite ni plugins**.

## Manuales para trabajar con esta base

Documentación correspondiente a **AL Base 1.0.3**. Empieza por este README y continúa con el manual que corresponda:

| Manual | Para qué sirve |
| --- | --- |
| [Configuración y edición](docs/editor-guide.md) | Instalar, construir páginas, configurar listados, menús y espacios desde WordPress. |
| [Arquitectura y referencia](docs/architecture.md) | Entender el recorrido de datos, los archivos, atributos, filtros y acciones. |
| [Temas de especialidad](docs/specialty-themes.md) | Crear un child theme, conectar campos y añadir tarjetas sin modificar el motor. |
| [Desarrollo y mantenimiento](docs/development.md) | Compilar, traducir, probar, empaquetar, actualizar y diagnosticar problemas. |
| [Validación](docs/validation.md) | Distinguir pruebas ejecutadas de verificaciones pendientes. |
| [Historial](docs/changelog.md) | Identificar cambios entre versiones. |

AL Base es un theme híbrido: las páginas se editan con bloques, pero las plantillas y la cabecera se resuelven con PHP. No incluye un sistema completo de plantillas FSE ni un constructor propio de cabeceras. Los CPT y la lógica de negocio pertenecen al plugin de cada proyecto; ACF es opcional.

## Espacios y extensiones desde 1.0.3

El margen lateral base se define en `settings.custom.layout.gutter` de `theme.json`: 16–24 px adaptables. Se aplica una sola vez en los contenedores PHP y en los bloques de primer nivel del contenido. Los bloques anidados conservan el layout nativo de Gutenberg. La plantilla con sidebar utiliza el margen de su contenedor exterior.

Los valores de margin y padding elegidos en Gutenberg prevalecen sobre los valores base del theme; también en móvil. Usa los presets fluidos L, XL y 2XL para espacios adaptables. Un valor fijo elegido por el autor continúa siendo fijo: el theme no lo reduce automáticamente. Los bloques `alignfull` llegan al borde y necesitan padding propio si contienen texto. Los grupos de estilo Surface incluyen padding por defecto, que se puede sustituir desde el editor.

El contenedor PHP de contenido usa `is-layout-flow` para mantener el espaciado vertical nativo sin imponer los márgenes horizontales `!important` del layout constrained. Los grupos internos siguen admitiendo constrained, flex y grid. No vuelvas a aplicar reglas globales de ancho a `.alignwide` en temas de especialidad. Los tokens H1–H6 usan variables `--wp--preset--font-size--h-1` hasta `h-6`; 2XL genera `--wp--preset--spacing--2-xl`.

## Actualización 1.0.1: aislamiento de Gutenberg

El editor carga `assets/src/css/editor.css` mediante `add_editor_style()`, mientras `app.css` se reserva al frontend. El CSS compartido de Content Listing no depende del handle `al-base`; todas sus reglas se limitan a sus componentes. Así los resets y los estilos de formularios del sitio no alcanzan la barra de herramientas de WordPress. La tipografía y los tokens del contenido continúan viniendo de `theme.json`.

Después de instalar el ZIP actualizado, recarga el editor con Ctrl+F5. La versión figura en Apariencia → Temas → Detalles del tema. Evita cargar `app.css` mediante `enqueue_block_assets` en extensiones: ese hook también puede cargar CSS fuera del iframe de Gutenberg.

## Instalación

1. Copia esta carpeta como `wp-content/themes/al-base` o instala el ZIP `release/al-base.zip` desde Apariencia → Temas.
2. Activa **AL Base**, configura el logo y los menús Primary/Footer en Apariencia.
3. Define la portada y la página de entradas en Ajustes → Lectura.
4. Construye páginas con Gutenberg y los patrones de la categoría AL Base.
5. En páginas que usan un Hero con H1, selecciona la plantilla **Gutenberg canvas**. La plantilla normal ya imprime el título como H1.

## Edición y listados

Para entradas habituales, usa **Query Loop**, la variación **AL Latest content**, o los patrones Latest/Featured content. Sus tarjetas son bloques nativos editables. La base no reemplaza Query Loop ni interfiere con sus consultas.

Para consultas avanzadas, inserta **Content Listing**. Configura fuente, CPT, taxonomía, términos por ID, autor, padre, inclusión/exclusión de IDs, orden, límite, offset y paginación en la barra lateral. Detecta los CPT públicos expuestos a la REST API por su plugin. Un CPT sin `show_in_rest` se puede renderizar desde PHP pero no aparece en los selectores del editor.

- **Children**: padre `0` utiliza el post/página actual; otro ID selecciona un padre explícito. Selecciona el tipo `page` para páginas hijas.
- **Siblings**: mismo padre del contenido actual, excluyendo el contenido actual; funciona también con páginas de primer nivel.
- **Related by taxonomy**: comparte términos con el contenido actual y lo excluye. Sin términos devuelve un listado vacío.
- **Related by public field**: relación de IDs mediante un campo del registro público, con o sin ACF.
- **Manual**: respeta el orden de IDs seleccionados y las exclusiones; una selección vacía no devuelve todos los posts.
- **Taxonomy terms**: devuelve términos reales y sus enlaces; usa el mismo layout y sistema de tarjetas.

Las columnas se adaptan al ancho del contenedor (móvil 1–2, tablet 1–4, escritorio 1–6). El inspector permite Grid, List, Horizontal, Featured + Grid y Compact, así como tarjetas Default, Minimal, Horizontal, Compact y Featured. Colores, padding, márgenes y ancho wide/full usan los controles nativos del bloque. El orden de elementos se configura subiéndolos dentro de la lista; cada elemento se puede activar/desactivar.

Cada listado guarda un ID estable. Duplicar un bloque en el editor genera un ID nuevo. Mantén IDs únicos si escribes bloques manualmente. La paginación usa `?al-page-ID=N`, de manera independiente por listado. Load more conserva un enlace real, añade HTML renderizado por el servidor, anuncia el resultado y mueve el foco al nuevo contenido. Si falla, el enlace permite navegar normalmente. Con paginación el orden aleatorio se sustituye por fecha para evitar resultados repetidos entre solicitudes.

## Arquitectura

```text
inc/query.php                  Query Engine → WP_Query / WP_Term_Query
components/listings/default.php Layout → container, items and pagination
inc/cards.php                  Card registry and dispatcher
components/cards/*.php         Template parts, overridable by child themes
inc/fields.php                 Dynamic Data → public meta / optional ACF / callbacks
```

`functions.php` únicamente carga módulos. Los templates siguen la jerarquía PHP de WordPress: `index.php` resuelve archivos, categorías, taxonomías, autores, resultados de búsqueda y archivos CPT; `single.php` resuelve entradas/CPT; `page.php` resuelve páginas. WordPress permite añadir templates específicos en un child theme sin crear un router propio.

Los headers y footers usan `get_template_part()`. El header ofrece logo, menú nativo, búsqueda, hook CTA y selector WPML/Polylang opcional. Desde 1.0.2, los enlaces son visibles en escritorio; en móvil un botón abre/cierra el panel. Los submenús tienen botones independientes de los enlaces del padre y se cierran con Escape, clic exterior o al salir el foco. Sin JavaScript, los enlaces y submenús quedan visibles. No se usa un modal ni un focus trap.

### Configurar menús y mega menús

1. En **Apariencia → Menús**, asigna tu menú a **Primary navigation**. Los elementos indentados forman submenús; no necesitas un plugin para desplegables normales.
2. Para un mega menú, abre **Opciones de pantalla** y activa **Clases CSS**.
3. Añade `al-mega-menu` al elemento padre de primer nivel.
4. Sus hijos directos forman las columnas. Los nietos son los enlaces de cada columna. En móvil se muestran apilados dentro del mismo desplegable.

Ejemplo: `Servicios [al-mega-menu] → Consultoría → Estrategia, Auditoría` y `Servicios → Desarrollo → Websites, Integraciones`.

El soporte incluido es un mega menú de enlaces en columnas, no un constructor visual de banners, imágenes o widgets. Para integrar un plugin o un walker propio, utiliza el filtro `al_primary_menu_args`. El filtro `al_header_search_enabled` permite desactivar la búsqueda de la cabecera. Si no hay menú asignado, se muestran Inicio y las páginas publicadas de primer nivel.

## Campos públicos y ACF opcional

Registra únicamente datos que puedan mostrarse públicamente, desde un plugin de proyecto o el child theme:

```php
add_filter( 'al_listing_fields', function ( $fields ) {
    $fields['cost'] = array(
        'label'    => __( 'Cost', 'project-domain' ),
        'key'      => 'project_cost',
        'source'   => 'acf', // 'meta' por defecto; ACF usa meta como fallback.
        'sortable' => true,
        'numeric'  => true,
    );
    $fields['related_items'] = array(
        'label'        => __( 'Related items', 'project-domain' ),
        'key'          => 'project_related_ids',
        'source'       => 'acf',
        'relationship' => true,
    );
    return $fields;
} );
```

Los campos de relación deben guardar IDs simples o arrays de IDs; ACF se lee sin formato para recibir IDs y no objetos. Las tarjetas muestran valores escalares escapados. Para imágenes, estructuras, formatos monetarios o cálculos, usa una variante de tarjeta o un `callback` del registro. Las consultas por campo ordenable omiten posts que no tienen ese meta, según el comportamiento de WP_Query.

Block Bindings expone la fuente `al-base/field` y recibe `args.key` del registro. También puedes utilizar `core/post-meta` para campos registrados con `show_in_rest`; esa configuración pertenece al plugin del proyecto.

```html
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"al-base/field","args":{"key":"cost"}}}}} -->
<p></p>
<!-- /wp:paragraph -->
```

## Extensiones y child themes

Usa prefijo/text domain propios para cada proyecto. El core no registra CPT, relaciones de negocio, reservas, reviews ni schemas de industria.

```php
add_filter( 'al_card_variants', function ( $variants ) {
    $variants['project-card'] = __( 'Project card', 'project-domain' );
    return $variants;
} );
```

Crea `components/cards/project-card.php` en el child theme. Recibe `$args['item']`, `attributes`, `kind` y `variant`. Usa `kind` para distinguir WP_Post de WP_Term. También puedes sobrescribir `components/listings/default.php`, breadcrumbs, paginación, header, footer y templates. El `theme.json` del child theme extiende los tokens del padre. Añade su CSS usando el handle `al-base` como dependencia.

Hooks disponibles: `al_before_card`, `al_after_card`, `al_card_meta`, `al_header_cta`, `al_footer_cta`. Filtros: `al_listing_query_args`, `al_listing_term_query_args`, `al_listing_fields`, `al_card_variants`, `al_breadcrumb_items`, `al_breadcrumb_schema_enabled`, `al_primary_menu_args`, `al_header_search_enabled`. Los filtros de consultas son código confiable: sus extensiones deben conservar visibilidad pública, permisos y paginación.

## Diseño, SEO y rendimiento

`theme.json` concentra paleta, fuentes, tamaños, espacios, anchos, sombras y radios. Los breakpoints figuran como tokens de documentación; las condiciones de media/container queries requieren valores estáticos en CSS, porque CSS no permite `var()` allí. La base usa CSS moderno sin Tailwind: evita añadir una dependencia sin utilidad en este core.

No hay fuentes externas, React frontend, jQuery global, sesiones ni endpoints personalizados. WordPress genera imágenes con dimensiones y srcset; las imágenes principales de entradas usan carga eager/high, y las tarjetas lazy. Revisa el LCP en cada página real, especialmente si usas una imagen de portada dentro del contenido.

El theme usa title-tag, HTML semántico y breadcrumbs. No genera canonical, description, OpenGraph ni schemas Article/Recipe/etc. Prefiere breadcrumbs Yoast, Rank Math o SEOPress, con fallback propio; BreadcrumbList propio es opt-in mediante `al_breadcrumb_schema_enabled` y se desactiva ante esos plugins SEO.

Las clases del HTML renderizado por PHP están presentes en el documento para herramientas como Remove Unused CSS. Page cache/CDN deben respetar los parámetros `al-page-*` o excluir esas solicitudes del caché; ignorarlos devolvería siempre la primera página. Los estilos de bloques nativos se cargan por demanda. La cabecera carga un módulo pequeño de navegación; Load more carga otro solo cuando se utiliza. El script del editor usa las bibliotecas de WordPress.

## Desarrollo

```sh
npm ci
npm run dev     # Vite build --watch: actualiza assets/dist, sin servidor/HMR.
npm run build   # Archivos minificados con hash y manifest.
npm run pot
npm run translations
npm run check
npm run package
```

`dev` usa compilación continua para que PHP y el editor iframe lean el mismo manifest sin proxies ni un origen adicional. `assets/dist` se distribuye con el theme. El cargador usa los fuentes si falta el manifest. Para cambiar el editor, reconstruye antes de probar.

`npm run check` verifica JSON, JS, manifest y sintaxis PHP cuando está disponible. Configura `AL_PHP` si PHP no está en PATH. También ejecuta contratos aislados de consulta; no sustituyen pruebas de base de datos, editor ni navegador.

Si defines `AL_WP_ROOT` con la ruta de una instalación WordPress, el check además utiliza su parser de bloques y lector MO reales, sin cargar la configuración ni acceder a la base de datos.

Se incluyen POT, PO, MO y traducciones JSON del editor para `es_ES` y `es_PE`. El catálogo español está en `languages/es.json`. Para otros idiomas usa Poedit o `wp i18n make-pot`, `make-mo` y `make-json`; no almacenes traducciones de contenido en opciones privadas del theme. Los IDs de posts/términos usados en selección manual pueden requerir mapear IDs por idioma mediante `al_listing_query_args`, dependiendo de la configuración WPML/Polylang.

## Validación

Consulta [docs/validation.md](docs/validation.md) para resultados verificados y pruebas de integración pendientes. No se han medido Lighthouse, Core Web Vitals ni WCAG sobre un sitio publicado. Las compatibilidades con plugins propietarios requieren sus instalaciones/licencias y un entorno de pruebas.

APIs de referencia: [Query Loop](https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/extending-the-query-loop-block/), [Block Bindings](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/), [Vite manifest](https://vite.dev/guide/backend-integration.html).
