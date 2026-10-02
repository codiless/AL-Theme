# Validación y pruebas de integración

## Ejecutado

- Versión 1.0.3: bloques renderizados mediante `do_blocks()` en WordPress real. Grupos anchos/completos/anidados, columnas, sidebar y código comprobados a 320, 390, 768 y 1280 px. Sin desbordamiento horizontal de página. Conservados padding 32/24 px y margin vertical 40 px en grupo ancho, padding 28/20 px en Surface completo y márgenes laterales 22/18 px con padding 12/14 px en párrafo. Tipografía fluida confirmada con las variables generadas por WP_Theme_JSON.
- Beta AL con 1.0.3 instalada: portada con seis tarjetas y entrada Machu Picchu comprobadas a esos cuatro anchos, sin overflow de página. Menú abierto a 390 px sin overflow. H1 pasa de 29.8 px a 320 px a 52 px en escritorio. Captura móvil guardada en `release/espacios-mobile-1.0.3.png` (evidencia local, excluida del ZIP).

- Vite production build: correcto, con archivos minificados y hash.
- Parseo JSON de configuración y JS syntax check: correcto.
- PHP 8.2.29: todos los archivos PHP pasan `php -l`.
- 15 contratos aislados del motor: correctos. Cubren offset, total de páginas, exclusiones manuales, selección vacía, hijos, hermanos de primer nivel, contexto ausente, relaciones vacías, campos no registrados, orden numérico, orden estable y términos paginados.
- Catálogos español PHP/editor: generados para España y Perú.
- 12 patrones: parseados con WP_Block_Parser real, sin HTML libre fuera de bloques.
- MO español Perú: cargado con el lector nativo de WordPress; singular y plural correctos.
- Dependencias npm: instalación auditada sin vulnerabilidades reportadas.
- Versión 1.0.1: pruebas de aislamiento de assets correctas. Ejecutan los hooks de registro/carga y comprueban que los estilos de los bloques y del editor no dependen de la hoja global del frontend; el frontend conserva su hoja base.
- Beta AL local: actualización 1.0.1 aplicada y verificada en el DOM del administrador; `al-base-css` y la carga duplicada `al-editor-listing-css` ya no se incluyen. El navegador de Codex no muestra el documento blob del iframe de Gutenberg, por lo que la comprobación visual de los controles dentro del canvas debe realizarse en el navegador habitual.
- Versión 1.0.2: walker probado con WordPress real usando datos de ejemplo sin modificar menús. Confirmados IDs únicos, correspondencia de `aria-controls` y columnas de mega menú sin botones extra. Pruebas de navegador a 1280px y 390px: desplegables, segundo nivel, cierre con Escape, foco restaurado al botón y mega menú apilado; sin desbordamiento horizontal a 390px.
- Beta AL local con 1.0.2: enlaces Test 01–04 visibles en escritorio; a 390px el botón abre el panel y pulsar un enlace lo cierra devolviendo el foco al botón. Sin desbordamiento horizontal y sin errores JS observados en el frontend. Los enlaces actuales siguen apuntando a `#`, tal como estaban configurados.

## Pendiente en una instalación WordPress

Las consultas se prueban con dobles de WP_Query; las pruebas de patrones y gettext usan bibliotecas reales de WordPress sin cargar wp-config.php. Para la corrección 1.0.1 se revisó además el administrador y el frontend de Beta AL con el theme ya activo. No se ha ejecutado la batería completa de pruebas funcionales del editor y plugins. Esta tabla es una matriz de pruebas, **no una certificación de compatibilidad**.

| Entorno | Comprobación | Estado |
| --- | --- | --- |
| WordPress sin plugins | Activar, abrir frontend/editor, guardar bloques y revisar logs | Pendiente |
| Query Loop | Insertar variación y patrones, cambiar CPT, términos y paginación | Pendiente |
| Content Listing | Dos listados paginados en una página; duplicar IDs; publicar y recargar | Pendiente |
| CPT de proyecto | Público y show_in_rest; single y archive; tarjeta propia | Pendiente |
| Taxonomías | Jerarquía, empty/nonempty, filtros, términos y enlaces | Pendiente |
| ACF PRO | Campos escalares, relación IDs, orden meta; desactivar ACF y repetir | Pendiente |
| WPML + String Translation | Navegación, títulos, cadenas, selección manual por idioma | Pendiente |
| Polylang | Selector, filtrado por idioma y selección manual | Pendiente |
| Yoast SEO | Un canonical, un conjunto OG, breadcrumbs sin schema duplicado | Pendiente |
| Rank Math | Mismos controles, probado separadamente de Yoast | Pendiente |
| SEOPress | Mismos controles y fallback cuando breadcrumbs no está activo | Pendiente |
| WP Rocket | Minify/defer/delay JS, RUCSS, lazy loading, al-page-* | Pendiente |
| WooCommerce | Shop, producto, carrito, checkout y estilos responsive | Pendiente |
| Contact Form 7 | Labels, errores, inputs, botones y foco | Pendiente |
| Gravity Forms | Formularios multipágina, errores y campos condicionales | Pendiente |
| Fluent Forms / WPForms | Controles y estilos del formulario | Pendiente |
| Child theme | Override de tokens, card, header, footer y layout | Pendiente |
| Caché/CDN | Página inicial y al-page-* generan documentos distintos | Pendiente |

## Navegador y accesibilidad

- Viewports 360, 768, 1280px y contenedores estrechos dentro de Columns.
- Teclado: skip link, botón/panel del menú móvil, botones de submenú, formularios, paginación y FAQ con Details nativo.
- Load more: con JS bloqueado, JS demorado, error HTTP y desconexión; anuncio de estado y foco después de añadir tarjetas; Ctrl/click y apertura en nueva pestaña.
- No contenido, imagen ausente, títulos largos, campos vacíos, página fuera de rango.
- Gutenberg: patrones no generan avisos de bloque inválido; SSR coincide con frontend; edición de una página nueva sin ID guardado.
- Axe y revisión manual de contraste/semántica; luego Lighthouse en un sitio real con contenido representativo.
- Revisar LCP/CLS/INP, srcset/sizes, eager de la imagen principal y requests condicionales.

No se incluye un accordion propietario: FAQ usa `core/details`. No se incluye slider, tabs ni un bloque distinto por industria. Desde 1.0.2 hay mega menús nativos de enlaces en columnas. Formularios y lógica comercial pertenecen a plugins.
