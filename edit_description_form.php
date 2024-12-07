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
    <title>Edit Description</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style=" background-color: #333;">
    <section class="box box-long2" style="width: 400px; height: 300px; margin: auto;">
        <form action="edit_description.php" method="POST">
            <label for="file_name">
                <p style="font-size: 20px;">
                    Select an Image:
                </p>
            </label>
            <select id="file_name" name="file_name" required>
                <?php include 'get_image_names.php'; ?>
            </select>
            <br><br>
            <label for="description">
                <p style="font-size: 20px;">
                    New Description:
                </p>
            </label>
            <textarea id="description" name="description" rows="3" required></textarea>
            <br><br>
            <button type="submit">Update <br> Description</button>
        </form>
    </section>
</body>
</html>
