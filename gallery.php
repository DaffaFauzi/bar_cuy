<?php
session_start();
include 'includes/db.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Proses hapus gambar
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("SELECT image_path FROM gallery WHERE id = :id");
        $stmt->execute(['id' => $delete_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $image_path = '../' . $item['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = :id");
            $stmt->execute(['id' => $delete_id]);
            $success = "Gambar berhasil dihapus!";
        } else {
            $error = "Gambar tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Ambil semua data gambar
try {
    $stmt = $conn->prepare("SELECT * FROM gallery ORDER BY created_at DESC");
    $stmt->execute();
    $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $gallery_items = [];
    $error = "Gagal mengambil data galeri: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Gambar</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="container">
        <h1>Galeri Gambar</h1>
        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php elseif (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="gallery">
            <?php if (!empty($gallery_items)): ?>
                <?php foreach ($gallery_items as $item): ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($item['caption']); ?>" 
                             onerror="this.src='images/default.jpg';">
                        <?php if (!empty($item['caption'])): ?>
                            <p><?php echo htmlspecialchars($item['caption']); ?></p>
                        <?php endif; ?>
                        <a href="?delete_id=<?php echo $item['id']; ?>" class="delete-button" onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')">Hapus</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada gambar dalam galeri.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
