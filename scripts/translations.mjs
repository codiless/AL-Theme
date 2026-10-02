import { readFileSync, writeFileSync } from 'node:fs';
const pot = readFileSync('languages/al-base.pot', 'utf8');
const dictionary = JSON.parse(readFileSync('languages/es.json', 'utf8'));
const entries = [...pot.matchAll(/^msgid (".+")\n(?:msgid_plural (".+")\n)?msgstr(?:\[0\])? ""/gm)].map((match) => ({ id: JSON.parse(match[1]), plural: match[2] ? JSON.parse(match[2]) : null }));
for (const entry of entries) if (!(entry.id in dictionary)) throw new Error(`Missing Spanish translation: ${entry.id}`);
const quote = JSON.stringify;
for (const locale of ['es_ES', 'es_PE']) {
  const header = `Project-Id-Version: AL Base 1.0.0\nLanguage: ${locale}\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nPlural-Forms: nplurals=2; plural=(n != 1);\n`;
  const rows = [{ id: '', value: header }, ...entries.map((entry) => ({ id: entry.id + (entry.plural ? `\0${entry.plural}` : ''), value: Array.isArray(dictionary[entry.id]) ? dictionary[entry.id].join('\0') : dictionary[entry.id] }))].sort((a, b) => Buffer.compare(Buffer.from(a.id), Buffer.from(b.id)));
  let po = `msgid ""\nmsgstr ${quote(header)}\n`;
  const messages = { '': { domain: 'al-base', lang: locale, 'plural-forms': 'nplurals=2; plural=(n != 1);' } };
  for (const entry of entries) {
    const value = dictionary[entry.id];
    po += `\nmsgid ${quote(entry.id)}\n`;
    po += entry.plural ? `msgid_plural ${quote(entry.plural)}\nmsgstr[0] ${quote(value[0])}\nmsgstr[1] ${quote(value[1])}\n` : `msgstr ${quote(value)}\n`;
    messages[entry.id] = Array.isArray(value) ? value : [value];
  }
  const count = rows.length;
  const start = 28 + count * 16;
  let offset = start;
  const originals = rows.map((row) => { const b = Buffer.from(row.id + '\0'); const record = [b.length - 1, offset]; offset += b.length; return { b, record }; });
  const translations = rows.map((row) => { const b = Buffer.from(row.value + '\0'); const record = [b.length - 1, offset]; offset += b.length; return { b, record }; });
  const mo = Buffer.alloc(start);
  [0x950412de, 0, count, 28, 28 + count * 8, 0, start].forEach((value, i) => mo.writeUInt32LE(value, i * 4));
  originals.forEach((row, i) => row.record.forEach((value, j) => mo.writeUInt32LE(value, 28 + i * 8 + j * 4)));
  translations.forEach((row, i) => row.record.forEach((value, j) => mo.writeUInt32LE(value, 28 + count * 8 + i * 8 + j * 4)));
  writeFileSync(`languages/${locale}.po`, po);
  writeFileSync(`languages/${locale}.mo`, Buffer.concat([mo, ...originals.map((row) => row.b), ...translations.map((row) => row.b)]));
  writeFileSync(`languages/al-base-${locale}-al-block-editor.json`, JSON.stringify({ domain: 'al-base', locale_data: { 'al-base': messages } }));
}
console.log(`${entries.length} Spanish translations compiled for PHP and the editor.`);
