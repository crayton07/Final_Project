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
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file_name'], $_POST['description'])) {
        // Sanitize inputs
        $file_name = htmlspecialchars($_POST['file_name']);
        $description = htmlspecialchars($_POST['description']);

        // Update the description in the database
        $sql = 'UPDATE pictures SET description = :description WHERE file_name = :file_name';
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                'description' => $description,
                'file_name' => $file_name,
            ]);
            echo "Description updated successfully! <a href='index.php'>Return to Home</a>";
        } catch (PDOException $e) {
            die("Failed to update description: " . $e->getMessage());
        }
    } else {
        echo "Invalid input. <a href='index.php'>Return to Home</a>";
    }
} else {
    echo "Invalid request method. <a href='index.php'>Return to Home</a>";
}
?>
