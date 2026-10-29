import { execSync } from 'child_process';
import { readdirSync, statSync } from 'fs';
import { join } from 'path';

console.log('[Lint] Scanning for prohibited TypeScript files...');

function findFiles(dir, extensions) {
  let results = [];
  try {
    const list = readdirSync(dir);
    for (const file of list) {
      const fullPath = join(dir, file);
      const stat = statSync(fullPath);
      if (stat && stat.isDirectory()) {
        results = results.concat(findFiles(fullPath, extensions));
      } else {
        for (const ext of extensions) {
          if (file.endsWith(ext)) {
            results.push(fullPath);
          }
        }
      }
    }
  } catch (e) {
    // ignore inaccessible dirs
  }
  return results;
}

const tsFiles = findFiles('.', ['.ts', '.tsx']);
if (tsFiles.length > 0) {
  console.error('[Lint Failed] Prohibited TypeScript files found:');
  tsFiles.forEach(f => console.error('  - ' + f));
  process.exit(1);
}
console.log(`[Lint Passed] Verified 0 .ts and 0 .tsx files.`);

console.log('[Lint] Checking PHP syntax in all PHP files...');
const phpFiles = findFiles('.', ['.php']);
let phpErrors = 0;
for (const f of phpFiles) {
  try {
    execSync(`php -l "${f}"`, { stdio: 'pipe' });
  } catch (e) {
    console.error(`[Lint Error] PHP syntax error in ${f}:`, e.stderr ? e.stderr.toString() : e.message);
    phpErrors++;
  }
}

if (phpErrors > 0) {
  console.error(`[Lint Failed] ${phpErrors} PHP syntax errors found.`);
  process.exit(1);
}

console.log(`[Lint Passed] Checked ${phpFiles.length} PHP files. All syntax valid.`);
