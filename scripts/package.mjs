import { readdirSync, readFileSync, statSync, mkdirSync, writeFileSync } from 'node:fs';
import { deflateRawSync } from 'node:zlib';
const exclude = new Set(['node_modules', '.npm-cache', '.git', '.agents', '.codex', 'release', '.DS_Store', 'Thumbs.db']);
const walk = (dir = '.') => readdirSync(dir).flatMap((name) => {
  if (exclude.has(name) || name.endsWith('.log')) return [];
  const path = `${dir}/${name}`;
  return statSync(path).isDirectory() ? walk(path) : [path];
});
const table = Array.from({ length: 256 }, (_, n) => {
  let c = n;
  for (let i = 0; i < 8; i++) c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1;
  return c >>> 0;
});
const crc32 = (buffer) => {
  let c = 0xffffffff;
  for (const byte of buffer) c = table[(c ^ byte) & 0xff] ^ (c >>> 8);
  return (c ^ 0xffffffff) >>> 0;
};
const parts = [], directory = [];
let offset = 0;
for (const file of walk()) {
  const name = Buffer.from(`al-base/${file.slice(2)}`);
  const data = readFileSync(file), compressed = deflateRawSync(data), crc = crc32(data);
  const local = Buffer.alloc(30);
  local.writeUInt32LE(0x04034b50); local.writeUInt16LE(20, 4); local.writeUInt16LE(0x800, 6); local.writeUInt16LE(8, 8);
  local.writeUInt16LE(0x21, 12); local.writeUInt32LE(crc, 14); local.writeUInt32LE(compressed.length, 18); local.writeUInt32LE(data.length, 22); local.writeUInt16LE(name.length, 26);
  parts.push(local, name, compressed);
  const central = Buffer.alloc(46);
  central.writeUInt32LE(0x02014b50); central.writeUInt16LE(20, 4); central.writeUInt16LE(20, 6); central.writeUInt16LE(0x800, 8); central.writeUInt16LE(8, 10);
  central.writeUInt16LE(0x21, 14); central.writeUInt32LE(crc, 16); central.writeUInt32LE(compressed.length, 20); central.writeUInt32LE(data.length, 24); central.writeUInt16LE(name.length, 28); central.writeUInt32LE(offset, 42);
  directory.push(central, name);
  offset += local.length + name.length + compressed.length;
}
const central = Buffer.concat(directory), end = Buffer.alloc(22);
end.writeUInt32LE(0x06054b50); end.writeUInt16LE(directory.length / 2, 8); end.writeUInt16LE(directory.length / 2, 10); end.writeUInt32LE(central.length, 12); end.writeUInt32LE(offset, 16);
mkdirSync('release', { recursive: true });
writeFileSync('release/al-base.zip', Buffer.concat([...parts, central, end]));
console.log(`release/al-base.zip: ${directory.length / 2} files, ${(offset + central.length + end.length) / 1024 | 0} KB.`);
