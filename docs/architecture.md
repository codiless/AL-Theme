# Arquitectura y referencia — AL Base 1.0.3

## Responsabilidades

La base separa consulta, presentación y datos:

```text
Gutenberg: atributos guardados en post_content
    ↓ render_callback: al_render_listing()
Query Engine: al_listing_query()
    ↓ { items, pages, kind }
Layout: components/listings/default.php
    ↓ cada item
Card dispatcher: al_render_card()
    ↓ variante registrada
Template part: components/cards/{variant}.php
    ↓ campos públicos cuando corresponda
Dynamic Data: al_dynamic_value()
```

La tarjeta no construye consultas. El motor no genera el HTML de las tarjetas. El registro de campos no define tipos de contenido. Una variante recipe-card, tour-card o service-card debe utilizar este recorrido sin duplicar el motor.

La consulta nativa Query Loop mantiene su funcionamiento independiente. La variación AL Latest content configura bloques de WordPress; no redirige sus consultas hacia Content Listing.

## Mapa del código

| Ruta | Responsabilidad |
| --- | --- |
| `functions.php` | Carga los diez módulos del padre; no contiene consultas ni markup de página. |
| `inc/setup.php` | Supports, traducciones, logo, menús, sidebar y hoja del editor. |
| `inc/assets.php` | Manifest, assets del padre y carga condicional de listados. |
| `inc/navigation.php` | Walker nativo, botones de submenú y fallback. |
| `inc/fields.php` | Registro público, lectura de valores y fuente Block Bindings. |
| `inc/query.php` | Normalización de IDs y consultas de posts/términos. |
| `inc/cards.php` | Registro de variantes y selección del template part. |
| `inc/blocks.php` | Registro de bloques, metadatos del inspector y render del listado. |
| `inc/breadcrumbs.php` | Integración SEO, fallback y schema opcional. |
| `inc/gutenberg.php` | Categoría de patrones y estilos Surface/Cards. |
| `inc/compatibility.php` | Selector de idiomas opcional WPML/Polylang. |
| `blocks/content-listing/block.json` | Atributos persistidos, supports y contexto. |
| `assets/src/js/editor.js` | Inspector, preview SSR, IDs y variación Query Loop. |
| `components/listings/default.php` | Wrapper del bloque, items, vacío y paginación. |
| `components/cards/*.php` | Markup de tarjetas; variantes incluidas delegan al template default. |
| `components/pagination/listing.php` | Enlaces de páginas o siguiente página para Load more. |
| `components/breadcrumbs/default.php` | Markup del fallback de breadcrumbs. |
| `template-parts/*` | Cabecera, pie y contenido de páginas/entradas. |
| `patterns/*` | Composiciones insertables de bloques. |
| `theme.json` | Tokens y estilos de contenido compartidos con Gutenberg. |
| `assets/src/css/app.css` | Shell y estilos exclusivos del frontend. |
| `assets/src/css/editor.css` | Estilos de contenido del editor, sin resets de interfaz. |
| `assets/src/css/listing.css` | Componentes de listado compartidos entre frontend y preview. |
| `assets/dist` | Assets compilados y manifest incluidos en distribución. |
| `tests` / `scripts` | Contratos y herramientas de mantenimiento. |

## Jerarquía de plantillas

`index.php` es el fallback para portada de entradas, archivos y búsqueda. `single.php` muestra entradas/CPT; `page.php` muestra páginas; `404.php` trata URLs inexistentes. Las plantillas elegibles de página son `templates/canvas.php` y `templates/sidebar.php`.

El proyecto puede añadir `single-{post_type}.php`, `archive-{post_type}.php` o templates específicos en el child theme. Se conserva la jerarquía de WordPress; no existe un router propio. El contenido raíz utiliza `entry-content is-layout-flow`, mientras que los grupos Gutenberg internos conservan sus layouts.

## Contrato de consulta

```php
$result = al_listing_query( $attributes, $context_id, $page );
// items: array de WP_Post o WP_Term.
// pages: número de páginas calculado.
// kind: 'post' o 'term'.
```

