<?php
session_start();
require_once 'auth.php';

// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: guess.html');
    exit;
}

// Database configuration
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
    exit("Database connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        // Delete an entry
        $delete_sql = 'DELETE FROM pictures WHERE id = :id';
        $pdo->prepare($delete_sql)->execute(['id' => (int)$_POST['delete_id']]);
    } elseif (isset($_POST['toggle_praise_id'])) {
        // Toggle praised status
        $id = (int)$_POST['toggle_praise_id'];
        $check_sql = 'SELECT praised FROM pictures WHERE id = :id';
        $stmt_check = $pdo->prepare($check_sql);
        $stmt_check->execute(['id' => $id]);
        $current_state = $stmt_check->fetchColumn();

        $new_state = ($current_state === "Yes") ? "No" : "Yes";
        $update_sql = 'UPDATE pictures SET praised = :new_state WHERE id = :id';
        $pdo->prepare($update_sql)->execute(['new_state' => $new_state, 'id' => $id]);
    }
}

// Handle picture search
$search_results = [];
if (!empty($_GET['search'])) {
    $search_sql = 'SELECT id, file_name, file_size, praised FROM pictures WHERE file_name LIKE :search';
    $stmt = $pdo->prepare($search_sql);
    $stmt->execute(['search' => '%' . $_GET['search'] . '%']);
    $search_results = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Full Dietrich Catalog</title>
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>
    <div class="hero-section">
        <h1 class="hero-name">It's a Journey</h1>
        <p class="hero-subname">"The Professor Dietrich Experience!!!"</p>

        <!-- Search Section -->
        <div class="hero-search">
            <h2>Search for Images to Remove</h2>
            <form action="" method="GET" class="search-form">
                <label for="search">Search by name:</label>
                <input type="text" id="search" name="search" required>
                <input type="submit" value="Search">
            </form>

            <?php if (!empty($_GET['search'])): ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <?php if (!empty($search_results)): ?>
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
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['file_name']) ?></td>
                                    <td><?= htmlspecialchars($row['file_size']) ?></td>
                                    <td><?= htmlspecialchars($row['praised']) ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                            <input type="submit" value="Remove">
                                        </form>

                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="toggle_praise_id" value="<?= $row['id'] ?>">
                                            <input type="submit" value="<?= $row['praised'] === 'Yes' ? 'Unpraise' : 'Praise' ?>">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No pictures found matching your search.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <iframe src="tableIFrame.php" name="table-frame" style="width: 100%; height: 500px; border: none;" title="Catalog Table"></iframe>
    <h1 style="text-align: center;">Image displayed here</h1>
    <iframe src="iframeAttempt.php" name="image-frame" style="width: 600px; height: 600px; border: none; display: block; margin: 0 auto; margin-bottom: 20px;" title="Image Preview"></iframe>   

    <footer>
        <a href="index.php">Back to homepage</a>
    </footer>
</body>
</html>
