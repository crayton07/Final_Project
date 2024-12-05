<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Ensure a file ID is passed as a GET parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "No file specified.";
    exit;
}

$host = 'db'; 
$dbname = 'final_project'; 
$user = 'root'; 
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Fetch the image data from the Blobs table
$file_id = (int) $_GET['id'];
$query = 'SELECT filename, blob_data FROM Blobs WHERE id = :id';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $file_id]);
$file = $stmt->fetch();

if (!$file) {
    echo "File not found.";
    exit;
}

// Serve the image with proper headers
header('Content-Type: image/jpeg'); // Adjust MIME type if necessary
header('Content-Disposition: inline; filename="' . htmlspecialchars($file['filename']) . '"');
echo $file['blob_data'];
exit;
