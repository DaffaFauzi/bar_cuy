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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #6a11cb, #2575fc);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 40px;
        }
        .menu {
            display: flex;
            gap: 20px;
        }
        .menu-item {
            text-align: center;
            text-decoration: none;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px 40px;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .menu-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.2);
        }
        .menu-item span {
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
    <title>Admin Panel</title>
</head>
<body>
    <h1>Welcome to Admin Panel</h1>
    <div class="menu">
        <a href="manage_services.php" class="menu-item">
            <span>Manage Services</span>
        </a>
        <a href="manage_gallery.php" class="menu-item">
            <span>Manage Gallery</span>
        </a>
        <a href="logout.php" class="menu-item">
            <span>Logout</span>
        </a>
    </div>
</body>
</html>
