<?php
// Load DB config
include(__DIR__ . '/../config.php');

// User is validated from layout.php
$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['username'];
?>

<div class="card p-4 shadow mb-4">
    <h3 class="text-primary">Patient Dashboard</h3>

    <p class="mt-2">
        Welcome, <b><?= htmlspecialchars($patient_name) ?></b>.  
        Your health records and appointments are shown below.
    </p>

    <!-- Feedback button removed -->
</div>


<!-- UPCOMING APPOINTMENTS -->
<div class="card p-4 shadow mb-4">
    <h4 class="text-primary">Upcoming Appointments</h4>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Symptoms</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $appQuery = mysqli_query($conn, "
                SELECT appointment_date, appointment_time, doctor_assigned, symptoms
                FROM appointments
                WHERE patient_id = $patient_id
                ORDER BY appointment_date DESC, appointment_time DESC
            ");

            if (mysqli_num_rows($appQuery) == 0) {
                echo "<tr><td colspan='4' class='text-center text-muted'>No appointments found.</td></tr>";
            }

            while ($row = mysqli_fetch_assoc($appQuery)) { ?>
                <tr>
                    <td><?= $row['appointment_date'] ?: 'Not set' ?></td>
                    <td><?= $row['appointment_time'] ?: 'Not set' ?></td>
                    <td><?= $row['doctor_assigned'] ?></td>
                    <td><?= $row['symptoms'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


<!-- PRESCRIPTION HISTORY -->
<div class="card p-4 shadow mb-4">
    <h4 class="text-primary">Prescription History</h4>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Doctor</th>
                <th>View</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $pres = mysqli_query($conn, "
    SELECT p.*, u.username AS doctor_name
    FROM prescriptions p
    JOIN users u ON p.doctor_id = u.id
    WHERE p.patient_id = $patient_id
    ORDER BY p.created_at DESC
");


            if (mysqli_num_rows($pres) == 0) {
                echo "<tr><td colspan='3' class='text-center text-muted'>No prescriptions yet.</td></tr>";
            }

            while ($p = mysqli_fetch_assoc($pres)) { ?>
                <tr>
                    <td><?= $p['created_at'] ?></td>
                    <td><?= $p['doctor_name'] ?></td>
                    <td>
                        <a href="layout.php?page=prescription_view&id=<?= $p['id'] ?>" 
                           class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
