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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .back-button {
            display: inline-block;
            margin: 20px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
            font-size: 2.5em;
        }

        .services-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px;
        }

        .service-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            width: 250px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: scale(1.05);
        }

        .service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .service-card h3 {
            font-size: 1.5em;
            margin: 10px 0;
        }

        .service-card p {
            font-size: 1.2em;
            color: #333;
        }
    </style>
</head>
<body>
    <a href="index.html" class="back-button">Kembali ke Beranda</a>
    <h1>Layanan Barbershop Kami</h1>
    <div class="services-container">
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <!-- Menampilkan gambar, jika tidak ada gambar, tampilkan gambar default -->
                <img src="<?php echo !empty($service['image']) ? 'uploads/' . htmlspecialchars($service['image']) : 'images/default.jpg'; ?>" alt="Gambar Layanan">
                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                <p>Harga: Rp<?php echo number_format(htmlspecialchars($service['price']), 0, ',', '.'); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
