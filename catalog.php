<?php

session_start();
require_once 'auth.php';

// Check if user is logged in
// Any Stupid Comment
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$host = 'db'; 
$dbname = 'coffee_flavors'; 
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

// Handle Coffee search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT id, imported, name, distributer FROM coffee_flavors WHERE name LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['imported']) && isset($_POST['name']) && isset($_POST['distributer'])) {
        // Insert new entry
        $imported = htmlspecialchars($_POST['imported']);
        $name = htmlspecialchars($_POST['name']);
        $distributer = htmlspecialchars($_POST['distributer']);
        
        $insert_sql = 'INSERT INTO coffee_flavors (imported, name, distributer) VALUES (:imported, :name, :distributer)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute(['imported' => $imported, 'name' => $name, 'distributer' => $distributer]);
    } elseif (isset($_POST['delete_id'])) {
        // Delete an entry
        $delete_id = (int) $_POST['delete_id'];
        
        $delete_sql = 'DELETE FROM coffee_flavors WHERE id = :id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['id' => $delete_id]);
    }
    elseif (isset($_POST['out_of_stock'])) {
        // Set Temp_ban to yes
        $update_id = (int) $_POST['update_id'];
        
        $update_sql = 'update coffee_flavors SET temp_ban = "yes" WHERE id = :id';
        $stmt_update = $pdo->prepare($update_sql);
        $stmt_update->execute(['id' => $update_id]);
    }
    elseif (isset($_POST['restore'])) {
        // Set Temp_ban to yes
        $update_id = (int) $_POST['update_id'];
        
        $update_sql = 'update coffee_flavors SET temp_ban = "no" WHERE id = :id';
        $stmt_update = $pdo->prepare($update_sql);
        $stmt_update->execute(['id' => $update_id]);
    }
    
}

// Get all coffee_flavors for main table
$sql = 'SELECT id, imported, name, distributer, temp_ban FROM coffee_flavors WHERE temp_ban = "no"';
$stmt = $pdo->query($sql);

$sql2 = 'SELECT id, name, temp_ban FROM coffee_flavors WHERE temp_ban = "yes"';
$statement = $pdo->query($sql2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <name>Betty's Coffees</name>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-name">Betty's Coffee Inventory Check</h1>
        <p class="hero-subname">"Keep track of your stuff!!!"</p>
        
        <!-- Search moved to hero section -->
        <div class="hero-search">
            <h2>Search for a Coffee Flavor by name</h2>
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
                                    <th>imported</th>
                                    <th>name</th>
                                    <th>distributer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['imported']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['distributer']); ?></td>
                                    <td>
                                        <form action="index5.php" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <input type="submit" value="Ban!">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No coffee_flavors found matching your search.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <table>

<tr>

<td>


    <!-- Table section with container -->
    <div class="table-container">
        <h2>All Coffee Flavors in Database</h2>
        <table class="half-width-left-align">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>imported</th>
                    <th>name</th>
                    <th>distributer</th>
                    <th>Temp Banned</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['imported']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['distributer']); ?></td>
                    <td><?php echo htmlspecialchars($row['temp_ban']); ?></td>
                    <td>
                        <form action="index5.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Remove">
                        </form>
                    </td>
                    <td>
                        <form action="index5.php" method="post" style="display:inline;">
                            <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" name="out_of_stock" value = "Out Of Stock">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    </td>
    <td>

    <div class="table-container">
        <h2>All Coffee Flavors in Database</h2>
        <table class="half-width-left-align">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>name</th>
                    <th>Temp Out</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $statement->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['temp_ban']); ?></td>
                    <td>
                        <form action="index5.php" method="post" style="display:inline;">
                            <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" name="restore" value = "Restore">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Form section with container -->
    <div class="form-container">
        <h2>Add your favorite Coffee Today!!!</h2>
        <form action="index5.php" method="post">
            <label for="imported">imported:</label>
            <input type="text" id="imported" name="imported" required>
            <br><br>
            <label for="name">name:</label>
            <input type="text" id="name" name="name" required>
            <br><br>
            <label for="distributer">distributer:</label>
            <input type="text" id="distributer" name="distributer" required>
            <br><br>
            <input type="submit" value="Add Coffee">
        </form>
    </div>

    </td>
    </tr>

    </table>

</body>
</html>