// Generate an untranslated catalog; wp i18n make-pot is also supported.
import { readFileSync, readdirSync, statSync, mkdirSync, writeFileSync } from 'node:fs';
const walk = (dir) => readdirSync(dir).flatMap((name) => {
  if (['node_modules', '.git', 'dist', 'languages'].includes(name)) return [];
  const path = `${dir}/${name}`;
  return statSync(path).isDirectory() ? walk(path) : [path];
});
const strings = new Map();
const add = (value, ref) => {
  const key = `${value}\0`;
  if (!strings.has(key)) strings.set(key, { value, plural: null, refs: [] });
  strings.get(key).refs.push(ref);
};
for (const file of walk('./patterns').filter((f) => f.endsWith('.php'))) {
  const match = readFileSync(file, 'utf8').match(/\* Title: (.+)/);
  if (match) add(match[1].trim(), `${file.slice(2)}:3`);
}
const tokens = JSON.parse(readFileSync('theme.json', 'utf8'));
for (const section of [tokens.settings.color.palette, tokens.settings.typography.fontFamilies, tokens.settings.typography.fontSizes, tokens.settings.spacing.spacingSizes, tokens.settings.shadow.presets]) {
  for (const item of section) add(item.name, 'theme.json');
}
for (const file of walk('.').filter((f) => /\.(php|js)$/.test(f))) {
  const source = readFileSync(file, 'utf8');
  const pattern = /\b(__|_e|esc_html__|esc_attr__|esc_html_e|esc_attr_e|_n)\(\s*'((?:\\.|[^'\\])*)'\s*,\s*'((?:\\.|[^'\\])*)'/g;
  for (const match of source.matchAll(pattern)) {
    const plural = match[1] === '_n';
    if (!plural && match[3] !== 'al-base') continue;
    const value = match[2].replace(/\\'/g, "'").replace(/\\\\/g, '\\');
    const key = `${value}\0${plural ? match[3] : ''}`;
    if (!strings.has(key)) strings.set(key, { value, plural: plural ? match[3] : null, refs: [] });
    strings.get(key).refs.push(`${file.slice(2)}:${source.slice(0, match.index).split('\n').length}`);
  }
}
const q = (s) => JSON.stringify(s);
const header = 'msgid ""\nmsgstr ""\n"Project-Id-Version: AL Base 1.0.0\\n"\n"Content-Type: text/plain; charset=UTF-8\\n"\n"Content-Transfer-Encoding: 8bit\\n"\n"X-Domain: al-base\\n"\n';
let catalog = header;
for (const entry of [...strings.values()].sort((a, b) => a.value.localeCompare(b.value))) {
  catalog += `\n#: ${entry.refs.join(' ')}\n`;
  if (/%(?:\d+\$)?[sd]/.test(entry.value)) catalog += '#, php-format\n';
  catalog += `msgid ${q(entry.value)}\n`;
  catalog += entry.plural ? `msgid_plural ${q(entry.plural)}\nmsgstr[0] ""\nmsgstr[1] ""\n` : 'msgstr ""\n';
}
mkdirSync('languages', { recursive: true });
writeFileSync('languages/al-base.pot', catalog);
console.log(`${strings.size} translation entries generated.`);
