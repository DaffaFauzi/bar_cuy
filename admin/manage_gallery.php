<?php
session_start();
include '../includes/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Menangani form submission untuk menambah gambar baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_image'])) {
    $caption = trim($_POST['caption']);
    $description = trim($_POST['description']);

    if (empty($caption) || empty($description)) {
        echo "Caption dan Deskripsi tidak boleh kosong.";
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

            // Simpan data gambar, caption, dan deskripsi ke dalam database
            $stmt = $conn->prepare("INSERT INTO gallery (caption, description, image_path) VALUES (:caption, :description, :image_path)");
            $stmt->execute([
                'caption' => $caption,
                'description' => $description,
                'image_path' => $fileName
            ]);

            header("Location: manage_image.php");
            exit();
        } catch (Exception $e) {
            echo "Gagal menambahkan gambar: " . $e->getMessage();
        }
    }
}

// Ambil daftar gambar dari database
try {
    $stmt = $conn->prepare("SELECT * FROM gallery ORDER BY id DESC");
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Gagal mengambil data galeri: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Gambar</title>
    <style>
        /* Styling yang sama dengan manage_service.php */
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
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        form h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #3498db;
        }

        /* Styling untuk kolom form */
        input[type="text"],
        textarea,
        input[type="file"],
        button {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: #ecf0f1;
        }

        input[type="text"]:focus,
        textarea:focus,
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

        /* Floating Inputs */
        .floating-label {
            position: relative;
            margin-bottom: 20px;
        }

        .floating-label input,
        .floating-label textarea {
            font-size: 16px;
            padding: 15px 12px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #ecf0f1;
            transition: all 0.3s ease;
        }

        .floating-label label {
            position: absolute;
            top: 0;
            left: 12px;
            font-size: 16px;
            color: #7f8c8d;
            transition: 0.3s;
            pointer-events: none;
            background-color: #fff;
            padding: 0 4px;
        }

        .floating-label input:focus + label,
        .floating-label textarea:focus + label,
        .floating-label input:not(:placeholder-shown) + label,
        .floating-label textarea:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 14px;
            color: #3498db;
        }

        .gallery-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .gallery-item {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .gallery-item img {
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .gallery-item h3 {
            font-size: 20px;
            color: #2c3e50;
            font-weight: bold;
        }

        .gallery-item p {
            font-size: 16px;
            color: #7f8c8d;
        }

        .gallery-item .actions {
            margin-top: 15px;
        }

        .gallery-item .actions a {
            margin: 0 10px;
            padding: 8px 15px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .gallery-item .actions a:hover {
            background-color: #2980b9;
        }

        .gallery-item .actions a.delete {
            background-color: #e74c3c;
        }

        .gallery-item .actions a.delete:hover {
            background-color: #c0392b;
        }

        .gallery-item .actions a.edit {
            background-color: #f39c12;
        }

        .gallery-item .actions a.edit:hover {
            background-color: #e67e22;
        }

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
        <h1>Kelola Gambar</h1>

        <!-- Form Tambah Gambar -->
        <form method="POST" action="" enctype="multipart/form-data">
            <h2>Tambah Gambar Baru</h2>

            <!-- Caption Gambar -->
            <div class="floating-label">
                <input type="text" name="caption" id="caption" placeholder=" " required>
                <label for="caption">Caption Gambar</label>
            </div>

            <!-- Deskripsi Gambar -->
            <div class="floating-label">
                <textarea name="description" id="description" placeholder=" " rows="4" required></textarea>
                <label for="description">Deskripsi Gambar</label>
            </div>

            <!-- Pilih File Gambar -->
            <div class="floating-label">
                <input type="file" name="image" id="image" accept="image/*" required>
                <label for="image">Pilih Gambar</label>
            </div>

            <button type="submit" name="add_image">Tambah Gambar</button>
        </form>

        <h2>Galeri</h2>

        <!-- Daftar Gambar -->
        <div class="gallery-list">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $image): ?>
                    <div class="gallery-item">
                        <img src="../uploads/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Gambar Galeri" onerror="this.src='../uploads/default.png';">
                        <h3><?php echo htmlspecialchars($image['caption']); ?></h3>
                        <p><?php echo htmlspecialchars($image['description']); ?></p>
                        <div class="actions">
                            <a href="edit_image.php?id=<?php echo $image['id']; ?>" class="edit">Edit</a>
                            <a href="delete_image.php?id=<?php echo $image['id']; ?>" class="delete" onclick="return confirm('Yakin ingin menghapus gambar ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada gambar dalam galeri.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Semua Hak Cipta Dilindungi
    </footer>
</body>
</html>
