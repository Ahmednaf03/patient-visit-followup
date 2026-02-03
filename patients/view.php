<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$patientId = $_GET['id'] ?? null;

if (!$patientId) {
    die('Patient ID missing');
}
/* main query to get patient 
id, name, age, last visit date, days since last visit, next follow up */
$sql = "
SELECT
    p.patient_id,
    p.name,
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age_years,
    MAX(v.visit_date) AS last_visit_date,
    DATEDIFF(CURDATE(), MAX(v.visit_date)) AS days_since_last_visit,
    MAX(v.follow_up_due) AS next_follow_up,
    CASE
        WHEN MAX(v.follow_up_due) < CURDATE() THEN 'Yes'
        ELSE 'No'
    END AS follow_up_overdue
FROM patients p
LEFT JOIN visits v ON v.patient_id = p.patient_id
WHERE p.patient_id = :patient_id
GROUP BY p.patient_id, p.name, p.dob
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['patient_id' => $patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    die('Patient not found');
}
?>

<h2>Patient Details</h2>

<ul class="list-group">
    <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($patient['name']) ?></li>
    <li class="list-group-item"><strong>Age:</strong> <?= $patient['age_years'] ?></li>
    <li class="list-group-item"><strong>Last Visit:</strong> <?= $patient['last_visit_date'] ?? 'No visits' ?></li>
    <li class="list-group-item"><strong>Days Since Last Visit:</strong> <?= $patient['days_since_last_visit'] ?? 'N/A' ?></li>
    <li class="list-group-item"><strong>Next Follow-up:</strong> <?= $patient['next_follow_up'] ?? 'N/A' ?></li>
    <li class="list-group-item"><strong>Follow-up Overdue:</strong> <?= $patient['follow_up_overdue'] ?></li>
</ul>
<br>
<a href="../visits/patient_visits.php?patient_id=<?= $patient['patient_id'] ?>" class="btn btn-primary">View Visits</a>
<?php require_once '../includes/footer.php'; ?>
