<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/admin.css">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Welcome to Admin Panel</h1>
    <ul>
        <li><a href="manage_services.php">Manage Services</a></li>
        <li><a href="manage_gallery.php">Manage Gallery</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
