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

    try {
        // Ambil path gambar dari database
        $stmt = $conn->prepare("SELECT image_path FROM gallery WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Hapus gambar dari sistem file
            unlink('../' . $image['image_path']);

            // Hapus gambar dari database
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = :id");
            $stmt->execute(['id' => $id]);

            header("Location: manage_gallery.php");
            exit();
        } else {
            die("Gambar tidak ditemukan!");
        }
    } catch (PDOException $e) {
        die("Gagal menghapus gambar: " . $e->getMessage());
    }
} else {
    header("Location: manage_gallery.php");
    exit();
}
?>
