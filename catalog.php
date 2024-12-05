<?php
$selected_file = [
    'name' => 'default.png', // Default image to display
    'size' => 'Default Size' // Optional, since size might not be relevant for the default image
];
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

// Handle picture search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT id, file_name, file_size, praised FROM pictures WHERE file_name LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // Delete an entry
        $delete_id = (int) $_POST['delete_id'];
        
        $delete_sql = 'DELETE FROM pictures WHERE id = :id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['id' => $delete_id]);
    } elseif (isset($_POST['out_of_stock'])) {
        // Mark as praised
        $update_id = (int) $_POST['update_id'];
        
        $update_sql = 'UPDATE pictures SET praised = "Yes" WHERE id = :id';
        $stmt_update = $pdo->prepare($update_sql);
        $stmt_update->execute(['id' => $update_id]);
    } elseif (isset($_POST['restore'])) {
        // Restore as not praised
        $update_id = (int) $_POST['update_id'];
        
        $update_sql = 'UPDATE pictures SET praised = "No" WHERE id = :id';
        $stmt_update = $pdo->prepare($update_sql);
        $stmt_update->execute(['id' => $update_id]);
    }

    // Handle View File Request
    $selected_file = null;
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['view_file'])) {
        $selected_file = [
            'name' => htmlspecialchars($_POST['view_file']),
            'size' => htmlspecialchars($_POST['view_size']),
        ];
    }
}

// Get all pictures for the main table
$sql = 'SELECT id, file_name, file_size, praised FROM pictures';
$stmt = $pdo->query($sql);

// Get all praised pictures for the second table
$sql2 = 'SELECT id, file_name, praised FROM pictures WHERE praised = "Yes"';
$statement = $pdo->query($sql2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Experience</title>
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-name">Its a Journey</h1>
        <p class="hero-subname">"The Professor Dietrich Expereice!!!"</p>
        
        <!-- Search Section -->
        <div class="hero-search">
            <h2>Find your image by name!</h2>
            <form action="" method="GET" class="search-form">
                <label for="search">Search by name:</label>
                <input type="text" id="search" name="search" required>
                <input type="submit" value="Search">
            </form>

            
            <?php if (isset($_GET['search'])): ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <?php if ($search_results && count($search_results) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>File Name</th>
                                    <th>File Size</th>
                                    <th>Actions</th>
                                    <th>Display</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['file_size']); ?></td>
                                    <td>
                                        <form action="" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <input type="submit" value="Remove">
                                        </form>
                                    </td>
                                    <td>
                                        <form action="iframeAttempt.php" method="get" target="image-frame" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                            <input type="submit" value="View">
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


    <iframe 
        src="tableIFrame.php" 
        name="table-frame" 
        style="width: 100%; height: 500px; border: none;" 
        title="Catalog Table">
    </iframe>

    <h1 style="text-align: center; margin-right: 20px;">Image displayed here</h1>

    <iframe 
        name="image-frame" 
        style="width: 600px; height: 600px; border: none; display: block; margin: 0 auto; padding-bottom: 30px;" 
        title="Image Preview">
    </iframe>   

    <footer>
        <a href="index.html">Back to homepage</a>
    </footer>
</body>
</html>
