<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8f9fa;
            padding: 50px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        h2 {
            color: #343a40;
            margin-bottom: 20px;
        }
        .btn {
            display: block;
            width: 94%;
            padding: 12px;
            margin: 10px 0;
            text-decoration: none;
            background: #007BFF;
            color: white;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 20px;
            }
            .btn {
                font-size: 16px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to XYZ Hotel Reservations</h2>
        <a href="users/login.php" class="btn">Login</a>
        <a href="users/register.php" class="btn">Register</a>
    </div>
</body>
</html>
