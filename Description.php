<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Database connection
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
    $stmt = $pdo->query('SELECT file_name, description FROM pictures');
    $pictures = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Names and Descriptions</title>
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>
    <h2>File Names and Descriptions</h2>
    <table>
        <thead>
            <tr>
                <th>File Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pictures as $picture): ?>
            <tr>
                <td><?php echo htmlspecialchars($picture['file_name']); ?></td>
                <td><?php echo htmlspecialchars($picture['description']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
