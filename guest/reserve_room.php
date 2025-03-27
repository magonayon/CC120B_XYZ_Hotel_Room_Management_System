<?php
session_start();
include '../includes/db.php';

// Ensure only logged-in users can reserve
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the room is still available
    $check_sql = "SELECT * FROM rooms WHERE id = ? AND status = 'available'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Insert reservation into database
        $insert_sql = "INSERT INTO reservations (user_id, room_id, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $room_id);
        if ($stmt->execute()) {
            // Update room status to "reserved"
            $update_sql = "UPDATE rooms SET status = 'reserved' WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $room_id);
            $stmt->execute();

            echo "<script>alert('✅ Room reserved successfully!'); window.location='available_rooms.php';</script>";
        } else {
            echo "<script>alert('❌ Error: Unable to reserve the room.'); window.location='available_rooms.php';</script>";
        }
    } else {
        echo "<script>alert('⚠️ This room is no longer available.'); window.location='available_rooms.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserve Room</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            text-align: center;
            margin: 40px;
        }
        .container {
            width: 50%;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
        }
        .message {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 18px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Room Reservation</h2>
        <p class="message">Processing your reservation... Please wait.</p>
        <a href="available_rooms.php" class="btn btn-primary">🔙 Back to Available Rooms</a>
    </div>
</body>
</html>
