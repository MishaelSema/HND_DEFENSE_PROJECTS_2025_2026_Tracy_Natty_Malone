<?php
require_once 'includes/db.php';

echo "<h2>Checking table columns...</h2>";

$tables = ['providers', 'clients'];
foreach ($tables as $table) {
    echo "<h3>$table table:</h3>";
    $res = $conn->query("SHOW COLUMNS FROM $table");
    while ($row = $res->fetch_assoc()) {
        echo "<p style='margin:2px;'>- " . $row['Field'] . " (" . $row['Type'] . ")</p>";
    }
}
?>
