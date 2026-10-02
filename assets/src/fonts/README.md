# Fuentes locales

La base usa fuentes del sistema y no realiza solicitudes externas. Para una fuente propia, coloca sus WOFF2 aquí y define `fontFace` dentro de `settings.typography.fontFamilies` en theme.json, con `fontDisplay: "swap"`, `fontWeight: "100 900"` para variables y `src: ["file:./assets/src/fonts/nombre.woff2"]`. Incluye la licencia de la fuente. Precarga únicamente la fuente crítica si las mediciones lo justifican.
