<?php

$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erorr connect to data: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $review = $_POST['review'];

    $stmt = $pdo->prepare("INSERT INTO reviews (name, review) VALUES (:name, :review)");
    $stmt->execute(['name' => $name, 'review' => $review]);
    header("Location: reviews.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $review = $_POST['review'];

    $stmt = $pdo->prepare("UPDATE reviews SET name = :name, review = :review WHERE id = :id");
    $stmt->execute(['name' => $name, 'review' => $review, 'id' => $id]);
    header("Location: reviews.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: reviews.php");
    exit;
}


$stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD reviews</title>
</head>
<body>
    <h1>reviews</h1>

    
    <form method="POST">
        <h2>add reviews</h2>
        <label>Name:</label><br>
        <input type="text" name="name" required><br>
        <label>review:</label><br>
        <textarea name="review" required></textarea><br>
        <button type="submit" name="add">add</button>
    </form>

    <hr>

   
    <h2>List review</h2>
    <?php foreach ($reviews as $review): ?>
        <div>
            <p><strong><?= htmlspecialchars($review['name']) ?></strong> (<?= $review['created_at'] ?>)</p>
            <p><?= nl2br(htmlspecialchars($review['review'])) ?></p>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="id" value="<?= $review['id'] ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($review['name']) ?>" required>
                <textarea name="review" required><?= htmlspecialchars($review['review']) ?></textarea>
                <button type="submit" name="update">update</button>
            </form>
            <a href="?delete=<?= $review['id'] ?>" onclick="return confirm('Are you sure?')">delete</a>
        </div>
        <hr>
    <?php endforeach; ?>
</body>
</html>