<?php
require_once 'includes/db.php';

echo "<h2>Adding missing columns...</h2>";

$tables_cols = [
    'providers' => [
        'selfie' => 'VARCHAR(255)',
        'is_online' => 'TINYINT(1) DEFAULT 0',
        'online_since' => 'DATETIME DEFAULT NULL',
        'payout_account' => 'VARCHAR(50)',
        'wallet_balance' => 'DECIMAL(10,2) DEFAULT 0',
    ],
    'clients' => [
        'selfie' => 'VARCHAR(255)',
    ]
];

foreach ($tables_cols as $table => $columns) {
    foreach ($columns as $col => $def) {
        try {
            $check = $conn->query("SHOW COLUMNS FROM $table LIKE '$col'");
            if ($check->num_rows == 0) {
                $conn->query("ALTER TABLE $table ADD COLUMN $col $def");
                echo "<p style='color:green;'>OK: Added $col to $table</p>";
            } else {
                echo "<p style='color:gray;'>Skip: $col already exists in $table</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h2 style='color:green;'>Done!</h2>";
?>
