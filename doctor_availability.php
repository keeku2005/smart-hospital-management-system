<?php
include('config.php');


// Check doctor login
if (!isset($_SESSION['role']) || $_SESSION['role'] != "doctor") {
    header("Location: login.php");
    exit;
}

// Get doctor_id from doctors table using username
$username = $_SESSION['username'];

$doc = mysqli_query($conn, "
    SELECT d.id AS doctor_id 
    FROM doctors d 
    JOIN users u ON u.id = d.user_id 
    WHERE u.username = '$username'
");

$doctorData = mysqli_fetch_assoc($doc);
$doctor_id = $doctorData['doctor_id'];

$message = "";

// Adding availability
if (isset($_POST['save'])) {

    $date = $_POST['available_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $slot = $_POST['slot_duration'];
    
    $insert = mysqli_query($conn, "
        INSERT INTO doctor_availability 
        (doctor_id, available_date, start_time, end_time, slot_duration_minutes, is_active)
        VALUES ('$doctor_id', '$date', '$start', '$end', '$slot', 1)
    ");

    if ($insert) {
        $message = "<div class='alert alert-success'>Availability Added Successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: Could not save availability.</div>";
    }
}

// Fetch all availability for logged doctor
$availability = mysqli_query($conn, "
    SELECT * FROM doctor_availability 
    WHERE doctor_id = '$doctor_id'
    ORDER BY available_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Availability</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-4" style="max-width:800px;">
    <div class="card p-4 shadow">
        <h3 class="text-primary mb-3">Doctor Availability</h3>

        <?= $message ?>

        <form method="post" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Available Date</label>
                <input type="date" name="available_date" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Slot Duration (minutes)</label>
                <input type="number" name="slot_duration" class="form-control" value="15" required>
            </div>

            <div class="col-md-12">
                <button class="btn btn-success w-100" name="save">Save Availability</button>
            </div>
        </form>

        <hr>

        <h5>Your Added Availability</h5>

        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Slot (min)</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($availability)) { ?>
                <tr>
                    <td><?= $row['available_date'] ?></td>
                    <td><?= $row['start_time'] ?></td>
                    <td><?= $row['end_time'] ?></td>
                    <td><?= $row['slot_duration_minutes'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
