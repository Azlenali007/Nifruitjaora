<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;
use PDOException;
use Exception;

class InstallController
{
    private string $lockFile;
    private string $envFile;

    public function __construct()
    {
        $this->lockFile = dirname(__DIR__, 2) . '/storage/installed.lock';
        $this->envFile = dirname(__DIR__, 2) . '/.env';
    }

    public function isInstalled(): bool
    {
        return file_exists($this->lockFile);
    }

    public function index(): void
    {
        if ($this->isInstalled()) {
            $this->render('locked', [
                'title' => 'Installer Locked | N.A Fresh Fruits & Coconuts',
                'lockDetails' => $this->getLockDetails(),
            ]);
            return;
        }

        $step = max(1, min(6, (int)($_GET['step'] ?? 1)));
        
        switch ($step) {
            case 1:
                $this->step1Requirements();
                break;
            case 2:
                $this->step2Database();
                break;
            case 3:
                $this->step3Website();
                break;
            case 4:
                $this->step4Admin();
                break;
            case 5:
                $this->step5RunInstall();
                break;
            case 6:
                $this->step6Complete();
                break;
            default:
                redirect('/install?step=1');
        }
    }

    // Step 1: Requirements Check
    private function step1Requirements(): void
    {
        $checks = [
            'php_version' => [
                'name' => 'PHP Version 8.2+',
                'required' => '>= 8.2.0',
                'current' => PHP_VERSION,
                'pass' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'help' => 'Upgrade your PHP version to 8.2 or higher.'
            ],
            'ext_pdo' => [
                'name' => 'PDO Extension',
                'required' => 'Required',
                'current' => extension_loaded('pdo') ? 'Enabled' : 'Missing',
                'pass' => extension_loaded('pdo'),
                'help' => 'Enable PDO in php.ini.'
            ],
            'ext_pdo_mysql' => [
                'name' => 'PDO MySQL Driver',
                'required' => 'Required',
                'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Missing',
                'pass' => extension_loaded('pdo_mysql'),
                'help' => 'Install php-mysql extension.'
            ],
            'ext_mbstring' => [
                'name' => 'Mbstring Extension',
                'required' => 'Required',
                'current' => extension_loaded('mbstring') ? 'Enabled' : 'Missing',
                'pass' => extension_loaded('mbstring'),
                'help' => 'Enable mbstring for UTF-8 string handling.'
            ],
            'ext_curl' => [
                'name' => 'cURL Extension',
                'required' => 'Required',
                'current' => extension_loaded('curl') ? 'Enabled' : 'Missing',
                'pass' => extension_loaded('curl'),
                'help' => 'Required for Razorpay gateway communication.'
            ],
            'ext_json' => [
                'name' => 'JSON Extension',
                'required' => 'Required',
                'current' => extension_loaded('json') ? 'Enabled' : 'Missing',
                'pass' => extension_loaded('json'),
                'help' => 'Enable JSON support.'
            ],
            'ext_openssl' => [
                'name' => 'OpenSSL Extension',
                'required' => 'Required',
                'current' => extension_loaded('openssl') ? 'Enabled' : 'Missing',
                'pass' => extension_loaded('openssl'),
                'help' => 'Required for cryptographic HMAC-SHA256 signature verification.'
            ],
            'ext_xml' => [
                'name' => 'XML Extension',
                'required' => 'Required',
                'current' => extension_loaded('xml') ? 'Enabled' : 'Missing',
                'pass' => extension_loaded('xml'),
                'help' => 'Enable php-xml.'
            ],
            'dir_storage' => [
                'name' => 'storage/ Directory Writable',
                'required' => 'Writable',
                'current' => is_writable(dirname(__DIR__, 2) . '/storage') ? 'Writable' : 'Not Writable',
                'pass' => is_writable(dirname(__DIR__, 2) . '/storage'),
                'help' => 'Run: chmod 775 storage'
            ],
            'dir_uploads' => [
                'name' => 'public/assets/ Uploads Writable',
                'required' => 'Writable',
                'current' => is_writable(dirname(__DIR__, 2) . '/public/assets') ? 'Writable' : 'Not Writable',
                'pass' => is_writable(dirname(__DIR__, 2) . '/public/assets'),
                'help' => 'Ensure public/assets is writable for product images.'
            ],
            'file_env' => [
                'name' => '.env Configuration Writable',
                'required' => 'Writable',
                'current' => (file_exists($this->envFile) ? (is_writable($this->envFile) ? 'Writable' : 'Read-only') : (is_writable(dirname($this->envFile)) ? 'Writable (can create)' : 'Cannot create')),
                'pass' => file_exists($this->envFile) ? is_writable($this->envFile) : is_writable(dirname($this->envFile)),
                'help' => 'Ensure .env or the project root is writable.'
            ]
        ];

        $allPass = true;
        foreach ($checks as $c) {
            if (!$c['pass']) {
                $allPass = false;
                break;
            }
        }

        $this->render('step1_requirements', [
            'title' => 'Step 1: System Requirements | Installer',
            'step' => 1,
            'checks' => $checks,
            'allPass' => $allPass,
        ]);
    }

