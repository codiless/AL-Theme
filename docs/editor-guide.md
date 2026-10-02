# Configuración y edición — AL Base 1.0.3

Este manual describe lo que se puede configurar desde WordPress. Para cambiar código, consulta [arquitectura](architecture.md) y [temas de especialidad](specialty-themes.md).

## 1. Instalar y configurar

1. Instala `release/al-base.zip` desde Apariencia → Temas → Añadir tema → Subir tema, o copia su carpeta `al-base` a `wp-content/themes`.
2. Activa AL Base. El ZIP incluye los assets compilados; no necesitas ejecutar npm en el hosting.
3. Configura nombre del sitio y logo en las opciones de identidad del sitio disponibles en tu instalación.
4. Crea un menú principal y otro de pie, y asígnalos a sus ubicaciones en Apariencia → Menús.
5. En Ajustes → Lectura, decide si la portada muestra las últimas entradas o una página estática. Si utilizas una portada estática, asigna también la página de entradas.
6. Configura enlaces permanentes y comprueba una página, una entrada, una categoría, la búsqueda y una URL inexistente.
7. Selecciona el idioma del sitio. Se incluyen traducciones del theme para español de España y Perú; las fechas y otras cadenas de WordPress dependen de WordPress y sus catálogos.

Los nombres exactos de los controles pueden variar con el idioma y la versión del administrador. No se requieren ACF, un plugin SEO ni WooCommerce para que la base funcione.

## 2. Elegir plantilla de página

| Plantilla | Uso | Título y estructura |
| --- | --- | --- |
| Predeterminada | Páginas de contenido | Imprime el título de página como H1 y el contenido Gutenberg. |
| Gutenberg canvas | Landing pages con Hero propio | Omite el título automático de página; el autor debe incluir el H1 en el contenido. Conserva la estructura del sitio definida por el theme. |
| Sidebar | Contenido con widgets laterales | Añade la zona Sidebar; en pantallas estrechas la distribución se apila. |

La página asignada como página de entradas utiliza el archivo de entradas del theme. Su contenido Gutenberg no sustituye automáticamente ese listado.

El menú del pie muestra solo el primer nivel. Si necesitas una estructura de pie con varias columnas o niveles, debe definirse en el child theme.

No añadas un Hero con H1 a la plantilla predeterminada sin revisar el encabezado automático. El nivel de título de una tarjeta se puede configurar entre H2 y H6 según la jerarquía de la página.

## 3. Construir una página con bloques

Utiliza Grupo para organizar secciones, Columnas para distribuciones y los bloques nativos para texto, imágenes, botones, consultas y detalles. En la categoría AL Base hay 12 patrones:

| Archivo del patrón | Finalidad |
| --- | --- |
| `hero.php` | Apertura de página con título y contenido. |
| `hero-breadcrumbs.php` | Apertura con bloque de breadcrumbs. |
| `latest-content.php` | Listado nativo de contenido reciente. |
| `featured-content.php` | Consulta nativa de contenido destacado. |
| `related-content.php` | Composición para contenido relacionado. |
| `category-grid.php` | Listado de términos mediante Content Listing. |
| `content-sidebar.php` | Composición de contenido en columnas. |
| `cta.php` | Llamada a la acción. |
| `footer-cta.php` | Llamada a la acción para cierre de contenido. |
| `faq.php` | Preguntas con bloques Details nativos. |
| `contact.php` | Composición de contacto que se adapta al proyecto. |
| `logos.php` | Composición para logos. |

Un patrón inserta bloques editables: no crea por sí solo formularios funcionales, entidades de negocio o servicios externos. Sustituye textos, enlaces e imágenes de ejemplo. El patrón de columnas no registra widgets; la plantilla Sidebar sí utiliza la zona de widgets del theme.

## 4. Anchos, padding y márgenes

El contenido de lectura tiene un máximo de 740 px; el ancho amplio tiene un máximo de 1240 px. En móvil se mantiene un margen lateral base de 16 px, que aumenta hasta 24 px cuando hay espacio. Son valores de la base, modificables por un developer mediante `theme.json`.

- **Normal:** contenido limitado al ancho de lectura.
- **Wide / amplio:** sección de primer nivel limitada al ancho amplio y con margen lateral.
- **Full / completo:** sección de primer nivel que llega a los bordes del área de contenido. Añade padding al grupo si contiene texto.

Dentro de otro Grupo, los bloques se rigen por el layout del Grupo. Un bloque full dentro de un contenedor limitado no atraviesa automáticamente ese contenedor hasta el borde del viewport.

**Padding** es el espacio dentro del bloque, entre su borde/fondo y sus hijos. **Margin** es el espacio fuera del bloque. **Block gap** separa los hijos de un layout. No son intercambiables: si quieres aire dentro de una sección con fondo, utiliza padding.

| Preset | Valor de la base |
| --- | --- |
| XS | 0.5 rem |
| S | 1 rem |
| M | 1.5 rem |
| L | `clamp(2rem, 4vw, 3rem)` |
| XL | `clamp(3rem, 6vw, 5rem)` |
| 2XL | `clamp(4rem, 8vw, 7rem)` |

