<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: guess.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Catalog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1 style="text-align: center;">
            Add to Pictures and Blobs
        </h1>
    </header>

    <section class="container" style="width: 200px;">
        <form action="add.php" method="POST" enctype="multipart/form-data" style="flex-direction: column; align-items: center;">
            <label for="file_name">File Name:</label>
            <input type="text" id="file_name" name="file_name" required>
            <br>
            
            <label for="file_size">File Size:</label>
            <input type="text" id="file_size" name="file_size" required>
            <br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="3"></textarea>
            <br>

            <label for="blob_file">Upload Image:</label>
            <input type="file" id="blob_file" name="blob_file" accept="image/*" required>
            <br>

            <button type="submit">Add to Catalog</button>
        </form>
    </section>

    <footer>
        <a href="http://localhost:8080/Final_Project/index.php">Return to Homepage</a>
    </footer>
</body>
</html>
