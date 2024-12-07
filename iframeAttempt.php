<?php
session_start();
require_once 'auth.php';

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
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

$image_src = 'default.png';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $file_id = (int)$_GET['id'];
    $query = 'SELECT filename, blob_data FROM Blobs WHERE id = :id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $file_id]);
    $file = $stmt->fetch();

    if ($file) {
        $image_src = 'data:image/jpeg;base64,' . base64_encode($file['blob_data']);
    } else {
        $image_src = 'default.png';
    }
}
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
            width: 100vw;
            background-color: #232623;
        }
        img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <img src="<?= htmlspecialchars($image_src) ?>" alt="Image Preview">
</body>
</html>
