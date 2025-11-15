<?php

include(__DIR__ . '/../config.php');

// Only patients allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];
$available_slots = [];
$doctor_id = 0;

// Fetch doctor list
$doctors = mysqli_query($conn, "
    SELECT u.id AS user_id, u.username, d.id AS doctor_id, d.specialty 
    FROM users u
    JOIN doctors d ON u.id = d.user_id
    WHERE u.role='doctor'
");
?>



<!DOCTYPE html>
<html>
<head>
<title>Book Appointment</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-4" style="max-width:700px;">
    <div class="card p-4 shadow">

        <h3 class="text-primary mb-3">Book Appointment</h3>


        <!-- ==========================
             STEP 1 – SELECT DOCTOR + DATE
        =========================== -->
        <form method="post">

            <label><b>Select Doctor</b></label>
            <select name="doctor_user" class="form-select mb-3" required>
                <option value="">-- Select Doctor --</option>

                <?php 
                mysqli_data_seek($doctors, 0);
                while ($d = mysqli_fetch_assoc($doctors)) { ?>
                    
                <option value="<?= $d['user_id'] ?>"
                    <?= (isset($_POST['doctor_user']) && $_POST['doctor_user'] == $d['user_id']) ? 'selected' : '' ?>>
                    <?= $d['username'] ?> (<?= $d['specialty'] ?>)
                </option>

                <?php } ?>
            </select>

            <label><b>Select Date</b></label>
            <input type="date" 
                   name="appointment_date" 
                   value="<?= $_POST['appointment_date'] ?? '' ?>"
                   class="form-control mb-3" 
                   required>

            <button name="show_slots" class="btn btn-primary w-100">Show Available Slots</button>
        </form>




        <!-- ==========================
             STEP 2 – SHOW AVAILABLE SLOTS
        =========================== -->
        <?php
        if (isset($_POST['show_slots'])) {

            $selected_doctor = $_POST['doctor_user'];
            $selected_date = $_POST['appointment_date'];

            // Fetch doctor_id + username
            $docInfo = mysqli_query($conn, "
                SELECT d.id AS doctor_id, u.username
                FROM doctors d
                JOIN users u ON u.id = d.user_id
                WHERE u.id = $selected_doctor
            ");
            $dr = mysqli_fetch_assoc($docInfo);

            $doctor_id = $dr['doctor_id'];
            $doctor_username = $dr['username'];


            // Fetch availability
            $avail = mysqli_query($conn, "
                SELECT * FROM doctor_availability
                WHERE doctor_id = $doctor_id
                AND available_date = '$selected_date'
                AND is_active = 1
            ");

            while ($av = mysqli_fetch_assoc($avail)) {

                $start = strtotime("$selected_date ".$av["start_time"]);
                $end = strtotime("$selected_date ".$av["end_time"]);
                $duration = $av["slot_duration_minutes"];

                while ($start < $end) {
                    $available_slots[] = date("H:i", $start);
                    $start += ($duration * 60);
                }
            }

            // Remove already booked slots
            $booked = [];
            $booked_q = mysqli_query($conn, "
                SELECT appointment_time FROM appointments
                WHERE doctor_id = $doctor_id
                AND appointment_date = '$selected_date'
            ");
            while ($b = mysqli_fetch_assoc($booked_q)) {
                $booked[] = $b['appointment_time'];
            }

            $available_slots = array_diff($available_slots, $booked);
        ?>

        <hr>
        <h5 class="mt-3 text-success">Available Slots</h5>


        <!-- NO SLOTS -->
        <?php if (empty($available_slots)) { ?>
            <div class="alert alert-warning mt-3">
                No slots available for this date.
            </div>

        <?php } else { ?>


        <!-- ==========================
             STEP 3 – SELECT SLOT + SYMPTOMS
        =========================== -->
        <form method="post">

            <input type="hidden" name="doctor_user" value="<?= $selected_doctor ?>">
            <input type="hidden" name="appointment_date" value="<?= $selected_date ?>">

            <label><b>Select Time Slot</b></label>
            <select name="appointment_time" class="form-select mb-3" required>
                <option value="">-- Select Time --</option>

                <?php foreach ($available_slots as $slot) { ?>
                    <option value="<?= $slot ?>">
                        <?= date("g:i A", strtotime($slot)) ?>
                    </option>
                <?php } ?>

            </select>

            <label><b>Symptoms</b></label>
            <textarea name="symptoms" class="form-control mb-3" required></textarea>

            <button name="book" class="btn btn-success w-100">Book Appointment</button>
        </form>

        <?php }} ?> <!-- END SLOT SECTION -->



        <!-- ==========================
             STEP 4 – SAVE APPOINTMENT
        =========================== -->
        <?php
        if (isset($_POST["book"])) {

            $doctor_user = $_POST["doctor_user"];
            $appointment_date = $_POST["appointment_date"];
            $appointment_time = $_POST["appointment_time"];
            $symptoms = $_POST["symptoms"];

            // Fetch doctor_id again
            $info = mysqli_query($conn, "
                SELECT d.id AS doctor_id, u.username
                FROM doctors d 
                JOIN users u ON d.user_id = u.id
                WHERE u.id = $doctor_user
            ");
            $dr = mysqli_fetch_assoc($info);

            $doctor_id = $dr["doctor_id"];
            $doctor_username = $dr["username"];

            // Insert appointment
            mysqli_query($conn, "
                INSERT INTO appointments
                (patient_id, symptoms, doctor_assigned, appointment_date, appointment_time, doctor_id, status)
                VALUES
                ($patient_id, '$symptoms', '$doctor_username', '$appointment_date', '$appointment_time', $doctor_id, 'booked')
            ");

            echo "<div class='alert alert-success mt-3'>Appointment booked successfully!</div>";
        }
        ?>

    </div>
</div>

</body>
</html>
