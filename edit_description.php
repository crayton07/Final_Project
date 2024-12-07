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
        $file_name = htmlspecialchars($_POST['file_name']);
        $description = htmlspecialchars($_POST['description']);

        $sql = 'UPDATE pictures SET description = :description WHERE file_name = :file_name';
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                'description' => $description,
                'file_name' => $file_name,
            ]);
            // Success message
            echo renderResponsePage("Success!", "The description for <em>{$file_name}</em> was updated successfully.", "index.php");
        } catch (PDOException $e) {
            // Error message
            echo renderResponsePage("Error!", "Failed to update the description. Please try again.", "index.php");
        }
    } else {
        // Invalid input message
        echo renderResponsePage("Invalid Input!", "The form inputs were invalid. Please try again.", "index.php");
    }
} else {
    // Invalid request method message
    echo renderResponsePage("Invalid Request!", "This action can only be performed through the form submission.", "index.php");
}

// Function to render a styled response page
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
