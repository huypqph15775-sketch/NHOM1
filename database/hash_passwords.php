<?php
// Run this script after importing database.sql to hash plaintext passwords.
// Usage: php hash_passwords.php

$host = '127.0.0.1';
$db   = 'smartphone';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    echo "DB connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Helper: hash column if it looks unhashed (length < 60 or matches known plain '123456')
function maybeHash($pdo, $table, $idCol, $passCol) {
    $sql = "SELECT `$idCol`, `$passCol` FROM `$table`";
    $stmt = $pdo->query($sql);
    $updated = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row[$idCol];
        $pw = $row[$passCol];
        if ($pw === null) continue;
        // if password appears to be plain (short) or exactly '123456'
        if (strlen($pw) < 60 || $pw === '123456') {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $u = $pdo->prepare("UPDATE `$table` SET `$passCol` = :h WHERE `$idCol` = :id");
            $u->execute([':h' => $hash, ':id' => $id]);
            $updated++;
        }
    }
    echo "Updated $updated rows in $table\n";
}

// Tables and columns used in this project
maybeHash($pdo, 'admin', 'admin_id', 'admin_password');
maybeHash($pdo, 'customer', 'customer_id', 'customer_password');

echo "Done. If your application uses password_verify / password_hash, logins should work.\n";
?>