# Desarrollo y mantenimiento — AL Base 1.0.3

## Entorno y primer recorrido

Instalación: WordPress 6.6+ y PHP 8.1+. Desarrollo de assets: Node 20.19+ y npm. El theme distribuido incluye assets/dist y funciona sin Node en producción.

Antes de tocar código, lee [arquitectura](architecture.md), identifica si el cambio pertenece al padre, al child o al plugin de proyecto, y prepara una instalación de pruebas. No utilices los IDs de Beta AL como configuración universal.

```sh
npm ci
npm run dev
```

`npm ci` instala versiones del lockfile. `dev` ejecuta Vite build --watch: genera archivos en assets/dist, sin servidor de desarrollo ni HMR. WordPress sigue sirviendo PHP y assets. Finaliza el watch antes de producir el paquete de entrega.

## Elegir dónde editar

| Cambio | Archivo/capa recomendada |
| --- | --- |
| Paleta, escala, anchos o gutter | theme.json del child; padre solo si beneficia a la base. |
| Sección editable predefinida | Patrón Gutenberg del proyecto. |
| Tarjeta de industria | Registro de variante + template del child. |
| Datos y lógica comercial | Plugin de proyecto. |
| Cabecera o pie del proyecto | Template part del child. |
| Bug general de consulta | inc/query.php del padre, con contrato relevante. |
| Bug de controles Gutenberg | Revisar carga de assets y CSS del editor antes de tocar estilos frontend. |

No edites assets/dist a mano. El siguiente build lo reemplaza. `al_asset_url()` resuelve assets del padre; los assets del child se encolan por separado.

## Compilar y empaquetar

```sh
npm run build
npm run check
npm run package
```

Build minifica las seis entradas y actualiza `assets/dist/manifest.json`. Check revisa JSON, sintaxis JavaScript, entradas del manifest y PHP si hay un ejecutable disponible. Package escribe `release/al-base.zip` con carpeta raíz `al-base`.

El ZIP excluye node_modules, caché npm, Git, carpetas internas de agentes y release. Por eso los backups, capturas y scripts locales de pruebas dentro de release no se distribuyen. Incluye los fuentes, assets compilados, documentación y herramientas de desarrollo. No incluye la base de datos ni las imágenes de la biblioteca Media del sitio.

Si falta el manifest, el cargador intenta los archivos fuente. Esa alternativa no reemplaza la comprobación del paquete: entrega siempre assets/dist con manifest coherente. Si cambias un asset, copia el manifest junto con los archivos que referencia.

## PHP y pruebas nativas

Si PHP no está en PATH, configura `AL_PHP` antes del check. Ejemplo PowerShell, sustituyendo rutas por las de tu equipo:

```powershell
$env:AL_PHP = 'C:\ruta\php.exe'
$env:AL_WP_ROOT = 'C:\ruta\wordpress'
npm run check
```

En una shell POSIX:

```sh
AL_PHP=/ruta/php AL_WP_ROOT=/ruta/wordpress npm run check
```

AL_WP_ROOT permite usar el parser de bloques y el lector MO de esa instalación. Estas pruebas **no cargan wp-config.php ni prueban consultas reales de su base de datos**.

| Verificación | Alcance |
| --- | --- |
| PHP lint | Sintaxis de archivos PHP; no garantiza ejecución correcta de todas las ramas. |
| Query engine contracts | 15 casos con dobles de consultas; normalización, offset, relaciones y exclusiones. |
| Editor assets | Registro/carga y ausencia de dependencia del frontend en hojas compartidas/editor. |
| Native API | Parser real de 12 patrones y lectura de traducciones singular/plural. |
| Browser/WordPress | Se ejecuta por separado para layout, interacción y datos reales. |

Si el check informa que no encuentra PHP, no interpretes su salida como un lint PHP completado. Consulta [validation.md](validation.md) para la evidencia de la versión y los pendientes.

## Traducciones

Las cadenas de PHP/JS utilizan el text domain al-base. El catálogo español editable está en `languages/es.json`.

```sh
npm run pot
npm run translations
npm run build
```

Pot extrae el catálogo; translations genera archivos PO/MO y JSON para es_ES/es_PE. Build reconstruye el JS si cambió. No traduzcas a mano archivos compilados. Un child y un plugin deben utilizar sus propios text domains y catálogos.

Las traducciones del theme no traducen entradas, menús ni campos. WPML/Polylang gestionan contenido según su configuración. Revisa el mapeo de IDs manuales por idioma y las consultas de relaciones en el proyecto real.

## Revisión visual y funcional

Para cambios de layout, prueba al menos 320, 390, 768 y 1280 px; además revisa anchos cercanos a los breakpoints afectados. El menú móvil utiliza un corte de 899 px; sidebar cambia a dos columnas desde 1000 px; los listados avanzados utilizan container queries.

