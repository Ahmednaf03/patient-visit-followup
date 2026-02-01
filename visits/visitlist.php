<?php
require_once '../config/db.php';
require_once '../includes/header.php';
include_once '../helpers/auth.php';
requireAdmin();
/* main query to get visit id, visit date, consultation fee, lab fee, 
follow up due, patient id, patient name */
$sql = "
SELECT
    v.visit_id,
    v.visit_date,
    v.consultation_fee,
    v.lab_fee,
    v.follow_up_due,
    p.patient_id,
    p.name AS patient_name
FROM visits v
JOIN patients p ON p.patient_id = v.patient_id
ORDER BY v.visit_date DESC
";

$visits = $pdo->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Visit List</h2>
<?php if (isAdmin()): ?>
    <a href="add.php" class="btn btn-dark">Add Visit</a>
<?php endif; ?>

</div>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Visit Date</th>
            <th>Consultation Fee</th>
            <th>Lab Fee</th>
            <th>Follow-up Due</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($visits as $v): ?>
        <tr>
            <td>
                <a href="../patients/view.php?id=<?= $v['patient_id'] ?>" class="patient-link">
                    <?= htmlspecialchars($v['patient_name']) ?>
                </a>
            </td>
            <td><?= $v['visit_date'] ?></td>
            <td><?= number_format($v['consultation_fee'], 2) ?></td>
            <td><?= number_format($v['lab_fee'], 2) ?></td>
            <td><?= $v['follow_up_due'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
