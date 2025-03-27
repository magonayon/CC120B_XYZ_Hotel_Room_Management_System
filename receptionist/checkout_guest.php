<?php
session_start();
include '../includes/db.php';

// Ensure only receptionists can check out guests
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'receptionist') {
    header("Location: ../users/login.php");
    exit();
}

// Fetch guests who are checked in
$checked_in_guests = $conn->query("
    SELECT res.id, r.room_number, COALESCE(u.full_name, res.guest_name) AS guest_name 
    FROM reservations res
    JOIN rooms r ON res.room_id = r.id
    LEFT JOIN users u ON res.user_id = u.id
    WHERE res.status = 'checked_in'
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST['reservation_id'];

    // Get room_id of checked-out guest
    $get_room_sql = "SELECT room_id FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($get_room_sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $stmt->bind_result($room_id);
    $stmt->fetch();
    $stmt->close();

    if ($room_id) {
        // Update reservation status to checked out
        $sql = "UPDATE reservations SET status = 'checked_out' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reservation_id);
        if ($stmt->execute()) {
            // Mark room as available
            $update_sql = "UPDATE rooms SET status = 'available' WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $room_id);
            $stmt->execute();

            echo "<script>alert('Guest checked out successfully!'); window.location='receptionist_dashboard.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Out Guest</title>
</head>
<body>
    <h2>Check Out Guest</h2>
    <form method="post">
        <select name="reservation_id" required>
            <option value="">Select Guest</option>
            <?php while ($row = $checked_in_guests->fetch_assoc()) { ?>
                <option value="<?= $row['id']; ?>">Room <?= $row['room_number']; ?> - <?= htmlspecialchars($row['guest_name']); ?></option>
            <?php } ?>
        </select>
        <button type="submit">Check Out</button>
    </form>
    <br>
    <a href="receptionist_dashboard.php">Back</a>
</body>
</html>
