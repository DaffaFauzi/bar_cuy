<?php
include 'includes/db.php';

// Ambil data layanan dari database
$stmt = $conn->prepare("SELECT * FROM services");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Barbershop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, rgba(255,87,34,0.8), rgba(33,150,243,0.8));
            padding: 20px;
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

        .back-button:hover {
            background-color: #1a252f;
        }

        .services-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .service-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            transition: transform 0.2s;
            cursor: pointer;
        }

        .service-card:hover {
            transform: scale(1.05);
        }

        .service-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .service-card h3 {
            margin: 0;
            padding: 10px;
            background-color: #2c3e50;
            color: #fff;
        }

        .service-card p {
            padding: 10px;
            font-size: 14px;
            color: #555;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #a9532a;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            position: relative;
        }

        .modal-content img {
            width: 80%;
            height: auto;
            max-height: 300px;
            border-radius: 8px;
        }

        .modal-content h3, .modal-content p {
            margin-top: 10px;
            color: #e2dcdc;
        }

        .modal-content ul {
            list-style-type: none;
            padding: 0;
            color: #f3ebeb;
            margin-top: 10px;
            text-align: left;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #2c3e50;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .close-btn:hover {
            background-color: #1a252f;
        }
    </style>
</head>
<body>