<?php
session_start();
include 'includes/db.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            background-size: 400% 400%;
            animation: gradientMove 8s ease infinite;
        }

        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .back-button {
            display: inline-block;
            margin: 20px;
            text-decoration: none;
            background-color: #6c63ff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #574b90;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            font-size: 2.5em;
            color: #4a4a4a;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .gallery-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .gallery-item {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid #eee;
        }

        .gallery-item p {
            font-size: 1.2em;
            color: #333;
            padding: 10px;
            margin: 0;
            text-align: center;
            background: #f9f9f9;
            border-top: 2px solid #eee;
        }

        /* Modal Style */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            margin: 10% auto;
            padding: 15px;
            background-color: #fff;
            border-radius: 15px;
            width: 40%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            animation: zoomIn 0.3s ease forwards;
        }

        .modal-content img {
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            border-radius: 10px;
        }

        .modal-content p {
            font-size: 1.2em;
            color: #333;
            margin: 10px 0;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #000;
            font-size: 1.5em;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: red;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.9);
            }
            to {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
<a href="index.html" class="back-button">Kembali ke Beranda</a>
<h1>Galeri Gambar</h1>
<div class="gallery-container">
    <?php if (!empty($gallery_items)): ?>
        <?php foreach ($gallery_items as $item): ?>
            <div class="gallery-item" onclick="openModal('<?php echo htmlspecialchars('uploads/' . $item['image_path']); ?>', '<?php echo htmlspecialchars($item['caption']); ?>', '<?php echo htmlspecialchars($item['description']); ?>')">
                <img src="<?php echo 'uploads/' . htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['caption']); ?>">
                <p><?php echo htmlspecialchars($item['caption']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; font-size: 1.2em; color: #666;">Belum ada gambar dalam galeri.</p>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Gambar Modal">
        <p id="modalCaption"></p>
        <p id="modalDescription" style="font-size: 1em; color: #666;"></p>
    </div>
</div>

<script>
    function openModal(imagePath, caption, description) {
        // Set gambar, caption, dan deskripsi pada modal
        document.getElementById('modalImage').src = imagePath;
        document.getElementById('modalCaption').textContent = caption;
        document.getElementById('modalDescription').textContent = description;
        // Tampilkan modal
        document.getElementById('myModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('myModal').style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('myModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
</body>
</html>
