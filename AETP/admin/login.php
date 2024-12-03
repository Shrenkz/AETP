<?php
session_start();

// Hardcoded admin credentials
$admin_username = "admin";
$admin_password = "password";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit(); 
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AETP Login</title>
    <link rel="icon" type="image/svg+xml" href="../svg/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <div class="container">
        <div class="logo-section">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </div>
            <h1>AETP Navigation Map</h1>
        </div>
        <div class="form-section">
            <div class="login-form">
                <h2>Welcome back!</h2>
                <p>Explore <span style="color: #1B8D4E;">AETP Navigation Map</span> as <span style="color: #FFA726;">administrator</span>.</p>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <button type="submit" class="btn">Login</button>
                </form>
                <?php if (isset($error)) { ?>
                    <p style="color: #e74c3c; text-align: center;"><?php echo $error; ?></p>
                <?php } ?>
                <p class="footer">AETP Navigation Map 2024</p>
            </div>
        </div>
    </div>
</body>

</html>