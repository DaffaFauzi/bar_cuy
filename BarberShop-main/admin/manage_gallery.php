<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Proses tambah gambar baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = '../images/';
        $target_file = $upload_dir . basename($image_name);
        $caption = $_POST['caption'] ?? '';

        // Pindahkan file yang diupload
        if (move_uploaded_file($tmp_name, $target_file)) {
            $stmt = $conn->prepare("INSERT INTO gallery (image_path, caption) VALUES (:image_path, :caption)");
            $stmt->execute(['image_path' => $target_file, 'caption' => $caption]);
            $success = "Image uploaded successfully!";
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "No file uploaded or file upload error.";
    }
}

// Fetch semua data galeri
$stmt = $conn->prepare("SELECT * FROM gallery");
$stmt->execute();
$gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/admin.css">
    <title>Manage Gallery</title>
</head>
<body>
    <div class="container">
        <h1>Manage Gallery</h1>
        
        <!-- Form Tambah Gambar Baru -->
        <form method="POST" enctype="multipart/form-data">
            <h2>Add New Image</h2>
            <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
            <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <input type="file" name="image" required>
            <input type="text" name="caption" placeholder="Caption (optional)">
            <button type="submit">Upload Image</button>
        </form>

        <!-- List Galeri -->
        <h2>Gallery Items</h2>
        <div class="gallery">
            <?php foreach ($gallery_items as $item): ?>
                <div class="gallery-item">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['caption']); ?>">
                    <?php if (!empty($item['caption'])): ?>
                        <p><?php echo htmlspecialchars($item['caption']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
