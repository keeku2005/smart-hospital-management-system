<?php
session_start();
include('config.php');

// If not logged in → go to login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

// If ADMIN → redirect OUTSIDE layout.php
if ($role == "admin") {
    header("Location: admin_dashboard.php");
    exit;
}

// Default requested page
$page = $_GET['page'] ?? "dashboard";

// Allowed patient/doctor pages
$allowed_pages = [
    "dashboard",
    "appointment",
    "feedback",
    "prescription_view",
    "doctor_dashboard",
    "doctor_availability",
    "doctor_prescription"
];

// Prevent invalid page access
if (!in_array($page, $allowed_pages)) {
    $page = "dashboard";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Smart Hospital App</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
/* Modern Teal Theme */
:root {
    --primary: #006d77;
    --primary-dark: #004f56;
    --background: #e0fbfc;
    --white: #ffffff;
}

/* Background */
body {
    background: var(--background);
    font-family: Arial, sans-serif;
}

/* Sidebar */
.sidebar {
    width: 220px;
    height: 100vh;
    background: var(--primary-dark);
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 60px;
    color: var(--white);
}
.sidebar a {
    display: block;
    padding: 15px;
    color: var(--white);
    text-decoration: none;
    font-size: 17px;
}
.sidebar a:hover {
    background: var(--primary);
}

/* Top bar */
.topbar {
    height: 60px;
    background: var(--primary);
    color: white;
    padding: 15px;
    padding-left: 250px;
    font-size: 20px;
    font-weight: bold;
}

/* Main content */
.content {
    margin-left: 250px;
    padding: 25px;
}
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    Smart Hospital — Logged in as <?= $username ?> (<?= $role ?>)
</div>

<!-- SIDEBAR -->
<div class="sidebar">

    <?php if ($role == "patient") { ?>
        <a href="layout.php?page=dashboard">🏠 Dashboard</a>
        <a href="layout.php?page=appointment">📅 Book Appointment</a>
        <a href="layout.php?page=feedback">⭐ Feedback</a>
        <a href="logout.php">🚪 Logout</a>
    <?php } ?>

    <?php if ($role == "doctor") { ?>
        <a href="layout.php?page=doctor_dashboard">🏥 Doctor Dashboard</a>
        <a href="layout.php?page=doctor_availability">📌 Availability</a>
        <a href="logout.php">🚪 Logout</a>
    <?php } ?>

</div>

<!-- MAIN CONTENT -->
<div class="content">
    <?php include("pages/" . $page . ".php"); ?>
</div>

</body>
</html>
