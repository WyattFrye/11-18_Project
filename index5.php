<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Connection for `films` database
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

// Handle movie search in `films` database
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT `Movie ID`, `Producer`, `Title`, `Director` FROM films WHERE `Title` LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions for `films` database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['producer']) && isset($_POST['title']) && isset($_POST['director'])) {
        // Insert new entry into `films`
        $producer = htmlspecialchars($_POST['producer']);
        $title = htmlspecialchars($_POST['title']);
        $director = htmlspecialchars($_POST['director']);

        $insert_sql = 'INSERT INTO films (`Producer`, `Title`, `Director`) VALUES (:producer, :title, :director)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute(['producer' => $producer, 'title' => $title, 'director' => $director]);
    } elseif (isset($_POST['delete_id'])) {
        // Delete an entry from `films`
        $delete_id = (int) $_POST['delete_id'];

        $delete_sql = 'DELETE FROM films WHERE `Movie ID` = :id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['id' => $delete_id]);
    }
}

// Fetch all films from `films` database
$sql = 'SELECT `Movie ID`, `Producer`, `Title`, `Director` FROM films';
$stmt = $pdo->query($sql);

// Connection for `good_films` database
$good_films_dsn = "mysql:host=$host;dbname=good_films;charset=$charset";

try {
    $pdo_good_films = new PDO($good_films_dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Handle form submissions for `good_films` database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['praise_producer']) && isset($_POST['praise_title']) && isset($_POST['praise_director'])) {
        // Insert new entry into `good_films`
        $praise_producer = htmlspecialchars($_POST['praise_producer']);
        $praise_title = htmlspecialchars($_POST['praise_title']);
        $praise_director = htmlspecialchars($_POST['praise_director']);

        $insert_good_sql = 'INSERT INTO good_films (`Producer`, `Title`, `Director`) VALUES (:praise_producer, :praise_title, :praise_director)';
        $stmt_insert_good = $pdo_good_films->prepare($insert_good_sql);
        $stmt_insert_good->execute(['praise_producer' => $praise_producer, 'praise_title' => $praise_title, 'praise_director' => $praise_director]);
    } elseif (isset($_POST['praise_delete_id'])) {
        // Delete an entry from `good_films`
        $praise_delete_id = (int) $_POST['praise_delete_id'];

        $delete_good_sql = 'DELETE FROM good_films WHERE `Movie ID` = :id';
        $stmt_delete_good = $pdo_good_films->prepare($delete_good_sql);
        $stmt_delete_good->execute(['id' => $praise_delete_id]);
    }
}

// Fetch all praised films from `good_films` database
$good_sql = 'SELECT `Movie ID`, `Producer`, `Title`, `Director` FROM good_films';
$stmt_good = $pdo_good_films->query($good_sql);
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
    <h1 class="hero-title">BANNED FILMS!!!</h1>
    <h3 class="hero-subtitle">(RISE AGAINST MICHAEL BAY!)</h3>
    <p class="hero-subtitle">"Because nothing brings a community together like collectively deciding what others shouldn't watch!"</p>

    <!-- Search moved to hero section -->
    <div class="hero-search">
        <h2>Search for a Film to Ban</h2>
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
                                    <form action="index5.php" method="post" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['Movie ID']; ?>">
                                        <input type="submit" value="Ban!">
                                    </form>
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

<!-- Table section with container -->
<div class="table-container">
    <h2>All Films in Database</h2>
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
        <?php while ($row = $stmt->fetch()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Movie ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Producer']); ?></td>
                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                <td><?php echo htmlspecialchars($row['Director']); ?></td>
                <td>
                    <form action="index5.php" method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?php echo $row['Movie ID']; ?>">
                        <input type="submit" value="Ban!">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Form section with container -->
<div class="form-container">
    <h2>Condemn or Praise a Film Today</h2>
    <div class="form-wrapper">
        <div class="form-section">
            <h3>Condemn a Film</h3>
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
                <input type="submit" value="Condemn Film">
            </form>
        </div>
    </div>