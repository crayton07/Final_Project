<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Ensure a file name is passed as a GET parameter
if (!isset($_GET['file']) || empty($_GET['file'])) {
    echo "No file specified.";
    exit;
}

// Sanitize the file name
$file_name = htmlspecialchars($_GET['file']);

// Check if the file exists
if (!file_exists($file_name)) {
    echo "File not found.";
    exit;
}

// Serve the image directly
header("Content-Type: image/jpeg"); // Change this to the correct MIME type if not JPEG
readfile($file_name);
?>
