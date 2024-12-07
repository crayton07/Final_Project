<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
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
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file_name'], $_POST['file_size'], $_FILES['blob_file']) && $_FILES['blob_file']['error'] === UPLOAD_ERR_OK) {
        $file_name = htmlspecialchars($_POST['file_name']);
        $file_size = htmlspecialchars($_POST['file_size']);
        $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;

        $blob_file = file_get_contents($_FILES['blob_file']['tmp_name']);

        $pictures_sql = 'INSERT INTO pictures (file_name, file_size, description) VALUES (:file_name, :file_size, :description)';
        $pictures_stmt = $pdo->prepare($pictures_sql);
        $pictures_stmt->execute([
            'file_name' => $file_name,
            'file_size' => $file_size,
            'description' => $description,
        ]);

        $last_id = $pdo->lastInsertId();

        $blobs_sql = 'INSERT INTO Blobs (id, filename, blob_data) VALUES (:id, :filename, :blob_data)';
        $blobs_stmt = $pdo->prepare($blobs_sql);
        $blobs_stmt->execute([
            'id' => $last_id,
            'filename' => $file_name,
            'blob_data' => $blob_file,
        ]);

        echo renderResponsePage("Success!", "The file <em>{$file_name}</em> has been successfully added to the catalog.", "add_form.html");
    } else {
        echo renderResponsePage("Error!", "Failed to upload the file. Please check your inputs.", "add_form.html");
    }
} else {
    echo renderResponsePage("Warning!", "Invalid request method.", "add_form.html");
}

function renderResponsePage($title, $message, $redirect) {
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>{$title}</title>
        <link rel='stylesheet' href='styles.css'>
        <script>
            let seconds = 5;
            const countdown = setInterval(() => {
                document.getElementById('counter').textContent = seconds;
                seconds--;
                if (seconds < 0) {
                    clearInterval(countdown);
                    window.location.href = '{$redirect}';
                }
            }, 1000);
        </script>
    </head>
    <body class='custom-body'>
        <div class='message'>
            <strong>{$title}</strong> {$message}
        </div>
        <p class='counter'>Redirecting in <span id='counter'>5</span> seconds...</p>
    </body>
    </html>";
}
?>
