<?php
$host = 'db';
$dbname = 'final_project';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query('SELECT file_name FROM pictures');
    while ($row = $stmt->fetch()) {
        echo "<option value='" . htmlspecialchars($row['file_name']) . "'>" . htmlspecialchars($row['file_name']) . "</option>";
    }
} catch (PDOException $e) {
    echo "<option disabled>Error loading images</option>";
}
?>
