<?php
session_start();
include '../includes/db.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: gallery.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $caption = $_POST['caption'];
        $description = $_POST['description'];  // Mengambil deskripsi
        $image_path = $_POST['current_image']; // Menyimpan gambar lama jika tidak ada gambar baru yang diunggah

        // Proses upload gambar jika ada gambar baru yang dipilih
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($_FILES['image']['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validasi tipe file gambar
            $valid_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $valid_types)) {
                $error = "Tipe file tidak valid. Harap pilih gambar JPG, JPEG, PNG, atau GIF.";
            } else {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = 'uploads/' . basename($_FILES['image']['name']);
                } else {
                    $error = "Terjadi kesalahan saat mengunggah gambar.";
                }
            }
        }

        // Jika tidak ada error, simpan data ke database
        if (!isset($error)) {
            try {
                $stmt = $conn->prepare("UPDATE gallery SET caption = :caption, description = :description, image_path = :image_path WHERE id = :id");
                $stmt->execute([
                    'caption' => $caption,
                    'description' => $description,
                    'image_path' => $image_path,
                    'id' => $id
                ]);
                header("Location: manage_gallery.php");
                exit();
            } catch (PDOException $e) {
                $error = "Terjadi kesalahan saat memperbarui data: " . $e->getMessage();
            }
        }
    }

    // Ambil data gambar berdasarkan ID
    try {
        $stmt = $conn->prepare("SELECT * FROM gallery WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$image) {
            die("Gambar tidak ditemukan!");
        }
    } catch (PDOException $e) {
        die("Gagal mengambil data: " . $e->getMessage());
    }
} else {
    header("Location: manage_gallery.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gambar</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f2f4f7, #ffffff);
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 700px;
            margin: 60px auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #1D9D68;
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-family: 'Ubuntu', sans-serif;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        label {
            font-size: 1.1rem;
            font-weight: 500;
            color: #555;
            text-align: left;
            margin-left: 5px;
        }
        input[type="text"], textarea, input[type="file"], button {
            padding: 14px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        input[type="text"]:focus, textarea:focus, input[type="file"]:focus {
            border-color: #1D9D68;
            outline: none;
        }
        button {
            background-color: #1D9D68;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 14px;
            border-radius: 10px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #156d4b;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .error {
            color: #e74c3c;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
        .form-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 16px;
            color: #555;
        }
        .form-footer a {
            color: #1D9D68;
            text-decoration: none;
            font-weight: bold;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .current-image img {
    max-height: 150px; /* Menyesuaikan ukuran gambar */
    border-radius: 8px; /* Membulatkan sudut gambar */
}

        .current-image p {
            font-size: 1.1rem;
            font-weight: 500;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Gambar</h1>

        <!-- Menampilkan error jika ada -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- Menampilkan gambar lama -->
            <?php if (!empty($image['image_path']) && file_exists('../uploads/' . $image['image_path'])): ?>
                <div class="current-image">
                    <p>Gambar saat ini:</p>
                    <img src="../uploads/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Gambar Layanan">
                </div>
            <?php else: ?>
                <p>Gambar tidak ditemukan.</p>
            <?php endif; ?>

            <!-- Input untuk mengganti caption -->
            <label for="caption">Nama Layanan:</label>
            <input type="text" name="caption" value="<?php echo htmlspecialchars($image['caption']); ?>" required>

            <!-- Input untuk mengganti deskripsi -->
            <label for="description">Deskripsi Layanan:</label>
            <textarea name="description" required><?php echo htmlspecialchars($image['description']); ?></textarea>

            <!-- Input untuk mengganti gambar -->
            <label for="image">Ganti Gambar (Opsional):</label>
            <input type="file" name="image">

            <!-- Menyimpan path gambar lama untuk digunakan jika tidak mengganti gambar -->
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($image['image_path']); ?>">

            <button type="submit">Simpan Perubahan</button>
        </form>

        <div class="form-footer">
            <p>Ingin kembali ke galeri? <a href="manage_gallery.php">Kembali ke Galeri</a></p>
        </div>
    </div>
</body>
</html>
