<?php
session_start();
include '../includes/db.php';

// Ensure only receptionists can process reservations
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'receptionist') {
    header("Location: ../users/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $reservation_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'accept') {
        $sql = "UPDATE reservations SET status = 'confirmed' WHERE id = ?";
    } elseif ($action == 'reject') {
        $sql = "DELETE FROM reservations WHERE id = ?"; // Remove rejected reservation
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);

    if ($stmt->execute()) {
        echo "<script>alert('Reservation updated successfully!'); window.location='receptionist_dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
