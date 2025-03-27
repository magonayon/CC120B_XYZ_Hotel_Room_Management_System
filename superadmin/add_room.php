<?php
session_start();
include '../includes/db.php';

// Ensure only superadmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin') {
    header("Location: ../users/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $sql = "INSERT INTO rooms (room_number, type, price, status) VALUES (?, ?, ?, 'available')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $room_number, $type, $price);

    if ($stmt->execute()) {
        echo "<script>alert('Room added successfully!'); window.location='manage_rooms.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Get all room numbers that are already used
$used_rooms = [];
$result = $conn->query("SELECT room_number FROM rooms");
while ($row = $result->fetch_assoc()) {
    $used_rooms[] = $row['room_number'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room</title>
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
        select, button {
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
        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-add:hover {
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
    <h2>🏨 Add New Room</h2>
    <form method="post">
        <!-- Room Number Selection (Only Unused Rooms) -->
        <label for="room_number">Room Number:</label>
        <select name="room_number" required>
            <option value="">Select Room Number</option>
            <?php
            $room_numbers = array_merge(range(101, 120), range(201, 220), [301, 310]);
            foreach ($room_numbers as $number) {
                if (!in_array($number, $used_rooms)) { // Only show unused rooms
                    echo "<option value='$number'>$number</option>";
                }
            }
            ?>
        </select>

        <!-- Room Type Selection -->
        <label for="type">Room Type:</label>
        <select name="type" required>
            <option value="">Select Room Type</option>
            <option value="Single Room">Single Room</option>
            <option value="Standard Double Room">Standard Double Room</option>
            <option value="Standard Twin Room">Standard Twin Room</option>
            <option value="Deluxe Double Room">Deluxe Double Room</option>
            <option value="Studio Room or Apartment">Studio Room or Apartment</option>
            <option value="Junior Suite">Junior Suite</option>
            <option value="Executive Suite">Executive Suite</option>
            <option value="Presidential Suite">Presidential Suite</option>
        </select>

        <!-- Room Price Selection -->
        <label for="price">Price:</label>
        <select name="price" required>
            <option value="">Select Price</option>
            <option value="50">₱50</option>
            <option value="100">₱100</option>
            <option value="500">₱500</option>
            <option value="1000">₱1,000</option>
            <option value="2000">₱2,000</option>
            <option value="5000">₱5,000</option>
            <option value="10000">₱10,000</option>
        </select>

        <button type="submit" class="btn btn-add">✅ Add Room</button>
    </form>

    <a href="manage_rooms.php" class="btn btn-back">⬅ Back to Manage Rooms</a>
</div>

</body>
</html>
