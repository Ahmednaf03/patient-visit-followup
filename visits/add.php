<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../helpers/validation.php';
require_once '../helpers/auth.php';
requireAdmin();

$error = '';

/* Fetch patients for dropdown */
$patients = $pdo->query("
    SELECT patient_id, name
    FROM patients
    ORDER BY name
")->fetchAll();
// runs after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $patientId  = $_POST['patient_id'] ?? '';
    $visitDate  = $_POST['visit_date'] ?? '';
    $consultFee = $_POST['consultation_fee'] ?? '';
    $labFee     = $_POST['lab_fee'] ?? '';

    // Validation via helper
    $errors = validateVisit($_POST);

    // If no errors, insert visit
    if (empty($errors)) {

        $sql = "
            INSERT INTO visits
            (patient_id, visit_date, consultation_fee, lab_fee, follow_up_due)
            VALUES
            (:patient_id, :visit_date, :consultation_fee, :lab_fee,
             DATE_ADD(:visit_date, INTERVAL 7 DAY))
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'patient_id'         => $patientId,
            'visit_date'         => $visitDate,
            'consultation_fee'   => $consultFee,
            'lab_fee'            => $labFee,
        ]);

        header('Location: visitlist.php');
        exit;
    }
}

?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h4 class="mb-4 text-center">Add Visit</h4>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
              <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                 </ul>
                </div>
            <?php endif; ?>


                <form method="post">

                    <div class="mb-3">
                        <label class="form-label">Patient *</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">Select patient</option>
                            <?php foreach ($patients as $p): ?>
                                <option value="<?= $p['patient_id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Visit Date *</label>
                        <input type="date" name="visit_date" class="form-control" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Consultation Fee *</label>
                            <input type="number" step="0.01" name="consultation_fee" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Lab Fee *</label>
                            <input type="number" step="0.01" name="lab_fee" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-dark px-4">
                            Save Visit
                        </button>
                        <a href="visitlist.php" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
