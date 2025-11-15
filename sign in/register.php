<?php include('config.php'); ?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Smart Hospital - Register</title>
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 420px;">
    <div class="card p-4 shadow">

        <h3 class="text-center text-success mb-3">Patient Registration</h3>

        <form method="post">

            <label>Full Name</label>
            <input type="text" name="username" class="form-control mb-3" required>

            <label>Email</label>
            <input type="email" name="email" class="form-control mb-3" required>

            <label>Password</label>
            <input type="password" name="password" class="form-control mb-3" required>

            <button class="btn btn-success w-100" name="register">Register</button>
        </form>

        <?php
        if (isset($_POST['register'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = md5($_POST['password']);

            $sql = "INSERT INTO users (username, email, password, role)
                    VALUES ('$username', '$email', '$password', 'patient')";

            if (mysqli_query($conn, $sql)) {
                echo "<div class='alert alert-success mt-3'>Registered Successfully!</div>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
        ?>

        <p class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Already have an account? Login</a>
        </p>
    </div>
</div>

</body>
</html>
