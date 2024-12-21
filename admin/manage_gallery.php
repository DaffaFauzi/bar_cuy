<?php
session_start();
include '../includes/db.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: gallery.php");
    exit();
}

// Proses upload gambar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image'];
    $caption = $_POST['caption'] ?? '';

    // Folder tujuan
    $upload_dir = '../head_cuy/';
    $image_path = $upload_dir . basename($image['name']);
    $db_image_path = 'head_cuy/' . basename($image['name']);

    // Proses upload
    if ($image['error'] == 0) {
        if (move_uploaded_file($image['tmp_name'], $image_path)) {
            try {
                // Simpan data gambar ke database
                $stmt = $conn->prepare("INSERT INTO gallery (image_path, caption) VALUES (:image_path, :caption)");
                $stmt->execute(['image_path' => $db_image_path, 'caption' => $caption]);
                $success = "Gambar berhasil diupload!";
            } catch (PDOException $e) {
                $error = "Terjadi kesalahan saat menyimpan ke database: " . $e->getMessage();
            }
        } else {
            $error = "Gagal mengupload gambar.";
        }
    } else {
        $error = "Terjadi kesalahan saat mengupload gambar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Gambar - Admin</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<a href="index.php" class="back-button">Kembali ke Galeri</a>
    <div class="container">
        <h1>Tambah Gambar Baru</h1>
        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php elseif (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="image" required>
            <input type="text" name="caption" placeholder="Keterangan (opsional)">
            <button type="submit">Upload Gambar</button>
        </form>
    </div>
</body>
</html>
