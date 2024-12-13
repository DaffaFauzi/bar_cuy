<?php
include 'includes/db.php';

// Ambil data galeri dari database
$stmt = $conn->prepare("SELECT * FROM gallery");
$stmt->execute();
$gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="style.css">

    <style>
         body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, rgba(255,87,34,0.8), rgba(33,150,243,0.8));
        }

        .back-button {
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #2c3e50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        <?php
include 'includes/db.php';

// Ambil data galeri dari database
$stmt = $conn->prepare("SELECT * FROM gallery ORDER BY created_at DESC");
$stmt->execute();
$gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Gallery</title>
</head>
<body>
    <div class="container">
        <h1>Gallery</h1>
        <div class="gallery">
            <?php if (empty($gallery_items)): ?>
                <p>No images in the gallery yet. Please check back later!</p>
            <?php else: ?>
                <?php foreach ($gallery_items as $item): ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['caption']); ?>">
                        <?php if (!empty($item['caption'])): ?>
                            <p><?php echo htmlspecialchars($item['caption']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

        .back-button:hover {
            background-color: #1a252f;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .grid-item {
            border: 2px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .grid-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .grid-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        .grid-item p {
            padding: 10px;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 10;
            overflow-x: auto;
        }

        .modal-content {
            background: #ffffff;
            padding: 50px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .close-button {
            background: #2c3e50;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
        }

        .close-button:hover {
            background: #1a252f;
        }
    </style>
</head>
<body>
    <h1>Gallery</h1>
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
</body>
</html>


    <script>
        function goBack() {
            window.history.back();
        }

        function showModal(imageSrc, title, price) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalPrice').textContent = price;
            document.getElementById('imageModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
    </script>
</body>
</html>
