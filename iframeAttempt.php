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


// Serve the image with proper HTML for sizing
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image Preview</title>
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <img src="<?php echo htmlspecialchars($file_name); ?>" alt="Preview">
</body>
</html>
