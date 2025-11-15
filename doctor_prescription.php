<?php
// FIX: No session_start() here
include(__DIR__ . '/../config.php');

// Check required parameter
if (!isset($_GET['appointment_id'])) {
    echo "<div class='alert alert-danger'>No appointment selected.</div>";
    exit;
}

$appointment_id = intval($_GET['appointment_id']);
$doctor_id = $_SESSION['user_id'];  // logged in doctor

// Fetch appointment details
$appointment = mysqli_query($conn, "
    SELECT a.*, u.username AS patient_name 
    FROM appointments a 
    JOIN users u ON a.patient_id = u.id
    WHERE a.id = $appointment_id
");

if (mysqli_num_rows($appointment) == 0) {
    echo "<div class='alert alert-danger'>Appointment not found.</div>";
    exit;
}

$app = mysqli_fetch_assoc($appointment);
?>

<div class="card p-4 shadow mb-4">
    <h3 class="text-primary">Write Prescription</h3>

    <p><b>Patient:</b> <?= $app['patient_name'] ?></p>
    <p><b>Symptoms:</b> <?= $app['symptoms'] ?></p>
</div>

<?php
// Save prescription
if (isset($_POST['save'])) {

    $medicines = mysqli_real_escape_string($conn, $_POST['medicines']);
    $instructions = mysqli_real_escape_string($conn, $_POST['instructions']);
    $patient_id = $app['patient_id'];

    // Insert into prescriptions table
    mysqli_query($conn, "
        INSERT INTO prescriptions (appointment_id, doctor_id, patient_id, medicines, instructions, created_at)
        VALUES ($appointment_id, $doctor_id, $patient_id, '$medicines', '$instructions', NOW())
    ");

    $prescription_id = mysqli_insert_id($conn);

    // Update appointment with prescription_id
    mysqli_query($conn, "
        UPDATE appointments 
        SET prescription_id = $prescription_id, status='completed'
        WHERE id = $appointment_id
    ");

    echo "<div class='alert alert-success'>Prescription saved successfully!</div>";
}
?>

<div class="card p-4 shadow">
    <form method="post">
        <label>Medicines</label>
        <textarea name="medicines" class="form-control mb-3" required></textarea>

        <label>Instructions</label>
        <textarea name="instructions" class="form-control mb-3" required></textarea>

        <button class="btn btn-primary" name="save">Save Prescription</button>
    </form>
</div>
