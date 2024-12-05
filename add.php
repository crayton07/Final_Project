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

        // Redirect and display success message
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Success</title>
    <style>
        body {
            background-color: #575a57;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .message {
            background-color: #232623;
            color: #d5a064;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            margin-bottom: 20px;
        }
        .counter {
            font-size: 1.2em;
        }
    </style>
    <script>
        let seconds = 5;
        const countdown = setInterval(() => {
            document.getElementById('counter').textContent = seconds;
            seconds--;
            if (seconds < 0) {
                clearInterval(countdown);
                window.location.href = 'add_form.html';
            }
        }, 1000);
    </script>
</head>
<body>
    <div class='message'>
        <strong>Success!</strong> The file <em>{$file_name}</em> has been successfully added to the catalog.
    </div>
    <p class='counter'>Redirecting to the form in <span id='counter'>5</span> seconds...</p>
</body>
</html>";
    } else {
        // Error message
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Error</title>
    <style>
        body {
            background-color: #575a57;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .message {
            background-color: #3e413e;
            color: #de630b;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            margin-bottom: 20px;
        }
        .counter {
            font-size: 1.2em;
        }
    </style>
    <script>
        let seconds = 5;
        const countdown = setInterval(() => {
            document.getElementById('counter').textContent = seconds;
            seconds--;
            if (seconds < 0) {
                clearInterval(countdown);
                window.location.href = 'add_form.html';
            }
        }, 1000);
    </script>
</head>
<body>
    <div class='message'>
        <strong>Error!</strong> Failed to upload the file. Please check your inputs.
    </div>
    <p class='counter'>Redirecting to the form in <span id='counter'>5</span> seconds...</p>
</body>
</html>";
    }
} else {
    // Invalid request message
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Warning</title>
    <style>
        body {
            background-color: #575a57;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .message {
            background-color: #3e413e;
            color: #d5a064;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            margin-bottom: 20px;
        }
        .counter {
            font-size: 1.2em;
        }
    </style>
    <script>
        let seconds = 5;
        const countdown = setInterval(() => {
            document.getElementById('counter').textContent = seconds;
            seconds--;
            if (seconds < 0) {
                clearInterval(countdown);
                window.location.href = 'add_form.html';
            }
        }, 1000);
    </script>
</head>
<body>
    <div class='message'>
        <strong>Warning!</strong> Invalid request method.
    </div>
    <p class='counter'>Redirecting to the form in <span id='counter'>5</span> seconds...</p>
</body>
</html>";
}
?>
