<?php
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_GET['patient_id']) || !is_numeric($_GET['patient_id'])) {
    die('Invalid patient');
}

$patientId = (int)$_GET['patient_id'];

/* Aggregates */
$summaryStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total_visits,
        MIN(visit_date) AS first_visit,
        MAX(visit_date) AS last_visit,
        DATEDIFF(MAX(visit_date), MIN(visit_date)) AS days_between
    FROM visits
    WHERE patient_id = ?
");
$summaryStmt->execute([$patientId]);
$summary = $summaryStmt->fetch();

/* Visit list */
$visitsStmt = $pdo->prepare("
    SELECT visit_date, consultation_fee, lab_fee, follow_up_due
    FROM visits
    WHERE patient_id = ?
    ORDER BY visit_date ASC
");
$visitsStmt->execute([$patientId]);
$visits = $visitsStmt->fetchAll();
?>

<h2>Patient Visit History</h2>

<p>
    <strong>Total Visits:</strong> <?= $summary['total_visits'] ?><br>
    <strong>First Visit:</strong> <?= $summary['first_visit'] ?? 'N/A' ?><br>
    <strong>Last Visit:</strong> <?= $summary['last_visit'] ?? 'N/A' ?><br>
    <strong>Days Between:</strong>
    <?= $summary['days_between'] !== null ? $summary['days_between'] : 'N/A' ?>
</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Visit Date</th>
            <th>Consultation Fee</th>
            <th>Lab Fee</th>
            <th>Follow-up Due</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($visits): ?>
            <?php foreach ($visits as $v): ?>
                <tr>
                    <td><?= $v['visit_date'] ?></td>
                    <td><?= $v['consultation_fee'] ?></td>
                    <td><?= $v['lab_fee'] ?></td>
                    <td><?= $v['follow_up_due'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No visits found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
