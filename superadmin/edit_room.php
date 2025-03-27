<?php
session_start();
include '../includes/db.php';

// Ensure only superadmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin') {
    header("Location: ../users/login.php");
    exit();
}

// Ensure a valid room ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_rooms.php");
    exit();
}

$room_id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM rooms WHERE id = $room_id");

if ($result->num_rows == 0) {
    echo "<script>alert('Room not found!'); window.location='manage_rooms.php';</script>";
    exit();
}

$room = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = trim($_POST['room_number']);
    $type = trim($_POST['type']);
    $price = floatval($_POST['price']);
    $status = $_POST['status'];

    $sql = "UPDATE rooms SET room_number = ?, type = ?, price = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $room_number, $type, $price, $status, $room_id);

    if ($stmt->execute()) {
        echo "<script>alert('Room updated successfully!'); window.location='manage_rooms.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-update {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-update:hover {
            background: #218838;
        }
        .btn-back {
            background: #007bff;
            color: white;
            display: block;
            margin-top: 10px;
        }
        .btn-back:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🛏 Edit Room</h2>
    <form method="post">
        <label for="room_number">Room Number</label>
        <input type="text" id="room_number" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>

        <label for="type">Room Type</label>
        <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($room['type']); ?>" required>

        <label for="price">Price ($)</label>
        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($room['price']); ?>" step="0.01" required>

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="available" <?php if ($room['status'] == 'available') echo 'selected'; ?>>Available</option>
            <option value="reserved" <?php if ($room['status'] == 'reserved') echo 'selected'; ?>>Reserved</option>
            <option value="occupied" <?php if ($room['status'] == 'occupied') echo 'selected'; ?>>Occupied</option>
        </select>

        <button type="submit" class="btn btn-update">✅ Update Room</button>
    </form>
    
    <a href="manage_rooms.php" class="btn btn-back">⬅ Back to Manage Rooms</a>
</div>

</body>
</html>
