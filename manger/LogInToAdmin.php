<?php
session_start();
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST["username"]);
    $password = $_POST["password"];

    // Check if the user is temporarily locked out
    $attempts_sql = "SELECT attempts, last_attempt FROM login_attempts WHERE username = ?";
    $attempts_stmt = $conn->prepare($attempts_sql);
    $attempts_stmt->bind_param("s", $username);
    $attempts_stmt->execute();
    $attempts_result = $attempts_stmt->get_result();
    $attempts_row = $attempts_result->fetch_assoc();

    if ($attempts_row) {
        $attempts = $attempts_row['attempts'];
        $last_attempt = strtotime($attempts_row['last_attempt']);
        $current_time = time();

        // Lockout period: 15 minutes
        if ($attempts >= 5 && ($current_time - $last_attempt) < 900) {
            echo "<p class='error-message'>Account locked due to multiple failed attempts. Please try again later.</p>";
            exit();
        }
    }

    $sql = "SELECT admin_Id, password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row["password"];
        //to build hash for the first time
        //echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;
        // for the hexjoker password  = jokerGolf2019!@!@ 
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Reset attempts on successful login
            $reset_sql = "DELETE FROM login_attempts WHERE username = ?";
            $reset_stmt = $conn->prepare($reset_sql);
            $reset_stmt->bind_param("s", $username);
            $reset_stmt->execute();

            $_SESSION['adminUserName'] = true;
            header("Location: index.php");
            exit();
        } else {
            // Log the failed attempt
            if ($attempts_row) {
                $update_attempts_sql = "UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE username = ?";
                $update_stmt = $conn->prepare($update_attempts_sql);
                $update_stmt->bind_param("s", $username);
            } else {
                $insert_attempts_sql = "INSERT INTO login_attempts (username, attempts) VALUES (?, 1)";
                $insert_stmt = $conn->prepare($insert_attempts_sql);
                $insert_stmt->bind_param("s", $username);
            }

            if (isset($update_stmt)) {
                $update_stmt->execute();
            } else {
                $insert_stmt->execute();
            }

            echo "<p class='error-message'>Invalid username or password.</p>";
        }
    } else {
        echo "<p class='error-message'>Invalid username or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Church Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            margin-bottom: 30px;
        }

        .login-header i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
            display: block;
        }

        h2 {
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .error-message {
            color: #e74c3c;
            background: #fdf2f2;
            border: 1px solid #fecaca;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-message::before {
            content: '⚠️';
            margin-right: 8px;
        }

        .footer-text {
            margin-top: 30px;
            color: #999;
            font-size: 0.8rem;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }

        /* Animation for form appearance */
        .login-container {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h2>Admin Login</h2>
        <p class="subtitle">Church Management System</p>
    </div>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" required placeholder="Enter your username">
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
        </div>

        <button type="submit" class="login-btn">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>
    
    <p class="footer-text">© 2024 Church Management System</p>
</div>

</body>
</html>
