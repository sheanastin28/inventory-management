<?php
session_start();
include '../lib/db/db.php';
$err = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $cont_num = $_POST['cont_num'];
    $address  = $_POST['address'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $err = "An account with this email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (name, email, cont_num, address, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $cont_num, $address, $hashed_password);


        if ($stmt->execute()) {
            $success = "Account created successfully! Redirecting to login...";
            header("refresh:3;url=login.php");
        } else {
            $err = "Registration failed. Try again.";
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      display: flex;
      height: 100vh;
      background: #151b2e;
      color: #fff;
    }

    .container {
      display: flex;
      flex: 1;
    }

    .left-panel {
      flex: 2;
      background-image: url('../.assets/kitchen.jpg');
      background-size: 100% 100%;
      background-repeat: no-repeat;
      background-position: center;
      background-color: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      border-top-right-radius: 12px;
      border-bottom-right-radius: 12px;
    }

    .right-panel {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 60px;
      background-color: #10162f;
      border-top-left-radius: 12px;
      border-bottom-left-radius: 12px;
    }

    .right-panel img {
      height: 100px;
      width: 100px;
    }

    .logo {
      text-align: center;
      margin-bottom: 20px;
    }

    h1 {
      font-size: 2rem;
      margin-bottom: 10px;
      text-align: center;
    }

    input[type="text"], input[type="email"], input[type="password"] {
      width: 100%;
      height: 10px;
      padding: 14px;
      margin: 10px 0;
      border-radius: 8px;
      border: none;
      background: #fff;
      color: #000;
      font-size: 1rem;
    }

    .btn {
      width: 100%;
      background: #99b6fd;
      color: white;
      padding: 14px;
      font-size: 1rem;
      margin-top: 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn:hover {
      background: #a187f8;
    }

    .link {
      margin-top: 15px;
      text-align: center;
      font-size: 0.95rem;
    }

    .link a {
      color: #8ecbff;
      text-decoration: none;
    }

    .link a:hover {
      text-decoration: underline;
    }

    .error {
      color: #ff6b6b;
      margin-top: 10px;
    }

    .success {
      color: #66ff99;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <!-- Background image already defined -->
    </div>

    <div class="right-panel">
      <div class="logo">
        <img src="../.assets/lightlogo.png" alt="Logo" />
      </div>
      <form method="POST">
        <h1>Create an Account</h1>
        <input type="text" name="name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="cont_num" placeholder="Contact Number" required />
        <input type="text" name="address" placeholder="Address" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" class="btn">Create Account</button>

        <?php if (!empty($err)): ?>
          <div class="error"><?php echo $err; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
          <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="link">
          <br> Already have an account? <br> <a href="login.php">Login</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
