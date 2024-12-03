<?php
$selected_file = null;
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
    if (isset($_POST['file_name']) && isset($_POST['file_size'])) {
        // Insert new entry
        $file_name = htmlspecialchars($_POST['file_name']);
        $file_size = htmlspecialchars($_POST['file_size']);
        
        $insert_sql = 'INSERT INTO pictures (file_name, file_size) VALUES (:file_name, :file_size)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute(['file_name' => $file_name, 'file_size' => $file_size]);
    } elseif (isset($_POST['delete_id'])) {
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
    <title>Betty's Coffees</title>
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-name">Betty's Coffee Inventory Check</h1>
        <p class="hero-subname">"Keep track of your stuff!!!"</p>
        
        <!-- Search Section -->
        <div class="hero-search">
            <h2>Search for a Coffee Flavor by Name</h2>
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


    <section class="hero-section">
    <?php if ($selected_file): ?>
    <div class="file-display">
        <h2>Selected File Details</h2>
        <p><strong>File Name:</strong> <?php echo $selected_file['name']; ?></p>
        <p><strong>File Size:</strong> <?php echo $selected_file['size']; ?></p>
        <div>
            <strong>Image Preview:</strong><br>
            <img src="<?php echo htmlspecialchars($selected_file['name']); ?>" alt="<?php echo htmlspecialchars($selected_file['name']); ?>" style="max-width: 400px; max-height: 400px;">
        </div>
    </div>
    <?php endif; ?>
    </section>


    <!-- Tables Section -->
    <div class="table-container">
        <h2>All Pictures in Database</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>File Name</th>
                    <th>File Size</th>
                    <th>Praised</th>
                    <th>Praise</th>
                    <th>View File</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['file_size']); ?></td>
                    <td><?php echo htmlspecialchars($row['praised']); ?></td>
                    <td>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" name="out_of_stock" value="Mark Praised">
                        </form>
                    </td>


                    <td>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="view_file" value="<?php echo htmlspecialchars($row['file_name']); ?>">
                            <input type="hidden" name="view_size" value="<?php echo htmlspecialchars($row['file_size']); ?>">
                            <input type="submit" value="View File">
                        </form>
                    </td>


                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Picture Form -->
    <div class="form-container">
        <h2>Add a New Picture</h2>
        <form action="" method="post">
            <label for="file_name">File Name:</label>
            <input type="text" id="file_name" name="file_name" required><br><br>
            <label for="file_size">File Size:</label>
            <input type="text" id="file_size" name="file_size" required><br><br>
            <input type="submit" value="Add Picture">
        </form>
    </div>





</body>
</html>
