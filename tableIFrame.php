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
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Handle updating "praised" column
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['praise_id'])) {
    $praise_id = (int) $_POST['praise_id'];
    $update_sql = 'UPDATE pictures SET praised = "Yes" WHERE id = :id';
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute(['id' => $praise_id]);
}

// Handle search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT id, file_name, file_size, praised FROM pictures WHERE file_name LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
} else {
    $search_sql = 'SELECT id, file_name, file_size, praised FROM pictures';
    $search_stmt = $pdo->query($search_sql);
    $search_results = $search_stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catalog Table</title>
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>
    <h2>Catalog Items</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>File Name</th>
                <th>File Size</th>
                <th>Praised</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($search_results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                <td><?php echo htmlspecialchars($row['file_size']); ?></td>
                <td><?php echo htmlspecialchars($row['praised']); ?></td>
                <td>
                    <!-- View Image Button -->
                    <form action="iframeAttempt.php" method="get" target="image-frame" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="submit" value="View">
                    </form>
                    <!-- Praise Button -->
                    <?php if ($row['praised'] !== "Yes"): ?>
                    <form action="" method="post" style="display:inline;">
                        <input type="hidden" name="praise_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="submit" value="Praise">
                    </form>
                    <?php else: ?>
                        <span>Praised</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
