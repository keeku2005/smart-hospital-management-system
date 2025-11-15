<?php
include(__DIR__ . '/../config.php');

// Only doctors allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$doctor_username = $_SESSION['username'];

// Find doctor_id of logged-in doctor
$docQuery = mysqli_query($conn, "
    SELECT d.id AS doctor_id, d.specialty
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    WHERE u.username = '$doctor_username'
");
$doc = mysqli_fetch_assoc($docQuery);
$doctor_id = $doc['doctor_id'];
$doctor_specialty = $doc['specialty'];

// Get doctor appointments
$appointments = mysqli_query($conn, "
    SELECT a.*, u.username AS patient_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    WHERE 
        a.doctor_assigned = '$doctor_username'
        OR a.doctor_assigned = '$doctor_specialty'
        OR a.doctor_id = $doctor_id
    ORDER BY appointment_date ASC, appointment_time ASC
");
?>

<div class="container mt-4">

    <div class="card p-4 shadow mb-4">
        <h3 class="text-primary">Your Appointments</h3>
        <p>Specialty: <b><?= $doctor_specialty ?></b></p>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <div class="card p-4 shadow">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Symptoms</th>
                    <th>Prescription</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if (mysqli_num_rows($appointments) == 0) {
                    echo "<tr><td colspan='5' class='text-center text-muted'>No appointments found.</td></tr>";
                }

                while ($row = mysqli_fetch_assoc($appointments)) { ?>
                    <tr>
                        <td><?= $row['appointment_date'] ?: 'Not set' ?></td>
                        <td><?= $row['appointment_time'] ?: 'Not set' ?></td>
                        <td><?= $row['patient_name'] ?></td>
                        <td><?= $row['symptoms'] ?></td>
                        <td>
                            <!-- FIXED WRITE BUTTON -->
                            <a href="layout.php?page=doctor_prescription&appointment_id=<?= $row['id'] ?>" 
                               class="btn btn-success btn-sm">
                                Write
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- FEEDBACK RECEIVED -->
    <div class="card p-4 shadow mt-4">
        <h3 class="text-primary">Feedback Received</h3>

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Rating</th>
                    <th>Comments</th>
                    <th>Patient</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $fbQuery = mysqli_query($conn, "
                    SELECT f.*, u.username AS patient_name
                    FROM feedback f
                    JOIN users u ON f.patient_id = u.id
                    WHERE f.doctor_id = $doctor_id
                    ORDER BY f.created_at DESC
                ");

                if (mysqli_num_rows($fbQuery) == 0) {
                    echo "<tr><td colspan='4' class='text-center text-muted'>No feedback yet.</td></tr>";
                }

                while ($fb = mysqli_fetch_assoc($fbQuery)) { ?>
                    <tr>
                        <td><?= str_repeat('⭐', $fb['rating']); ?></td>
                        <td><?= htmlspecialchars($fb['comments']) ?></td>
                        <td><?= $fb['patient_name'] ?></td>
                        <td><?= $fb['created_at'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>
        <!-- PRESCRIPTIONS WRITTEN -->
<div class="card p-4 shadow mt-4">
    <h3 class="text-primary">Prescriptions Written</h3>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Medicines</th>
                <th>Instructions</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $pres = mysqli_query($conn, "
                SELECT p.*, u.username AS patient_name
                FROM prescriptions p
                JOIN users u ON p.patient_id = u.id
                WHERE p.doctor_id = $doctor_id
                ORDER BY p.created_at DESC
            ");

            if (mysqli_num_rows($pres) == 0) {
                echo "<tr><td colspan='5' class='text-center text-muted'>No prescriptions written.</td></tr>";
            }

            while ($p = mysqli_fetch_assoc($pres)) { ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= $p['patient_name'] ?></td>
                    <td><?= htmlspecialchars($p['medicines']) ?></td>
                    <td><?= htmlspecialchars($p['instructions']) ?></td>
                    <td><?= $p['created_at'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

    </div>

</div>
