<?php
session_start();
include '../includes/db.php';

// Ensure only logged-in users can book
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

// Fetch available rooms (status = 'available')
$sql = "SELECT * FROM rooms WHERE status = 'available'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Rooms</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            text-align: center;
            margin: 40px;
        }
        .container {
            width: 80%;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s ease;
        }
        .btn-primary {
            background-color: #28a745;
            color: white;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .btn-back {
            background-color: #dc3545;
            color: white;
            margin-top: 20px;
            display: inline-block;
        }
        .btn-back:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🏨 Available Rooms</h2>
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['room_number']; ?></td>
                        <td><?= $row['type']; ?></td>
                        <td>₱<?= number_format($row['price'], 2); ?></td>
                        <td><a href="reserve_room.php?room_id=<?= $row['id']; ?>" class="btn btn-primary">Reserve</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <a href="../guest/guest_dashboard.php" class="btn btn-back">🔙 Back to Dashboard</a>
    </div>
</body>
</html>
