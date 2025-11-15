<?php
include('config.php');


if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

// Correct SQL JOIN
$q = mysqli_query($conn, "
SELECT p.*, 
       u.username AS doctor_name,
       (SELECT username FROM users WHERE id = p.patient_id) AS patient_name
FROM prescriptions p
JOIN users u ON p.doctor_id = u.id
WHERE p.id = $id
");

$data = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html>
<head>
<title>Prescription</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4" style="max-width: 650px;">
    <div class="card p-4 shadow">

        <h3 class="text-primary">Prescription Details</h3>

        <p><b>Patient:</b> <?= $data['patient_name'] ?></p>
        <p><b>Doctor:</b> <?= $data['doctor_name'] ?></p>
        <p><b>Date:</b> <?= $data['created_at'] ?></p>

        <hr>

        <h5>Medicines</h5>
        <p><?= nl2br($data['medicines']) ?></p>

        <h5>Instructions</h5>
        <p><?= nl2br($data['instructions']) ?></p>

        <!-- FIXED BACK BUTTON -->
        <a href="layout.php?page=dashboard" class="btn btn-secondary mt-3">⬅ Back</a>

    </div>
</div>

</body>
</html>
