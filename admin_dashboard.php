<?php
session_start();
include("config.php");

// Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Counts for dashboard
$doctorCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role='doctor'"))['c'];
$patientCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role='patient'"))['c'];
$appointmentCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM appointments"))['c'];

// Get all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

// Get doctors table details
$doctors = mysqli_query($conn, "
    SELECT d.id AS doctor_id, u.username, d.specialty, d.phone 
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    ORDER BY d.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard - Smart Hospital</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body {
    background: #f0f7f7;
    font-family: Arial;
}

.topbar {
    background: #00706f;
    color: white;
    padding: 15px;
    font-size: 20px;
    font-weight: bold;
}

.card {
    border-radius: 12px;
    background: white;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    Smart Hospital — Admin
    <span style="float:right;">Logged in as: <b><?= $username ?></b> | 
        <a href="logout.php" class="text-white">Logout</a>
    </span>
</div>

<div class="container mt-4">

    <!-- STATS ROW -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card p-3">
                <h5>Doctors</h5>
                <h3><?= $doctorCount ?></h3>
                <p>Profiles in system</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h5>Patients</h5>
                <h3><?= $patientCount ?></h3>
                <p>Registered patients</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h5>Appointments</h5>
                <h3><?= $appointmentCount ?></h3>
                <p>Total bookings</p>
            </div>
        </div>

        <!-- QUICK ACTIONS DROPDOWN -->
        <div class="col-md-3">
            <div class="card p-3">
                <h5>Quick Actions</h5>

                <div class="btn-group mt-2">
                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        Manage
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="add_doctor.php">➕ Add Doctor</a></li>
                        <li><a class="dropdown-item" href="view_users.php">👤 View Users</a></li>
                    </ul>
                </div>

            </div>
        </div>

    </div>

    <!-- USERS LIST -->
    <div class="card p-3 mt-4">
        <h4>All Users</h4>

        <div style="max-height:300px; overflow-y:auto;">
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($u = mysqli_fetch_assoc($users)) { ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= $u['username'] ?></td>
                        <td><?= $u['email'] ?></td>
                        <td><?= $u['role'] ?></td>
                        <td>
                            <?php if ($u['role'] != 'admin') { ?>
                                <a href="delete_user.php?id=<?= $u['id'] ?>" 
                                   class="btn btn-danger btn-sm">Delete</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

    <!-- DOCTORS LIST -->
    <div class="card p-3 mt-4">
        <h4>Doctors</h4>

        <div style="max-height:300px; overflow-y:auto;">
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Specialty</th>
                        <th>Phone</th>
                        <th>Edit</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($d = mysqli_fetch_assoc($doctors)) { ?>
                    <tr>
                        <td><?= $d['doctor_id'] ?></td>
                        <td><?= $d['username'] ?></td>
                        <td><?= $d['specialty'] ?></td>
                        <td><?= $d['phone'] ?></td>
                        <td><a href="edit_doctor.php?id=<?= $d['doctor_id'] ?>" class="btn btn-primary btn-sm">Edit</a></td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>


    <!-- QUICK LINKS DROPDOWN -->
    <div class="card p-3 mt-4">
        <h5>Quick Links</h5>

        <div class="btn-group mt-2">
            <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                Open Page
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="appointment_test.php">📅 Book Slot (Test)</a></li>
                <li><a class="dropdown-item" href="doctor_availability.php">📌 Doctor Availability</a></li>
                <li><a class="dropdown-item" href="doctor_prescription.php">💊 Doctor Prescription</a></li>
            </ul>
        </div>
    </div>

</div>

<!-- BOOTSTRAP JS REQUIRED FOR DROPDOWN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
