<?php
session_start();
include '../includes/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Ambil data layanan untuk diedit
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Ambil data layanan dari database
        $stmt = $conn->prepare("SELECT * FROM services WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$service) {
            die("Layanan tidak ditemukan.");
        }
    } catch (Exception $e) {
        die("Gagal mengambil data layanan: " . $e->getMessage());
    }
}

// Update layanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_service'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);

    if (empty($name) || empty($price)) {
        echo "Nama dan harga layanan tidak boleh kosong.";
    } else {
        try {
            $fileName = $service['image'];  // Menyimpan nama gambar lama jika tidak ada gambar baru

            if (!empty($_FILES['image']['name'])) {
                // Hapus gambar lama jika ada
                if (!empty($service['image'])) {
                    unlink('../uploads/' . $service['image']);
                }

                // Upload gambar baru
                $targetDir = "../uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = time() . "_" . basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . $fileName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    echo "Gagal mengunggah gambar.";
                    $fileName = null;
                }
            }

            // Update layanan di database
            $stmt = $conn->prepare("UPDATE services SET name = :name, price = :price, image = :image WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'price' => $price,
                'image' => $fileName,
                'id' => $id
            ]);

            header("Location: manage_services.php");
            exit();
        } catch (Exception $e) {
            echo "Gagal memperbarui layanan: " . $e->getMessage();
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
    <title>Edit Layanan</title>
    <style>
        /* Styling untuk tampilan yang lebih menarik dan estetik */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 40px auto;
            max-width: 1200px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
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
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form button {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: #ecf0f1;
        }

        form input[type="text"]:focus,
        form input[type="number"]:focus,
        form input[type="file"]:focus {
            border-color: #3498db;
            outline: none;
        }

        button {
            background-color: #27ae60;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 18px;
        }

        button:hover {
            background-color: #2ecc71;
        }

        .current-image {
            margin-top: 10px;
        }

        .current-image img {
            max-height: 150px;
            border-radius: 8px;
        }

        .alert {
            padding: 10px;
            background-color: #f39c12;
            color: white;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .alert a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="manage_services.php" class="back-button">Kembali ke Kelola Layanan</a>
        <h1>Edit Layanan</h1>

        <?php if (isset($error)) : ?>
            <div class="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
            <input type="number" name="price" value="<?php echo htmlspecialchars($service['price']); ?>" required min="0">
            <input type="file" name="image" accept="image/*">
            <?php if (!empty($service['image'])): ?>
                <div class="current-image">
                    <p>Gambar saat ini:</p>
                    <img src="../uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Gambar Layanan">
                </div>
            <?php endif; ?>
            <button type="submit" name="update_service">Perbarui Layanan</button>
        </form>
    </div>
</body>
</html>