El resultado no incluye HTML. Para posts sin paginación, `pages` vale 1 en una consulta válida; un resultado inválido puede devolver 0. Para términos se calcula el total incluso sin mostrar paginación. No utilices `pages` como indicador de que existen items: comprueba `items`.

Las consultas de posts exigen un tipo público, status publish y ausencia de contraseña; ignoran sticky posts. La rama de términos exige una taxonomía pública. Los filtros de extensión reciben los argumentos después de estas decisiones y pueden modificarlos: deben conservar las restricciones apropiadas al sitio.

| Source | Comportamiento |
| --- | --- |
| `posts` | Consulta el `postType` seleccionado; puede ser post, page o CPT. |
| `terms` | Consulta términos de la taxonomía, no las entradas de esos términos. |
| `children` | Usa parent explícito; parent 0 usa context_id. Sin padre/contexto válido no lista todas las páginas. |
| `siblings` | Usa el padre del contexto y excluye el contexto; admite padre 0 para páginas raíz. |
| `related` | Obtiene términos del contexto en la taxonomía y excluye el contexto; sin términos devuelve vacío. |
| `relationship` | Lee IDs del campo marcado relationship y conserva su orden; vacío/no registrado devuelve vacío. |
| `manual` | Usa include con orden post__in; include vacío devuelve vacío. |

La rama de relación lee ACF sin formato (`get_field(..., false)`) o meta. No utiliza el callback de presentación de `al_dynamic_value()`. Los IDs deben pertenecer al tipo consultado y cumplir las demás restricciones. Las exclusiones se aplican también a las inclusiones; un resultado vacío de esa diferencia no se convierte en una consulta abierta.

## Atributos de Content Listing

Valores por defecto procedentes de `block.json`. Los controles del editor y la normalización del servidor pueden limitar su rango.

| Atributo | Tipo / default | Uso |
| --- | --- | --- |
| listingId | string / vacío | Identificador estable; el editor lo genera. |
| source | string / posts | Rama de consulta. |
| postType | string / post | Tipo público de posts. |
| taxonomy | string / category | Taxonomía para filtro o consulta de términos. |
| terms | array / [] | IDs para filtrar posts o incluir términos. |
| hideEmpty | boolean / true | Ocultar términos vacíos. |
| parent | number / 0 | Padre explícito en Children. |
| author | number / 0 | Autor; cero significa cualquiera. |
| include / exclude | array / [] | IDs de posts; include también selección manual. |
| relationField | string / vacío | Clave pública del campo de relación. |
| sortField | string / vacío | Clave pública marcada sortable. |
| perPage | number / 6 | Servidor limita de 1 a 100. |
| offset | number / 0 | Desplazamiento inicial; editor 0–100, servidor no negativo. |
| orderBy | string / date | Posts: date/title/menu_order/rand/post__in; términos: name/count/slug/include. |
| order | string / DESC | ASC o DESC. |
| pagination | string / none | none, pagination o load-more. |
| layout | string / grid | grid, list, horizontal, featured o compact. |
| columns | number / 3 | Escritorio, 1–6. |
| tabletColumns | number / 2 | Tablet, 1–4. |
| mobileColumns | number / 1 | Móvil, 1–2. |
| gap | string / m | xs, s, m, l, xl, 2xl; 2xl se convierte a token CSS 2-xl. |
| cardVariant | string / default | Clave del registro de variantes. |
| elements | array | image, category, title, excerpt, date, button por defecto. |
| fields | array / [] | Claves públicas a mostrar. |
| badgeField | string / vacío | Clave pública de badge. |
| headingLevel | number / 2 | H2–H6. |

Los supports nativos añaden color, margin, padding, align wide/full y anchor. `anchor` identifica el destino HTML; `listingId` identifica la consulta. No son el mismo contrato.

## Paginación y caché

El parámetro es `al-page-{listingId}`. La página está limitada entre 1 y 10000. El offset efectivo es `offset + (page - 1) * perPage`; el total de posts descuenta el offset inicial. Orden rand se sustituye por date cuando hay paginación.