1. Portada, página normal, canvas, sidebar, entrada, archivo, búsqueda con/sin resultados y 404.
2. Grupos normal/wide/full, grupo anidado, columnas, imagen, tabla y código largo.
3. Padding/margin explícitos, presets fluidos y Surface con padding sustituido.
4. Menú cerrado/abierto, submenú, mega menú, Escape, foco y navegación sin JavaScript.
5. Listado vacío, selección manual vacía, exclusiones, relaciones sin datos, offset y última página.
6. Dos listados paginados en una página, IDs distintos y Load more con respuesta correcta y con fallo.
7. ACF ausente, plugin SEO activo, idioma del proyecto y CPT/taxonomías reales.

La ausencia de overflow se comprueba comparando scrollWidth con clientWidth de documentElement; innerWidth puede incluir la barra de scroll. Tablas/código/listados horizontales pueden tener scroll local sin que lo tenga toda la página. No ocultes overflow globalmente para disimular un problema de ancho.

El navegador usado en la revisión local no mostraba el documento blob del canvas Gutenberg. Por tanto, las pruebas del DOM de assets no equivalen a validar visualmente todos los bloques dentro del editor. Completa esa revisión en el navegador habitual antes de entregar una especialidad.

## Actualizar una instalación

1. Guarda una copia del theme instalado. Para una entrega real conserva también backup de archivos y base de datos del sitio mediante su procedimiento habitual.
2. Produce un build y ejecuta los checks relevantes.
3. Actualiza versión de style.css y package.json/lockfile; ajusta versiones de assets cuando cambien. La versión del bloque puede ser independiente de la versión del theme.
4. Actualiza changelog y validation con resultados reales, sin convertir la matriz pendiente en pruebas aprobadas.
5. Genera y revisa el ZIP. Instálalo reemplazando el padre en el entorno de prueba; conserva el child separado.
6. Purga el caché relevante, recarga frontend y editor, y verifica las URLs y flujos afectados.
7. Comprueba que las personalizaciones estén en el child/plugin; las modificaciones directas al padre pueden perderse al reemplazarlo.

Reemplazar archivos del theme no migra CPT, IDs, relaciones ni contenido. Si cambias el contrato de atributos de un bloque existente, diseña y verifica su migración antes de distribuirlo. Mantener el padre anterior permite revertir código; una migración de datos necesita su propio plan de reversión.

## Diagnóstico

| Síntoma | Comprobación y acción |
| --- | --- |
| Barra Gutenberg con botones o espacios extraños | Inspeccionar estilos cargados: app.css debe quedar en frontend. Mantener listing.css aislado y registrar editor.css con add_editor_style. |
| Cambio CSS no aparece | Reconstruir, comprobar manifest, archivos referenciados, versión y caché. No editar solo dist. |
| Títulos pierden escala | Comparar variables emitidas por WordPress y referencias. H1 usa h-1; 2XL usa 2-xl en variables CSS. |
| Gutter repetido en bloques anidados | Buscar reglas globales .alignwide o restricciones añadidas por el child. El gutter base se aplica a shell/bloques raíz. |
| Full tiene texto al borde | Añadir padding al Grupo. Full elimina el límite exterior, no inventa padding interior. |
| Padding excesivo en móvil | Revisar valor fijo y acumulación de padding en padres/hijos; utilizar presets fluidos cuando corresponda. |
| CPT no aparece en inspector | Revisar registro público, exposición REST y permisos del plugin; reconstruir no sustituye ese registro. |
| Listado manual/relacionado vacío | Verificar IDs, tipo, publish, contraseña, exclusiones, contexto y datos reales de relación. |
| Términos vacíos no aparecen | Revisar hideEmpty y taxonomía pública. |
| Orden por campo omite posts | Revisar meta almacenado y registro sortable; callbacks no crean meta para ordenar. |
| Todas las páginas muestran los mismos resultados | Revisar si el caché ignora al-page-* y si los listingId son únicos. |
| Load more deja de mejorar el enlace | Revisar respuesta, timeout, wrapper/data-al-listing y clases conservadas en overrides. El enlace normal es la alternativa. |
| Variante registrada no imprime tarjeta | Verificar archivo components/cards/{clave}.php y registro exacto. |
| Datos ACF desaparecen al desactivarlo | Verificar clave y valor meta; campos complejos necesitan presentación específica. |
| Breadcrumbs no aparecen en portada | Es el comportamiento previsto de al_breadcrumbs. |

## Límites de la entrega base

La base no certifica Lighthouse, Core Web Vitals, accesibilidad WCAG, compatibilidad de todos los plugins ni rendimiento con grandes volúmenes. No incluye slider, tabs propietarios, constructor visual de mega menú, sistema de reservas, formularios enviados ni schemas de industria. La documentación de una especialidad debe añadir sus requisitos, integraciones y resultados propios.
