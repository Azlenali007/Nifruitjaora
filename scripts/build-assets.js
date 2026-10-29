import { execSync } from 'child_process';
import { existsSync, mkdirSync } from 'fs';

console.log('[Assets] Building Tailwind CSS bundle...');
if (!existsSync('public/assets/css')) {
  mkdirSync('public/assets/css', { recursive: true });
}

try {
  execSync('tailwindcss -i resources/css/input.css -o public/assets/css/app.css --minify', {
    stdio: 'inherit'
  });
  console.log('[Assets] CSS compiled successfully to public/assets/css/app.css');
} catch (err) {
  console.error('[Assets] Error compiling CSS:', err.message);
  process.exit(1);
}
