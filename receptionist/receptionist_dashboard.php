<?php
session_start();
include '../includes/db.php';

// Ensure only receptionists can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'receptionist') {
    header("Location: ../users/login.php");
    exit();
}

// Fetch reservations with different statuses
$pending_reservations = $conn->query("
    SELECT res.id, u.full_name, r.room_number, r.type 
    FROM reservations res
    JOIN rooms r ON res.room_id = r.id
    JOIN users u ON res.user_id = u.id
    WHERE res.status = 'pending'
");

$confirmed_reservations = $conn->query("
    SELECT res.id, u.full_name, r.room_number, r.type 
    FROM reservations res
    JOIN rooms r ON res.room_id = r.id
    JOIN users u ON res.user_id = u.id
    WHERE res.status = 'confirmed'
");

$checked_in_guests = $conn->query("
    SELECT res.id, r.room_number, r.type, u.full_name AS guest_name 
    FROM reservations res
    JOIN rooms r ON res.room_id = r.id
    JOIN users u ON res.user_id = u.id
    WHERE res.status = 'checked-in'
");

$available_rooms = $conn->query("SELECT * FROM rooms WHERE status = 'available'");

// Handle reservation actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST['reservation_id'];
    if (isset($_POST['confirm_reservation'])) {
        $conn->query("UPDATE reservations SET status = 'confirmed' WHERE id = $reservation_id");
        echo "<script>alert('Reservation confirmed!'); window.location='receptionist_dashboard.php';</script>";
    } elseif (isset($_POST['check_in'])) {
        $conn->query("UPDATE reservations SET status = 'checked-in' WHERE id = $reservation_id");
        $conn->query("UPDATE rooms SET status = 'occupied' WHERE id = (SELECT room_id FROM reservations WHERE id = $reservation_id)");
        echo "<script>alert('Guest checked in successfully!'); window.location='receptionist_dashboard.php';</script>";
    } elseif (isset($_POST['check_out'])) {
        $conn->query("UPDATE reservations SET status = 'checked-out' WHERE id = $reservation_id");
        $conn->query("UPDATE rooms SET status = 'available' WHERE id = (SELECT room_id FROM reservations WHERE id = $reservation_id)");
        echo "<script>alert('Guest checked out successfully!'); window.location='receptionist_dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receptionist Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        h3 {
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        form {
            text-align: center;
            margin: 15px 0;
        }
        label {
            font-weight: bold;
        }
        select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        .btn {
            font-size: 16px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 20%;
        }
        .btn-confirm {

            background: #28a745;
        }
        .btn-checkin {
            background: #ffc107;
        }
        .btn-checkout {
            background: #dc3545;
        }
        .btn:hover {
            opacity: 0.9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .btn-link {
            display: block;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            background: #17a2b8;
            color: white;
            margin-top: 10px;
            border-radius: 5px;
        }
        .btn-link:hover {
            background: #117a8b;
        }
        .checked-in-btn {
    display: inline-block;
    padding: 12px 18px;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    background-color: #28a745; /* Green color */
    color: white;
    border-radius: 6px;
    transition: 0.3s ease;
    border: none;
}

.checked-in-btn:hover {
    background-color: #218838;
}

    </style>
</head>
<body>

<div class="container">
    <h2>🏨 Welcome to Receptionist Dashboard</h2>

    <!-- Accept Reservations -->
    <h3>📌 Pending Reservations</h3>
    <form method="post">
        <select name="reservation_id" required>
            <option value="">Select Reservation</option>
            <?php while ($row = $pending_reservations->fetch_assoc()) { ?>
                <option value="<?= $row['id']; ?>">Room <?= $row['room_number']; ?> - <?= htmlspecialchars($row['full_name']); ?> (<?= $row['type']; ?>)</option>
            <?php } ?>
        </select>
        <button type="submit" name="confirm_reservation" class="btn btn-confirm">Confirm Reservation</button>
    </form>

    <!-- Check-in -->
    <h3>📌 Confirm Check-in & Payment</h3>
    <form method="post">
        <select name="reservation_id" required>
            <option value="">Select Guest</option>
            <?php while ($row = $confirmed_reservations->fetch_assoc()) { ?>
                <option value="<?= $row['id']; ?>">Room <?= $row['room_number']; ?> - <?= htmlspecialchars($row['full_name']); ?> (<?= $row['type']; ?>)</option>
            <?php } ?>
        </select>
        <button type="submit" name="check_in" class="btn btn-checkin">Check In</button>
    </form>

    <!-- Check-out -->
    <h3>📌 Check-out Guest</h3>
    <form method="post">
        <select name="reservation_id" required>
            <option value="">Select Guest</option>
            <?php while ($row = $checked_in_guests->fetch_assoc()) { ?>
                <option value="<?= $row['id']; ?>">Room <?= $row['room_number']; ?> - <?= htmlspecialchars($row['guest_name']); ?> (<?= $row['type']; ?>)</option>
            <?php } ?>
        </select>
        <button type="submit" name="check_out" class="btn btn-checkout">Check Out</button>
    </form>

    <!-- View Available Rooms -->
    <h3>📌 Available Rooms</h3>
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Type</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $available_rooms->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['room_number']; ?></td>
                    <td><?= $row['type']; ?></td>
                    <td>₱<?= number_format($row['price'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <br>
<a href="checked_in_guests.php" class="checked-in-btn">📋 View Checked-in Guests</a>
<br><br>



    <a href="../users/logout.php" class="btn-link">Logout</a>
</div>

</body>
</html>
