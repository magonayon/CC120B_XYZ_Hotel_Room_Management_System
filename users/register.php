<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $middle_initial = trim($_POST['middle_initial']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $cell_number = trim($_POST['cell_number']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = isset($_POST['role']) ? $_POST['role'] : 'guest';

    // Validate Cell Number (Must start with 09 & be 11 digits)
    if (!preg_match('/^09\d{9}$/', $cell_number)) {
        echo "<script>alert('Invalid phone number! Must start with 09 and be 11 digits.'); window.history.back();</script>";
        exit();
    }

    // Validate Email (Only Gmail, Yahoo, Outlook)
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|outlook\.com)$/', $email)) {
        echo "<script>alert('Invalid email! Only Gmail, Yahoo, and Outlook are allowed.'); window.history.back();</script>";
        exit();
    }

    // Concatenate Name Fields into full_name
    $full_name = $first_name . ' ' . strtoupper($middle_initial) . '. ' . $last_name;

    $sql = "INSERT INTO users (username, full_name, email, cell_number, password, role) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $full_name, $email, $cell_number, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please log in.'); window.location='login.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
            width: 95%;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Register</h2>
    <form method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Enter Username" required>
        </div>

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" placeholder="Enter First Name" required>
        </div>

        <div class="form-group">
            <label for="middle_initial">Middle Initial</label>
            <input type="text" name="middle_initial" placeholder="M" maxlength="1" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" placeholder="Enter Last Name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Enter Email" required>
        </div>

        <div class="form-group">
            <label for="cell_number">Cell Number</label>
            <input type="text" name="cell_number" placeholder="09XXXXXXXXX" pattern="09[0-9]{9}" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>
        </div>

        <div class="form-group">
            <label for="role">Select Role</label>
            <select name="role" required>
                <option value="user">Guest</option>
                <option value="receptionist">Receptionist</option>
            </select>
        </div>

        <button type="submit">Register</button>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </form>
</div>

</body>
</html>
