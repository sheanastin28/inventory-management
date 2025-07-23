<?php
session_start();
include '../lib/db/db.php';
$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["name"] = $name;
            header("Location: cabstone_site.php");
            exit;
        } else {
            $err = "Invalid email or password.";
        }
    } else {
        $err = "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
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

    .logo {
      text-align: center;
      margin-bottom: 30px;
    }

    .logo img {
      height: 100px;
      width: 100px;
    }

    h2 {
      font-size: 2rem;
      margin-bottom: 20px;
      text-align: center;
    }

    input[type="email"], input[type="password"] {
      width: 100%;
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

    .left-panel p {
      color: #000;
      font-size: 1.5rem;
      background-color: rgba(255, 255, 255, 0.7);
      padding: 10px 20px;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <!-- Optional left panel content -->
    </div>

    <div class="right-panel">
      <div class="logo">
        <img src="../.assets/lightlogo.png" alt="Logo" />
      </div>
      <form method="POST">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" class="btn">Login</button>

        <?php if (!empty($err)): ?>
          <div class="error"><?php echo $err; ?></div>
        <?php endif; ?>

        <div class="link">
          <br> Don't have an account? <br> <a href="register.php">Create</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
