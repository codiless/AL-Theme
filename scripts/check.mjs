import { readFileSync, readdirSync, statSync } from 'node:fs';
import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
const files = (dir) => readdirSync(dir).flatMap((name) => {
  const path = `${dir}/${name}`;
  if (['node_modules', '.npm-cache', '.git', 'dist', 'release'].includes(name)) return [];
  return statSync(path).isDirectory() ? files(path) : [path];
});
const all = files('.');
for (const file of all.filter((f) => f.endsWith('.json'))) JSON.parse(readFileSync(file, 'utf8'));
for (const file of all.filter((f) => f.endsWith('.js') || f.endsWith('.mjs'))) {
  const result = spawnSync(process.execPath, ['--check', file], { encoding: 'utf8' });
  assert.equal(result.status, 0, result.stderr);
}
const block = JSON.parse(readFileSync('blocks/content-listing/block.json', 'utf8'));
assert.equal(block.apiVersion, 3);
assert.ok(block.usesContext.includes('postId'));
const manifest = JSON.parse(readFileSync('assets/dist/manifest.json', 'utf8'));
for (const entry of ['assets/src/css/app.css', 'assets/src/css/editor.css', 'assets/src/css/listing.css', 'assets/src/js/editor.js', 'assets/src/js/load-more.js', 'assets/src/js/navigation.js']) {
  assert.ok(manifest[entry]?.file, `Missing build entry: ${entry}`);
  assert.ok(statSync(`assets/dist/${manifest[entry].file}`).isFile());
}
const php = process.env.AL_PHP || 'php';
const probe = spawnSync(php, ['-v'], { encoding: 'utf8' });
if (probe.error) {
  console.warn('PHP lint not run: set AL_PHP to a PHP 8.1+ executable.');
} else {
  for (const file of all.filter((f) => f.endsWith('.php'))) {
    const result = spawnSync(php, ['-l', file], { encoding: 'utf8' });
    assert.equal(result.status, 0, `${file}: ${result.stdout}${result.stderr}`);
  }
  console.log('PHP syntax verified.');
  const query = spawnSync(php, ['tests/query-engine.php'], { encoding: 'utf8' });
  assert.equal(query.status, 0, `${query.stdout}${query.stderr}`);
  console.log(query.stdout.trim());
  const editor = spawnSync(php, ['tests/editor-assets.php'], { encoding: 'utf8' });
  assert.equal(editor.status, 0, `${editor.stdout}${editor.stderr}`);
  console.log(editor.stdout.trim());
  if (process.env.AL_WP_ROOT) {
    const native = spawnSync(php, ['tests/native-api.php'], { encoding: 'utf8' });
    assert.equal(native.status, 0, `${native.stdout}${native.stderr}`);
    console.log(native.stdout.trim());
  }
}
console.log('JSON, JavaScript and production manifest verified.');
