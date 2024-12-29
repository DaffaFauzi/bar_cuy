<?php
session_start();
include '../includes/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Ambil daftar layanan dari database
try {
    $stmt = $conn->prepare("SELECT * FROM services");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Gagal mengambil data layanan: " . $e->getMessage());
}

// Tambah layanan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);

    // Validasi input
    if (empty($name) || empty($price)) {
        echo "Nama dan harga layanan tidak boleh kosong.";
    } else {
        try {
            $fileName = null;

            // Menangani upload gambar
            if (!empty($_FILES['image']['name'])) {
                $targetDir = "../uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // Membuat folder uploads jika belum ada
                }
                $fileName = time() . "_" . basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . $fileName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    echo "Gagal mengunggah gambar.";
                    $fileName = null;
                }
            }

            // Query untuk menambahkan layanan baru
            $stmt = $conn->prepare("INSERT INTO services (name, price, image) VALUES (:name, :price, :image)");
            $stmt->execute([
                'name' => $name,
                'price' => $price,
                'image' => $fileName
            ]);

            // Redirect setelah berhasil menambah layanan
            header("Location: manage_services.php");
            exit();
        } catch (Exception $e) {
            echo "Gagal menambahkan layanan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/admin.css">
    <title>Kelola Layanan</title>
    <style>
        /* Styling dengan warna latar belakang biru yang lebih segar */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #3498db; /* Background biru utama */
            color: #fff; /* Warna teks putih */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 30px auto;
            max-width: 1200px;
        }

        h1 {
            text-align: center;
            color: #fff; /* Warna heading putih */
            font-size: 32px;
            margin-bottom: 30px;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #fff;
            background-color: #e74c3c;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #c0392b;
        }

        form {
            background-color: #ecf0f1; /* Latar belakang form yang lebih terang */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        form h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #3498db; /* Warna heading form biru */
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        button {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: #ecf0f1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Menambahkan efek bayangan untuk kolom mengambang */
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        /* Kolom mengambang - efek ketika hover */
        input[type="text"]:hover,
        input[type="number"]:hover,
        input[type="file"]:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Bayangan lebih besar ketika hover */
            transform: translateY(-5px); /* Efek mengambang */
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus {
            border-color: #3498db;
            outline: none;
        }

        button {
            background-color: #27ae60;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2ecc71;
        }

        .service-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .service-item {
            background-color: #f7f9f9; /* Latar belakang item layanan lebih terang */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .service-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .service-item img {
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .service-item h3 {
            font-size: 20px;
            color: #2c3e50; /* Warna teks item layanan biru tua */
            font-weight: bold;
        }

        .service-item .price {
            font-size: 18px;
            color: #e74c3c; /* Warna harga merah */
            font-weight: bold;
        }

        .service-item .actions {
            margin-top: 15px;
        }

        .service-item .actions a {
            margin: 0 10px;
            padding: 8px 15px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .service-item .actions a:hover {
            background-color: #2980b9;
        }

        .service-item .actions a.delete {
            background-color: #e74c3c;
        }

        .service-item .actions a.delete:hover {
            background-color: #c0392b;
        }

        .service-item .actions a.edit {
            background-color: #f39c12;
        }

        .service-item .actions a.edit:hover {
            background-color: #e67e22;
        }

        /* Warna footer */
        footer {
            background-color: #2980b9; /* Biru gelap untuk footer */
            color: #fff;
            padding: 15px 0;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-button">Kembali ke Beranda</a>
        <h1>Kelola Layanan</h1>

        <!-- Form Tambah Layanan -->
        <form method="POST" action="" enctype="multipart/form-data">
            <h2>Tambah Layanan Baru</h2>
            <input type="text" name="name" placeholder="Nama Layanan" required>
            <input type="number" name="price" placeholder="Harga" required min="0">
            <input type="file" name="image" accept="image/*">
            <button type="submit" name="add_service">Tambah Layanan</button>
        </form>

        <h2>Daftar Layanan</h2>

        <!-- Daftar Layanan -->
        <div class="service-list">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <div class="service-item">
                        <img src="../uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Gambar Layanan" onerror="this.src='../uploads/default.png';">
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p class="price">Rp<?php echo number_format($service['price'], 0, ',', '.'); ?></p>
                        <div class="actions">
                            <a href="edit_service.php?id=<?php echo $service['id']; ?>" class="edit">Edit</a>
                            <a href="delete_service.php?id=<?php echo $service['id']; ?>" class="delete" onclick="return confirm('Yakin ingin menghapus layanan ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada layanan yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Semua Hak Cipta Dilindungi
    </footer>
</body>
</html>
