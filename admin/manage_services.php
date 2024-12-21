<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Ambil daftar layanan dari database
$stmt = $conn->prepare("SELECT * FROM services");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Proses unggah gambar
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../head_cuy/";
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            // Tambahkan data layanan dan nama file gambar ke database
            $stmt = $conn->prepare("INSERT INTO services (name, price, image) VALUES (:name, :price, :image)");
            $stmt->execute(['name' => $name, 'price' => $price, 'image' => $fileName]);
        } else {
            echo "Gagal mengunggah gambar.";
        }
    } else {
        // Tambahkan layanan tanpa gambar
        $stmt = $conn->prepare("INSERT INTO services (name, price) VALUES (:name, :price)");
        $stmt->execute(['name' => $name, 'price' => $price]);
    }
    header("Location: manage_services.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        body { /* Gaya CSS seperti sebelumnya */ }
    </style>
    <title>Kelola Layanan</title>
</head>
<body>
    <a href="index.php" class="back-button">Kembali ke Beranda</a>
    <h1>Kelola Layanan</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Nama Layanan" required>
        <input type="number" name="price" placeholder="Harga" required>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Tambah Layanan</button>
    </form>
    <h2>Daftar Layanan</h2>
    <ul>
        <?php foreach ($services as $service): ?>
            <li>
                <span><?php echo htmlspecialchars($service['name']); ?></span>
                <span>Rp<?php echo number_format(htmlspecialchars($service['price']), 0, ',', '.'); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
