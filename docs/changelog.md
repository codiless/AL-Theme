# Cambios

## 1.0.3

- Margen lateral adaptable de 16–24 px en theme.json, aplicado una sola vez.
- Anchos limitados a contenedores PHP y bloques raíz, sin afectar bloques anidados.
- Contenido raíz con layout flow y valores base de baja especificidad para respetar margin/padding del autor.
- Corregidas referencias a variables H1–H6 y al gap 2XL de Content Listing.
- Surface con padding sustituible; columnas/sidebar con min-width cero; código y tablas con scroll local.
- Verificación responsive de bloques nativos y espacios personalizados a 320, 390, 768 y 1280 px.

## 1.0.2

- Navegación visible en escritorio y panel desplegable en móvil, sustituyendo `details` en la cabecera.
- Walker nativo con botones de submenú, estado ARIA, cierre con Escape y soporte de teclado/touch.
- Mega menús de enlaces en columnas mediante la clase `al-mega-menu` del menú nativo; disposición apilada en móvil.
- Filtros `al_primary_menu_args` y `al_header_search_enabled` para integraciones.
- Fallback con Inicio y páginas publicadas si aún no se ha asignado menú.
- Módulo vanilla de navegación cargado por el componente de cabecera.

## 1.0.1

Corrige la carga de estilos del frontend dentro de Gutenberg, que podía aumentar la altura y el padding de los botones de su barra de herramientas.

- Hoja específica de contenido del editor, registrada con `add_editor_style()`.
- Eliminada la dependencia de `al-base` desde el CSS compartido de Content Listing.
- Eliminada la carga duplicada de CSS de listados en el administrador.
- Estilos de texto accesible de los listados limitados al componente.
- Pruebas que ejercitan la carga de assets y su aislamiento entre editor y frontend.
- Assets reconstruidos, con manifest y hashes nuevos para los archivos modificados.

## 1.0.0

Base híbrida inicial con sistema universal de consultas, tarjetas, patrones, bindings, traducciones y compilación Vite.
