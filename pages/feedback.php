<?php
include('config.php');

// Only patients allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];
$message = "";

// Fetch doctors the patient had appointments with
$doctors = mysqli_query($conn, "
    SELECT DISTINCT u.username, d.id AS doctor_id
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    JOIN users u ON u.id = d.user_id
    WHERE a.patient_id = '$patient_id'
");

if (isset($_POST['submit'])) {
    
    $doctor_id = $_POST['doctor_id'];
    $rating = $_POST['rating'];
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);

    $insert = mysqli_query($conn, "
        INSERT INTO feedback (patient_id, doctor_id, rating, comments, created_at)
        VALUES ('$patient_id', '$doctor_id', '$rating', '$comments', NOW())
    ");

    if ($insert) {
        $message = "<div class='alert alert-success'>Thank you! Your feedback was submitted.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error submitting feedback.</div>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Give Feedback</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width:600px;">
    <div class="card p-4 shadow">
        <h3 class="text-primary mb-3">Give Feedback</h3>

        <?= $message ?>

        <form method="post">
            
            <label class="form-label">Select Doctor</label>
            <select name="doctor_id" class="form-select mb-3" required>
                <option value="">-- Choose Doctor --</option>
                <?php while ($d = mysqli_fetch_assoc($doctors)) { ?>
                    <option value="<?= $d['doctor_id'] ?>">
                        <?= $d['username'] ?>
                    </option>
                <?php } ?>
            </select>

            <label class="form-label">Rating (1–5)</label>
            <select name="rating" class="form-select mb-3" required>
                <option value="5">★★★★★ (5)</option>
                <option value="4">★★★★☆ (4)</option>
                <option value="3">★★★☆☆ (3)</option>
                <option value="2">★★☆☆☆ (2)</option>
                <option value="1">★☆☆☆☆ (1)</option>
            </select>

            <label class="form-label">Comments</label>
            <textarea name="comments" class="form-control mb-3" rows="4" required></textarea>

            <button name="submit" class="btn btn-success w-100">Submit Feedback</button>

        </form>

    </div>
</div>

</body>
</html>
