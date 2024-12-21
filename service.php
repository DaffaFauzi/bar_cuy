<?php
include 'includes/db.php';

// Ambil data layanan dari database
$stmt = $conn->prepare("SELECT * FROM services");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Barbershop</title>
    <style>
        body { /* Gaya CSS seperti sebelumnya */ }
    </style>
</head>
<body>
    <a href="index.html" class="back-button">Kembali ke Beranda</a>
    <h1>Layanan Barbershop Kami</h1>
    <div class="services-container">
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <img src="<?php echo !empty($service['image']) ? 'head_cuy/' . htmlspecialchars($service['image']) : 'images/default.jpg'; ?>" alt="Gambar Layanan">
                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                <p>Harga: Rp<?php echo number_format(htmlspecialchars($service['price']), 0, ',', '.'); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