Los presets L, XL y 2XL se adaptan al viewport. Un padding escrito como `80px` continúa siendo 80 px en móvil: el theme conserva esa decisión. Los controles nativos no constituyen un editor de valores separados para cada breakpoint. Si necesitas una regla específica para móvil, el developer debe añadirla a la extensión del proyecto.

El estilo Surface de Grupo aporta fondo, radio y padding por defecto. Puedes sustituir su padding en Gutenberg. Antes de aumentar espacios, comprueba si ya existe padding en el Grupo padre: acumularlo en todos sus hijos puede dejar muy poco ancho en móvil.

## 5. Elegir el sistema de listado

Usa **Query Loop** o **AL Latest content** cuando los bloques nativos resuelvan la consulta. Sus tarjetas se componen con bloques editables de título, imagen, extracto, etc.

Usa **Content Listing** cuando necesites hijos, hermanos, relaciones, selección manual, términos o las variantes PHP de tarjeta. Su tarjeta se renderiza en el servidor: no es una colección de InnerBlocks que puedas editar individualmente dentro del canvas.

### Ejemplos de configuración

| Objetivo | Configuración de Content Listing |
| --- | --- |
| Últimas seis entradas | Source Posts/pages/CPT; Post type post; Date DESC; 6 elementos. |
| Páginas hijas actuales | Source Children; Post type page; Parent ID 0; Menu order ASC. |
| Páginas hermanas | Source Siblings; Post type page; utiliza el padre de la página actual y excluye esa página. |
| Entradas de una categoría | Source Posts/pages/CPT; Post type post; Taxonomy category; IDs de términos de esa instalación. |
| Selección editorial | Source Manual; IDs en el orden deseado; tipo de contenido correspondiente. |
| Categorías | Source Taxonomy terms; Taxonomy category; decidir si se ocultan términos vacíos. |
| Contenido relacionado | Source Related by taxonomy; taxonomía compartida; necesita contenido actual con términos. |
| Relación del proyecto | Source Related by public field; campo de relación previamente registrado por un developer. |

Los campos de IDs reciben números separados por comas. No reciben slugs ni URLs. Los IDs de una instalación pueden ser distintos a los de otra. Los selectores del editor muestran los tipos/taxonomías accesibles mediante las APIs de WordPress.

### Presentación y datos

Configura Layout y Card variant por separado. Grid distribuye tarjetas; List y Compact usan una columna; Horizontal tiene desplazamiento local; Featured combina un primer elemento destacado con una cuadrícula. El número de columnas responde al ancho del contenedor del listado, no solo al ancho de la pantalla.

Activa los elementos de tarjeta que necesites: imagen, categoría, título, extracto, autor, fecha, tiempo de lectura, campos, badge y enlace. Usa las flechas para subir elementos en el orden. Activar Fields requiere además seleccionar campos públicos del registro. Activar Badge requiere elegir un campo para su valor.

En términos no existen imagen destacada, autor o fecha de entrada: esos elementos se omiten. La tarjeta de términos utiliza nombre, descripción y enlace del término.

### Paginación

None muestra el límite configurado. Page links permite navegar entre páginas del listado. Load more añade resultados al listado con JavaScript y conserva un enlace real como alternativa.

Cada bloque necesita un **Stable listing ID** único. El editor genera uno y corrige duplicados al duplicar bloques; si introduces contenido mediante código, debes garantizar la unicidad. Cambiar ese ID cambia el parámetro de URL utilizado para paginar.

Offset omite elementos desde el inicio de la consulta; no representa el número de página. El orden aleatorio con paginación se sustituye por fecha para evitar una consulta aleatoria distinta en cada solicitud.

## 6. Menús normales y mega menús

Asigna un menú a Primary navigation. Los elementos indentados son submenús. En escritorio se muestran los enlaces; en móvil el botón Menu abre el panel. Los botones de submenú son independientes del enlace del padre. Escape permite cerrar y recuperar el foco; sin JavaScript los enlaces siguen disponibles.

Para un mega menú, activa Clases CSS en Opciones de pantalla de Apariencia → Menús y añade `al-mega-menu` al padre de primer nivel:

```text
Servicios [clase: al-mega-menu]
├── Consultoría
│   ├── Estrategia
│   └── Auditoría
└── Desarrollo
    ├── Websites
    └── Integraciones
```

Los hijos directos forman columnas y los nietos son enlaces. En móvil se apilan. Cada elemento sigue siendo un enlace normal de WordPress; configura su destino. La base no incorpora un constructor visual de banners o widgets dentro del mega menú.

## 7. Revisión antes de entregar contenido

Comprueba escritorio y móvil, menú abierto, títulos largos, enlaces reales, alt de imágenes informativas, ausencia de H1 duplicado, columnas apiladas y padding dentro de fondos completos. Revisa listados vacíos, última página y resultados de búsqueda.

Si el canvas del editor difiere del frontend, identifica primero si la diferencia proviene de contenido, layout, estilos del proyecto o caché. Consulta el [manual de mantenimiento](development.md); no cargues la hoja global del frontend en el administrador para intentar resolverlo.
