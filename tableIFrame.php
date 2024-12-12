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

// Handle updating "praised" column and deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['praise_id'])) {
        // Set praised to "Yes"
        $update_sql = 'UPDATE pictures SET praised = "Yes" WHERE id = :id';
        $pdo->prepare($update_sql)->execute(['id' => (int)$_POST['praise_id']]);
    } elseif (isset($_POST['unpraise_id'])) {
        // Set praised to "No"
        $update_sql = 'UPDATE pictures SET praised = "No" WHERE id = :id';
        $pdo->prepare($update_sql)->execute(['id' => (int)$_POST['unpraise_id']]);
    } elseif (isset($_POST['delete_id'])) {
        // Delete item by ID from both tables
        $delete_pictures_sql = 'DELETE FROM pictures WHERE id = :id';
        $delete_blobs_sql = 'DELETE FROM Blobs WHERE id = :id';
        $pdo->prepare($delete_pictures_sql)->execute(['id' => (int)$_POST['delete_id']]);
        $pdo->prepare($delete_blobs_sql)->execute(['id' => (int)$_POST['delete_id']]);
    }
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
                    <!-- Praise/Unpraise Buttons -->
                    <?php if ($row['praised'] === "Yes"): ?>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="unpraise_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="submit" value="Unpraise">
                        </form>
                    <?php else: ?>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="praise_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="submit" value="Praise">
                        </form>
                    <?php endif; ?>
                    <!-- Remove Button -->
                    <form action="" method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="submit" value="Remove" onclick="return confirm('This better be a test image and not a glorious dietrich image!!!');">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <footer>
    <form action="iframeAttempt.php" method="get" target="image-frame" style="display:inline;" onsubmit="setRandomId(this);">
        <input type="hidden" name="id" id="random-id">
        <input type="submit" value="Feeling Lucky?">
    </form>

<script>
function setRandomId(form) {
    var randomNumber = Math.floor(Math.random() * 55) + 1;

    form.querySelector('#random-id').value = randomNumber;
}
</script>
    </footer>

</body>
</html>
