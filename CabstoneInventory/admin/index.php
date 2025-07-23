<?php
session_start();
include '../lib/db/db.php'; // Database connection

$secretKey = '6Lc99IcrAAAAACI7oJSCE2Lc1nR4qGwb5CFSI9W8';

if (isset($_POST['lgin'])) {
    $username = $_POST['usrnme'];
    $password = $_POST['psword'];
    $captchaResponse = $_POST['g-recaptcha-response'];

    // 1. Verify reCAPTCHA
    if (!$captchaResponse) {
        echo "<script>alert('Please complete the CAPTCHA.');</script>";
    } else {
        $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $response = file_get_contents($verifyUrl . '?secret=' . $secretKey . '&response=' . $captchaResponse);
        $responseData = json_decode($response);

        if ($responseData->success) {
            // 2. CAPTCHA passed — continue login
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        header("Location: ../admin/dashboard.php");
                        exit;
                    } elseif ($user['role'] === 'employee') {
                        header("Location: ../employee/dashboard.php");
                        exit;
                    } else {
                        echo "<script>alert('Unknown role.');</script>";
                    }
                } else {
                    echo "<script>alert('Incorrect password.');</script>";
                }
            } else {
                echo "<script>alert('User not found.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('CAPTCHA verification failed.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Form</title>
    <link rel="stylesheet" href="../lib/css/login.css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo">
            <h2>Cabstone Inventory System</h2>
        </div>
        <!-- Log In Form -->
        <form method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="usrnme" required />
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="psword" required />
            </div>
            <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="6Lc99IcrAAAAAIJm32_8nm-8oU338CQxjC8tyC4J"></div>
            </div>
            <button type="submit" name="lgin">Login</button>
        </form>
    </div>
</body>
</html>