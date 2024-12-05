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
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (isset($_POST['file_name'], $_POST['file_size'], $_FILES['blob_file']) && $_FILES['blob_file']['error'] === UPLOAD_ERR_OK) {
        // Gather form inputs
        $file_name = htmlspecialchars($_POST['file_name']);
        $file_size = htmlspecialchars($_POST['file_size']);
        $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;

        // Handle file upload
        $blob_file = file_get_contents($_FILES['blob_file']['tmp_name']);

        // Insert into pictures table
        $pictures_sql = 'INSERT INTO pictures (file_name, file_size, description) VALUES (:file_name, :file_size, :description)';
        $pictures_stmt = $pdo->prepare($pictures_sql);
        $pictures_stmt->execute([
            'file_name' => $file_name,
            'file_size' => $file_size,
            'description' => $description,
        ]);

        // Get the ID of the inserted record
        $last_id = $pdo->lastInsertId();

        // Insert into Blobs table
        $blobs_sql = 'INSERT INTO Blobs (id, filename, blob_data) VALUES (:id, :filename, :blob_data)';
        $blobs_stmt = $pdo->prepare($blobs_sql);
        $blobs_stmt->execute([
            'id' => $last_id,
            'filename' => $file_name,
            'blob_data' => $blob_file,
        ]);

        // Redirect or confirm success
        echo "File successfully added to catalog!";
    } else {
        echo "Failed to upload the file. Please check your inputs.";
    }
} else {
    echo "Invalid request method.";
}
?>
