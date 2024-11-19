<?php

session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$host = 'localhost';
$dbname = 'films';
$user = 'fryew06';
$pass = '8973';
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

// Handle movie search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT `Movie ID`, `Producer`, `Title`, `Director`, `praise` FROM films WHERE `Title` LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['producer']) && isset($_POST['title']) && isset($_POST['director']) && isset($_POST['praise_choice'])) {
        // Insert new entry with praise choice
        $producer = htmlspecialchars($_POST['producer']);
        $title = htmlspecialchars($_POST['title']);
        $director = htmlspecialchars($_POST['director']);
        $praise_choice = htmlspecialchars($_POST['praise_choice']);

        $insert_sql = 'INSERT INTO films (`Producer`, `Title`, `Director`, `praise`) VALUES (:producer, :title, :director, :praise_choice)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute(['producer' => $producer, 'title' => $title, 'director' => $director, 'praise_choice' => $praise_choice]);
    } elseif (isset($_POST['delete_id'])) {
        // Delete an entry
        $delete_id = (int) $_POST['delete_id'];

        $delete_sql = 'DELETE FROM films WHERE `Movie ID` = :id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['id' => $delete_id]);
    } elseif (isset($_POST['praise_id'])) {
        // Update praise status to "yes"
        $praise_id = (int) $_POST['praise_id'];

        $update_praise_sql = 'UPDATE films SET `praise` = "yes" WHERE `Movie ID` = :id';
        $stmt_update_praise = $pdo->prepare($update_praise_sql);
        $stmt_update_praise->execute(['id' => $praise_id]);
    } elseif (isset($_POST['condemn_id'])) {
        // Update praise status to "no"
        $condemn_id = (int) $_POST['condemn_id'];

        $update_condemn_sql = 'UPDATE films SET `praise` = "no" WHERE `Movie ID` = :id';
        $stmt_update_condemn = $pdo->prepare($update_condemn_sql);
        $stmt_update_condemn->execute(['id' => $condemn_id]);
    }
}

// Fetch all condemned films
$condemn_sql = 'SELECT `Movie ID`, `Producer`, `Title`, `Director` FROM films WHERE `praise` = "no"';
$stmt_condemn = $pdo->query($condemn_sql);

// Fetch all praised films
$praise_sql = 'SELECT `Movie ID`, `Producer`, `Title`, `Director` FROM films WHERE `praise` = "yes"';
$stmt_praise = $pdo->query($praise_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BANNED FILMS</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<!-- Hero Section -->
<div class="hero-section">
    <h1 class="hero-title">Films: Good or Bad?!?!</h1>
    <h3 class="hero-subtitle">(You Decide!)</h3>
    <p class="hero-subtitle">"Because nothing brings a community together like collectively deciding what others shouldn't watch!"</p>

    <!-- Search moved to hero section -->
    <div class="hero-search">
        <h2>Search for a Film to Condemn or Praise</h2>
        <form action="" method="GET" class="search-form">
            <label for="search">Search by Title:</label>
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
                            <th>Producer</th>
                            <th>Title</th>
                            <th>Director</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($search_results as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Movie ID']); ?></td>
                                <td><?php echo htmlspecialchars($row['Producer']); ?></td>
                                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                                <td><?php echo htmlspecialchars($row['Director']); ?></td>
                                <td>
                                    <?php if ($row['praise'] === 'no'): ?>
                                        <form action="index5.php" method="post" style="display:inline;">
                                            <input type="hidden" name="praise_id" value="<?php echo $row['Movie ID']; ?>">
                                            <input type="submit" value="Praise!">
                                        </form>
                                    <?php else: ?>
                                        <p>Praised</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No films found matching your search.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Table section -->
<div class="table-container">
    <h2>Condemned Films</h2>
    <table class="half-width-left-align">
        <thead>
        <tr>
            <th>ID</th>
            <th>Producer</th>
            <th>Title</th>
            <th>Director</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $stmt_condemn->fetch()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Movie ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Producer']); ?></td>
                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                <td><?php echo htmlspecialchars($row['Director']); ?></td>
                <td>
                    <form action="index5.php" method="post" style="display:inline;">
                        <input type="hidden" name="praise_id" value="<?php echo $row['Movie ID']; ?>">
                        <input type="submit" value="Praise!">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Praised Films section -->
<div class="table-container">
    <h2>Praised Films</h2>
    <table class="half-width-left-align">
        <thead>
        <tr>
            <th>ID</th>
            <th>Producer</th>
            <th>Title</th>
            <th>Director</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $stmt_praise->fetch()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Movie ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Producer']); ?></td>
                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                <td><?php echo htmlspecialchars($row['Director']); ?></td>
                <td>
                    <form action="index5.php" method="post" style="display:inline;">
                        <input type="hidden" name="condemn_id" value="<?php echo $row['Movie ID']; ?>">
                        <input type="submit" value="Condemn!">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Form section -->
<div class="form-container">
    <h2>Add a Film</h2>
    <form action="index5.php" method="post">
        <label for="producer">Producer:</label>
        <input type="text" id="producer" name="producer" required>
        <br><br>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <br><br>
        <label for="director">Director:</label>
        <input type="text" id="director" name="director" required>
        <br><br>
        <label for="praise_choice">Add to:</label>
        <select id="praise_choice" name="praise_choice" required>
            <option value="no">Condemn Films</option>
            <option value="yes">Praised Films</option>
        </select>
        <br><br>
        <input type="submit" value="Add Film">
    </form>
</div>

</body>
</html>
