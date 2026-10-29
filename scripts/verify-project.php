<?php

/**
 * N.A Fresh Fruits & Coconuts - Project Verification Suite
 * Tests full system integrity, database, models, security, and ZERO TypeScript policy.
 */

echo "\n============================================================\n";
echo "  N.A Fresh Fruits & Coconuts - Verification & Test Suite   \n";
echo "============================================================\n\n";

$start = microtime(true);
$totalTests = 0;
$totalPassed = 0;
$totalFailed = 0;

// 1. Strict Zero TypeScript Check
echo "[1/4] Checking Zero TypeScript Policy Across Entire Codebase...\n";
$workspaceDir = dirname(__DIR__);
$tsFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($workspaceDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile()) {
        $ext = strtolower($file->getExtension());
        if ($ext === 'ts' || $ext === 'tsx') {
            $tsFiles[] = $file->getPathname();
        }
    }
}

if (count($tsFiles) === 0) {
    echo "  ✓ Zero TypeScript Policy PASSED: Exactly 0 .ts and 0 .tsx files found.\n";
    $totalTests++;
    $totalPassed++;
} else {
    echo "  ✗ Zero TypeScript Policy FAILED: Found " . count($tsFiles) . " TypeScript files:\n";
    foreach ($tsFiles as $tf) {
        echo "    - $tf\n";
    }
    $totalTests++;
    $totalFailed++;
}

// 2. Database Connectivity Check
echo "\n[2/4] Verifying MariaDB Database Connection & Schema...\n";
try {
    require_once $workspaceDir . '/vendor/autoload.php';
    $db = App\Core\Database::getConnection();
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $expectedTables = [
        'users', 'categories', 'products', 'product_variants',
        'delivery_zones', 'addresses', 'carts', 'cart_items',
        'orders', 'order_items', 'payments', 'coupons',
        'settings', 'admin_users', 'activity_logs'
    ];

    $missing = array_diff($expectedTables, $tables);
    if (empty($missing)) {
        echo "  ✓ Database connection established. All " . count($expectedTables) . " core tables present.\n";
        $totalTests++;
        $totalPassed++;
    } else {
        echo "  ✗ Missing tables: " . implode(', ', $missing) . "\n";
        $totalTests++;
        $totalFailed++;
    }
} catch (\Throwable $e) {
    echo "  ✗ Database connection error: " . $e->getMessage() . "\n";
    $totalTests++;
    $totalFailed++;
}

// 3. Run Test Suites
echo "\n[3/4] Running Functional Test Suites...\n";
$testClasses = [
    'AuthTest'     => $workspaceDir . '/tests/AuthTest.php',
    'ProductTest'  => $workspaceDir . '/tests/ProductTest.php',
    'CartTest'     => $workspaceDir . '/tests/CartTest.php',
    'CheckoutTest' => $workspaceDir . '/tests/CheckoutTest.php',
    'RazorpayTest' => $workspaceDir . '/tests/RazorpayTest.php',
    'AdminTest'    => $workspaceDir . '/tests/AdminTest.php',
    'OrderTest'    => $workspaceDir . '/tests/OrderTest.php',
    'InstallerTest'=> $workspaceDir . '/tests/InstallerTest.php',
];

foreach ($testClasses as $className => $file) {
    if (!file_exists($file)) {
        echo "  ✗ Missing test file: $file\n";
        $totalTests++;
        $totalFailed++;
        continue;
    }

    require_once $file;
    $ref = new ReflectionClass($className);
    $instance = $ref->newInstance();
    $suiteResults = $instance->run();

    echo "  • Running $className (" . count($suiteResults) . " assertions)...\n";
    foreach ($suiteResults as $key => $res) {
        $totalTests++;
        if (is_array($res) && isset($res['name'])) {
            $testName = $res['name'];
            $isPassed = !empty($res['passed']);
            $errorMsg = $res['error'] ?? 'Assertion failed';
        } else {
            $testName = str_replace('_', ' ', (string)$key);
            $isPassed = (bool)$res;
            $errorMsg = 'Assertion evaluated to false';
        }

        if ($isPassed) {
            $totalPassed++;
            echo "    ✓ " . ucfirst($testName) . "\n";
        } else {
            $totalFailed++;
            echo "    ✗ " . ucfirst($testName) . " - " . $errorMsg . "\n";
        }
    }
}

// 4. Summary
$duration = round(microtime(true) - $start, 3);
echo "\n[4/4] Verification Summary:\n";
echo "============================================================\n";
echo "Total Assertions: $totalTests\n";
echo "Passed:           $totalPassed\n";
echo "Failed:           $totalFailed\n";
echo "Execution Time:   {$duration}s\n";
echo "============================================================\n\n";

if ($totalFailed > 0) {
    exit(1);
}

exit(0);