Load more solicita el HTML de la siguiente URL, encuentra el wrapper con el mismo `data-al-listing`, añade sus items y reemplaza su paginación. Tiene timeout de 15 segundos, estado aria-busy, anuncio de resultado y foco en el primer enlace nuevo. Ante fallo retira la mejora del enlace para permitir la navegación normal. No crea un endpoint REST/AJAX propio ni actualiza automáticamente la URL del navegador al añadir resultados.

El caché debe distinguir los parámetros `al-page-*`. Los filtros de consulta no deben producir una respuesta pública dependiente de datos privados de sesión sin configurar correctamente el caché. Los cambios de contenido entre solicitudes pueden modificar los resultados: la paginación no es un snapshot de la base de datos.

## Registro público de datos

`al_listing_fields()` devuelve un array inicialmente vacío. Cada clave identifica un campo público; su definición puede contener `label`, `key`, `source`, `callback`, `sortable`, `numeric` y `relationship`.

`al_dynamic_value($post_id, $key)` devuelve string o cadena vacía. Da prioridad al callback callable; después utiliza ACF para source acf si está disponible; en caso contrario utiliza post meta. Solo devuelve valores escalares. Los arrays, objetos e imágenes estructuradas requieren presentación propia.

`sortable` ordena por la clave meta almacenada; no por el resultado de un callback. `numeric` elige meta_value_num. Los posts sin ese meta pueden quedar fuera por el comportamiento de la consulta meta. Una relación requiere almacenamiento de IDs; no basta con marcar un campo de texto como relationship.

La fuente Block Bindings `al-base/field` recibe `args.key` y utiliza el contexto postId. El registro es una lista explícita de datos destinados a publicación; no debe incluir campos privados solo para que aparezcan en un selector.

## Filtros y acciones

| Nombre | Argumentos entregados | Responsabilidad |
| --- | --- | --- |
| al_listing_fields | fields | Devolver registro de campos públicos. |
| al_card_variants | variants | Devolver clave → etiqueta de variantes. |
| al_listing_query_args | args, attributes, context_id | Ajustar argumentos WP_Query. |
| al_listing_term_query_args | args, attributes | Ajustar consulta de términos. |
| al_breadcrumb_items | items | Ajustar fallback, cada item con label/url. |
| al_breadcrumb_schema_enabled | false | Habilitar schema fallback; respeta detección de plugins SEO. |
| al_primary_menu_args | args | Ajustar wp_nav_menu del header. |
| al_header_search_enabled | true | Mostrar u ocultar búsqueda del header. |
| al_before_card / al_after_card / al_card_meta | item, attributes | Imprimir extensión en puntos de la tarjeta default. |
| al_header_cta / al_footer_cta | sin argumentos | Imprimir CTA en esos componentes. |

Usa el número de argumentos correspondiente en `add_filter()`/`add_action()`. Un template de tarjeta propio debe invocar los hooks si quiere mantener sus integraciones; no se ejecutan automáticamente alrededor de cualquier archivo de variante.

## Assets y compatibilidad

`al_asset_path()` y `al_asset_url()` resuelven **assets del padre** mediante el manifest; no son un mecanismo de override de assets del child. Los template parts sí se pueden sobrescribir en el child con la misma ruta. `functions.php` del child extiende mediante hooks; no reemplaza el del padre ni debe redeclarar funciones `al_*`.

Frontend carga app.css; editor carga editor.css; listado comparte listing.css sin dependencia del handle global al-base. Navegación y Load more son módulos pequeños. El editor utiliza librerías provistas por WordPress.

SEO: title-tag y breadcrumbs incluidos; canonical, description, OpenGraph y schema de industria pertenecen a plugins. Breadcrumbs prioriza Yoast, Rank Math y SEOPress cuando existen sus funciones; en la portada se omiten. Idiomas: el selector utiliza Polylang o WPML si están disponibles. WooCommerce tiene declaración de soporte, pero la base no garantiza todos sus flujos o extensiones: consulta [validación](validation.md).
