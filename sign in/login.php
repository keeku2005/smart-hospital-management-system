<?php
include('config.php');
session_start();

$notice = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($row['password'] === $password || md5($password) === $row['password']) {

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            header("Location: layout.php");
            exit;

        } else {
            $notice = "<div class='alert alert-danger text-center'>Wrong Password!</div>";
        }

    } else {
        $notice = "<div class='alert alert-danger text-center'>User Not Found!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Smart Hospital - Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
      background-image: url('/smart_hospital/hospital.jpg');
 /* BEAUTIFUL HOSPITAL BACKGROUND */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    /* Soft blur overlay */
    backdrop-filter: blur(3px);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Overlay tint */
.bg-overlay {
    background: rgba(200, 219, 221, 0.45);
    position: fixed;
    inset: 0;
    backdrop-filter: blur(2px);
}

/* Login Card */
.login-card {
    background: rgba(255, 255, 255, 0.92);
    padding: 35px;
    border-radius: 15px;
    width: 380px;
    box-shadow: 0px 10px 25px rgba(0,0,0,0.25);
    position: relative;
    z-index: 2;
}

.login-card h3 {
    text-align: center;
    color: #0a989dff;
    margin-bottom: 20px;
    font-weight: bold;
}

.btn-primary {
    background: #ee8666ff;
    border-color: #00838F;
    font-weight: bold;
    border-radius: 8px;
}

.btn-primary:hover {
    background: #006064;
}
</style>
</head>

<body>

<div class="bg-overlay"></div>

<div class="login-card">

    <h3>Smart Hospital Login</h3>

    <?= $notice ?>

    <form method="post">
        <label><b>Email</b></label>
        <input type="email" name="email" class="form-control mb-3" required>

        <label><b>Password</b></label>
        <input type="password" name="password" class="form-control mb-3" required>

        <button class="btn btn-primary w-100" name="login">Login</button>
    </form>

    <p class="text-center mt-3">
        <a href="register.php" class="text-decoration-none text-dark">
            New user? Register here
        </a>
    </p>

</div>

</body>
</html>