    // Step 2: Database Configuration
    private function step2Database(): void
    {
        $error = null;
        $success = null;

        $dbHost = $_POST['db_host'] ?? ($_SESSION['install_db']['host'] ?? env('DB_HOST', '127.0.0.1'));
        $dbPort = $_POST['db_port'] ?? ($_SESSION['install_db']['port'] ?? env('DB_PORT', '3306'));
        $dbName = $_POST['db_name'] ?? ($_SESSION['install_db']['name'] ?? env('DB_DATABASE', 'na_fresh_fruits'));
        $dbUser = $_POST['db_user'] ?? ($_SESSION['install_db']['user'] ?? env('DB_USERNAME', 'na_user'));
        $dbPass = $_POST['db_pass'] ?? ($_SESSION['install_db']['pass'] ?? env('DB_PASSWORD', ''));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify($_POST['csrf_token'] ?? '')) {
                $error = 'Security session expired. Please refresh the page and try again.';
            } else {
                try {
                    // Test connection to MySQL server
                    $dsnWithoutDb = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
                    $pdo = new PDO($dsnWithoutDb, $dbUser, $dbPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => 5,
                    ]);

                    // Create database if not exists
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                    // Select DB
                    $pdo->exec("USE `{$dbName}`");

                    // Store credentials in session for installation step
                    $_SESSION['install_db'] = [
                        'host' => $dbHost,
                        'port' => $dbPort,
                        'name' => $dbName,
                        'user' => $dbUser,
                        'pass' => $dbPass,
                    ];

                    redirect('/install?step=3');
                    return;
                } catch (PDOException $e) {
                    $error = 'Database Connection Failed: ' . $this->sanitizeDbError($e->getMessage());
                }
            }
        }

        $this->render('step2_database', [
            'title' => 'Step 2: Database Setup | Installer',
            'step' => 2,
            'dbHost' => $dbHost,
            'dbPort' => $dbPort,
            'dbName' => $dbName,
            'dbUser' => $dbUser,
            'error' => $error,
            'success' => $success,
        ]);
    }

    // Step 3: Website Configuration
    private function step3Website(): void
    {
        if (empty($_SESSION['install_db'])) {
            redirect('/install?step=2');
            return;
        }

        $defaultUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:3000');
        $appName = $_POST['app_name'] ?? ($_SESSION['install_app']['name'] ?? 'N.A Fresh Fruits & Coconuts');
        $appUrl = $_POST['app_url'] ?? ($_SESSION['install_app']['url'] ?? $defaultUrl);
        $appTimezone = $_POST['app_timezone'] ?? ($_SESSION['install_app']['timezone'] ?? 'Asia/Kolkata');
        $appEnv = $_POST['app_env'] ?? ($_SESSION['install_app']['env'] ?? 'development');
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify($_POST['csrf_token'] ?? '')) {
                $error = 'Security token invalid. Please try again.';
            } elseif (empty(trim($appName))) {
                $error = 'Website name is required.';
            } elseif (empty(trim($appUrl)) || !filter_var($appUrl, FILTER_VALIDATE_URL)) {
                $error = 'Please provide a valid website URL (including http:// or https://).';
            } else {
                $_SESSION['install_app'] = [
                    'name' => trim($appName),
                    'url' => rtrim(trim($appUrl), '/'),
                    'timezone' => $appTimezone,
                    'env' => $appEnv,
                    'debug' => ($appEnv === 'development'),
                ];
                redirect('/install?step=4');
                return;
            }
        }

        $this->render('step3_website', [
            'title' => 'Step 3: Store Configuration | Installer',
            'step' => 3,
            'appName' => $appName,
            'appUrl' => $appUrl,
            'appTimezone' => $appTimezone,
            'appEnv' => $appEnv,
            'error' => $error,
        ]);
    }

    // Step 4: Administrator Creation
    private function step4Admin(): void
    {
        if (empty($_SESSION['install_db']) || empty($_SESSION['install_app'])) {
            redirect('/install?step=2');
            return;
        }

        $adminName = $_POST['admin_name'] ?? ($_SESSION['install_admin']['name'] ?? 'Store Administrator');
        $adminEmail = $_POST['admin_email'] ?? ($_SESSION['install_admin']['email'] ?? '');
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminPass = $_POST['admin_password'] ?? '';
            $confirmPass = $_POST['confirm_password'] ?? '';

            if (!csrf_verify($_POST['csrf_token'] ?? '')) {
                $error = 'Security session expired. Please try again.';
            } elseif (empty(trim($adminName))) {
                $error = 'Administrator name is required.';
            } elseif (empty(trim($adminEmail)) || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid administrator email address.';
            } elseif (strlen($adminPass) < 8) {
                $error = 'Password must be at least 8 characters long for security.';
            } elseif ($adminPass !== $confirmPass) {
                $error = 'Passwords do not match.';
            } else {
                $_SESSION['install_admin'] = [
                    'name' => trim($adminName),
                    'email' => strtolower(trim($adminEmail)),
                    'password' => $adminPass,
                ];
                redirect('/install?step=5');
                return;
            }
        }

        $this->render('step4_admin', [
            'title' => 'Step 4: Create Administrator | Installer',
            'step' => 4,
            'adminName' => $adminName,
            'adminEmail' => $adminEmail,
            'error' => $error,
        ]);
    }

    // Step 5: Database Installation & Execution
    private function step5RunInstall(): void
    {
        if (empty($_SESSION['install_db']) || empty($_SESSION['install_app']) || empty($_SESSION['install_admin'])) {
            redirect('/install?step=1');
            return;
        }

        $error = null;
        $existingTablesCount = 0;

        // Check if database already has tables
        try {
            $db = $_SESSION['install_db'];
            $pdo = new PDO("mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4", $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $stmt = $pdo->query("SHOW TABLES");
            $existingTablesCount = count($stmt->fetchAll(PDO::FETCH_COLUMN));
        } catch (\Throwable $e) {
            $error = 'Database check error: ' . $e->getMessage();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify($_POST['csrf_token'] ?? '')) {
                $error = 'Security session expired. Please refresh and try again.';
            } else {
                try {
                    $this->executeInstallation();
                    redirect('/install?step=6');
                    return;
                } catch (\Throwable $e) {
                    $error = 'Installation Error: ' . $e->getMessage();
                }
            }
        }

        $this->render('step5_install', [
            'title' => 'Step 5: Install & Initialize | Installer',
            'step' => 5,
            'installDb' => $_SESSION['install_db'],
            'installApp' => $_SESSION['install_app'],
            'installAdmin' => $_SESSION['install_admin'],
            'existingTablesCount' => $existingTablesCount,
            'error' => $error,
        ]);
    }

    // Step 6: Completion
    private function step6Complete(): void
    {
        if (!$this->isInstalled() && empty($_SESSION['install_completed'])) {
            redirect('/install?step=1');
            return;
        }

        $adminEmail = $_SESSION['install_admin']['email'] ?? 'admin';
        // Clear sensitive session data
        unset($_SESSION['install_db'], $_SESSION['install_app'], $_SESSION['install_admin'], $_SESSION['install_completed']);

        $this->render('step6_complete', [
            'title' => 'Installation Complete! | N.A Fresh Fruits & Coconuts',
            'step' => 6,
            'adminEmail' => $adminEmail,
        ]);
    }

    // Perform actual database setup, schema execution, and lock writing
    private function executeInstallation(): void
    {
        $db = $_SESSION['install_db'];
        $app = $_SESSION['install_app'];
        $admin = $_SESSION['install_admin'];

        // 1. Write .env configuration file
        $envContent = $this->buildEnvContent($db, $app);
        if (file_put_contents($this->envFile, $envContent) === false) {
            throw new Exception("Unable to write configuration to {$this->envFile}. Check filesystem permissions.");
        }

        // 2. Connect to Database
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // 3. Run all migrations safely
        $migrationFiles = glob(dirname(__DIR__, 2) . '/database/migrations/*.php');
        sort($migrationFiles);

        foreach ($migrationFiles as $file) {
            $migration = require $file;
            if (is_callable($migration)) {
                $migration($pdo);
            }
        }

        // 4. Run Seeders safely (Settings, Delivery Zones, Categories, Active Coconut Products)
        $seedFiles = [
            dirname(__DIR__, 2) . '/database/seeds/AdminSeeder.php',
            dirname(__DIR__, 2) . '/database/seeds/SettingsSeeder.php',
        ];

        foreach ($seedFiles as $seedFile) {
            if (file_exists($seedFile)) {
                $seeder = require $seedFile;
                if (is_callable($seeder)) {
                    $seeder($pdo);
                }
            }
        }

        // 5. Create or Update Administrator Account
        $passwordHash = password_hash($admin['password'], PASSWORD_BCRYPT);
        $adminCheck = $pdo->prepare("SELECT id FROM `admin_users` WHERE `email` = ?");
        $adminCheck->execute([$admin['email']]);
        $existingAdmin = $adminCheck->fetch();

        if ($existingAdmin) {
            $update = $pdo->prepare("UPDATE `admin_users` SET `name` = ?, `password_hash` = ?, `is_active` = 1, `updated_at` = NOW() WHERE `id` = ?");
            $update->execute([$admin['name'], $passwordHash, $existingAdmin['id']]);
            $adminId = $existingAdmin['id'];
        } else {
            $insert = $pdo->prepare("INSERT INTO `admin_users` (`name`, `email`, `password_hash`, `is_active`, `created_at`, `updated_at`) VALUES (?, ?, ?, 1, NOW(), NOW())");
            $insert->execute([$admin['name'], $admin['email'], $passwordHash]);
            $adminId = $pdo->lastInsertId();
        }

        // Ensure superadmin role assignment
        $roleCheck = $pdo->prepare("SELECT id FROM `roles` WHERE `slug` = 'superadmin'");
        $roleCheck->execute();
        $role = $roleCheck->fetch();
        if ($role) {
            $pdo->prepare("INSERT IGNORE INTO `admin_roles` (`admin_id`, `role_id`) VALUES (?, ?)")
                ->execute([$adminId, $role['id']]);
        }

        // Update Store Name & URL in settings table
        $setStmt = $pdo->prepare("INSERT INTO `settings` (`key`, `value`, `type`, `description`) VALUES (?, ?, 'string', ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
        $setStmt->execute(['store_name', $app['name'], 'Store Display Name']);
        $setStmt->execute(['app_url', $app['url'], 'Storefront URL']);

        // 6. Write secure installation lock
        $lockData = [
            'installed' => true,
            'installed_at' => date('Y-m-d H:i:s'),
            'app_name' => $app['name'],
            'app_url' => $app['url'],
            'admin_email' => $admin['email'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'php_version' => PHP_VERSION,
        ];

        if (!is_dir(dirname($this->lockFile))) {
            mkdir(dirname($this->lockFile), 0775, true);
        }

        if (file_put_contents($this->lockFile, json_encode($lockData, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("Could not write lock file at {$this->lockFile}.");
        }

        $_SESSION['install_completed'] = true;
    }

    private function buildEnvContent(array $db, array $app): string
    {
        $debugStr = $app['debug'] ? 'true' : 'false';
        return <<<ENV
APP_NAME="{$app['name']}"
APP_ENV={$app['env']}
APP_DEBUG={$debugStr}
APP_URL={$app['url']}
APP_TIMEZONE={$app['timezone']}

DB_CONNECTION=mysql
DB_HOST={$db['host']}
DB_PORT={$db['port']}
DB_DATABASE={$db['name']}
DB_USERNAME={$db['user']}
DB_PASSWORD={$db['pass']}

RAZORPAY_ENABLED=true
RAZORPAY_MODE=test
RAZORPAY_TEST_KEY_ID=rzp_test_nafreshfruits
RAZORPAY_TEST_KEY_SECRET=your_test_secret_here
RAZORPAY_TEST_WEBHOOK_SECRET=your_webhook_secret_here
RAZORPAY_LIVE_KEY_ID=
RAZORPAY_LIVE_KEY_SECRET=
RAZORPAY_LIVE_WEBHOOK_SECRET=
ENV;
    }

    private function getLockDetails(): array
    {
        if (file_exists($this->lockFile)) {
            $content = @file_get_contents($this->lockFile);
            $json = json_decode($content, true);
            if (is_array($json)) {
                return $json;
            }
        }
        return ['installed_at' => 'Unknown', 'admin_email' => 'System Owner'];
    }

    private function sanitizeDbError(string $error): string
    {
        // Hide raw password substrings if MySQL returns them in error string
        return preg_replace('/password.*$/i', '[password hidden]', $error);
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = dirname(__DIR__) . "/Views/install/{$view}.php";
        $layoutPath = dirname(__DIR__) . "/Views/install/layout.php";

        if (!file_exists($viewPath)) {
            echo "Installer view not found: {$view}";
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }
}
