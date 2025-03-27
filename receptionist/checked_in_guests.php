<?php
session_start();
include '../includes/db.php';

// Ensure only receptionists can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'receptionist') {
    header("Location: ../users/login.php");
    exit();
}

// Fetch all checked-in guests
$checked_in_guests = $conn->query("
    SELECT res.id, r.room_number, r.type, u.full_name AS guest_name, res.status 
    FROM reservations res
    JOIN rooms r ON res.room_id = r.id
    JOIN users u ON res.user_id = u.id
    WHERE res.status = 'checked-in'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checked-in Guests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 40px;
            text-align: center;
        }
        h2 {
            color: #343a40;
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 18px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            background-color: #28a745;
            color: white;
            border-radius: 6px;
            transition: 0.3s ease;
        }
        .back-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h2>📋 Checked-in Guests</h2>
    
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Guest Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $checked_in_guests->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['room_number']; ?></td>
                    <td><?= $row['type']; ?></td>
                    <td><?= htmlspecialchars($row['guest_name']); ?></td>
                    <td style="color: green; font-weight: bold;"><?= ucfirst($row['status']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="receptionist_dashboard.php" class="back-btn">🔙 Back to Receptionist Dashboard</a>
</body>
</html>
