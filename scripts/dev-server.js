import http from 'http';
import { spawn, execSync } from 'child_process';
import { existsSync } from 'fs';

let targetPort = 3000;
let targetHost = '0.0.0.0';

// Parse CLI args like --port 3000 --host 0.0.0.0
const args = process.argv.slice(2);
for (let i = 0; i < args.length; i++) {
  if (args[i] === '--port' && args[i + 1]) {
    targetPort = parseInt(args[i + 1], 10);
    i++;
  } else if (args[i].startsWith('--port=')) {
    targetPort = parseInt(args[i].split('=')[1], 10);
  } else if (args[i] === '--host' && args[i + 1]) {
    targetHost = args[i + 1];
    i++;
  } else if (args[i].startsWith('--host=')) {
    targetHost = args[i].split('=')[1];
  }
}

console.log(`[DevServer] Initializing N.A Fresh Fruits & Coconuts Dev Environment on ${targetHost}:${targetPort}...`);

// 1. Ensure MariaDB is running
try {
  execSync('service mariadb status', { stdio: 'pipe' });
  console.log('[DevServer] MariaDB is already running.');
} catch (e) {
  console.log('[DevServer] Starting MariaDB service...');
  try {
    execSync('service mariadb start', { stdio: 'inherit' });
  } catch (err) {
    console.error('[DevServer] Warning starting MariaDB:', err.message);
  }
}

// 2. Ensure Database and migrations
try {
  if (existsSync('scripts/migrate.php')) {
    console.log('[DevServer] Running migrations...');
    execSync('php scripts/migrate.php', { stdio: 'inherit' });
  }
  if (existsSync('scripts/seed.php')) {
    console.log('[DevServer] Running seeders...');
    execSync('php scripts/seed.php', { stdio: 'inherit' });
  }
} catch (err) {
  console.error('[DevServer] Migration/Seed notice:', err.message);
}

// 3. Compile Tailwind CSS if needed
try {
  if (!existsSync('public/assets/css/app.css')) {
    console.log('[DevServer] Compiling Tailwind CSS...');
    execSync('node scripts/build-assets.js', { stdio: 'inherit' });
  }
} catch (err) {
  console.error('[DevServer] CSS compile notice:', err.message);
}

// 4. Start PHP Built-in Server on internal port 8088
const PHP_PORT = 8088;
console.log(`[DevServer] Spawning PHP 8.2 built-in server on 127.0.0.1:${PHP_PORT}...`);

const phpProcess = spawn('php', ['-S', `127.0.0.1:${PHP_PORT}`, '-t', 'public', 'public/index.php'], {
  stdio: 'inherit'
});

phpProcess.on('error', (err) => {
  console.error('[DevServer] Failed to start PHP server:', err);
});

phpProcess.on('exit', (code, signal) => {
  console.log(`[DevServer] PHP process exited with code ${code} and signal ${signal}`);
});

process.on('SIGINT', () => {
  phpProcess.kill('SIGINT');
  process.exit(0);
});

process.on('SIGTERM', () => {
  phpProcess.kill('SIGTERM');
  process.exit(0);
});

// 5. Proxy HTTP requests from public port to PHP internal server
const proxyServer = http.createServer((clientReq, clientRes) => {
  const options = {
    hostname: '127.0.0.1',
    port: PHP_PORT,
    path: clientReq.url,
    method: clientReq.method,
    headers: {
      ...clientReq.headers,
      'x-forwarded-host': clientReq.headers.host || `localhost:${targetPort}`,
      'x-forwarded-proto': clientReq.headers['x-forwarded-proto'] || 'http',
      'x-forwarded-for': clientReq.socket.remoteAddress || '127.0.0.1'
    }
  };

  const proxyReq = http.request(options, (proxyRes) => {
    clientRes.writeHead(proxyRes.statusCode, proxyRes.headers);
    proxyRes.pipe(clientRes, { end: true });
  });

  proxyReq.on('error', (err) => {
    clientRes.writeHead(502, { 'Content-Type': 'text/html; charset=utf-8' });
    clientRes.end(`
      <div style="font-family:sans-serif;padding:40px;text-align:center;">
        <h2 style="color:#16a34a;">N.A Fresh Fruits & Coconuts</h2>
        <p>Connecting to PHP server... please refresh in a moment.</p>
        <small style="color:#64748b;">${err.message}</small>
      </div>
    `);
  });

  clientReq.pipe(proxyReq, { end: true });
});

proxyServer.listen(targetPort, targetHost, () => {
  console.log(`[DevServer] Ready! Application listening on http://${targetHost}:${targetPort}`);
});
