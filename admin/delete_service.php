<?php
session_start();
include '../includes/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Hapus layanan berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Hapus gambar dari server jika ada
        $stmt = $conn->prepare("SELECT image FROM services WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($service && !empty($service['image'])) {
            unlink('../uploads/' . $service['image']);
        }

        // Hapus layanan dari database
        $stmt = $conn->prepare("DELETE FROM services WHERE id = :id");
        $stmt->execute(['id' => $id]);

        header("Location: manage_services.php");
        exit();
    } catch (Exception $e) {
        echo "Gagal menghapus layanan: " . $e->getMessage();
    }
}
?>
