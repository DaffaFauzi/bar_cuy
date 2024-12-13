<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch services
$stmt = $conn->prepare("SELECT * FROM services");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stmt = $conn->prepare("INSERT INTO services (name, price) VALUES (:name, :price)");
    $stmt->execute(['name' => $name, 'price' => $price]);
    header("Location: manage_services.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/admin.css">
    <title>Manage Services</title>
</head>
<body>
    <h1>Manage Services</h1>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Service Name" required>
        <input type="number" name="price" placeholder="Price" required>
        <button type="submit">Add Service</button>
    </form>
    <h2>Service List</h2>
    <ul>
        <?php foreach ($services as $service): ?>
            <li><?php echo htmlspecialchars($service['name']); ?> - $<?php echo htmlspecialchars($service['price']); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
