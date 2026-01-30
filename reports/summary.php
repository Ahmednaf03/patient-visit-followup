<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$perPage = 5;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$summary = $pdo->query("
    SELECT
        p.patient_id,
        p.name,
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age_years,
        COUNT(v.visit_id) AS total_visits,
        MAX(v.visit_date) AS last_visit,
        DATEDIFF(CURDATE(), MAX(v.visit_date)) AS days_since_last_visit,
        MAX(v.follow_up_due) AS next_follow_up,
        CASE
            WHEN MAX(v.follow_up_due) IS NULL THEN 'No visits'
            WHEN MAX(v.follow_up_due) >= CURDATE() THEN 'Upcoming'
            WHEN MAX(v.follow_up_due) < CURDATE()
                 AND NOT EXISTS (
                     SELECT 1 FROM visits v2
                     WHERE v2.patient_id = p.patient_id
                       AND v2.visit_date > MAX(v.follow_up_due)
                 )
                 AND MAX(v.follow_up_due) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            THEN 'Overdue'
            WHEN MAX(v.follow_up_due) < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 AND NOT EXISTS (
                     SELECT 1 FROM visits v2
                     WHERE v2.patient_id = p.patient_id
                       AND v2.visit_date > MAX(v.follow_up_due)
                 )
            THEN 'Missed'
            ELSE 'Completed'
        END AS follow_up_status
    FROM patients p
    LEFT JOIN visits v ON v.patient_id = p.patient_id
    GROUP BY p.patient_id, p.name, p.dob
    ORDER BY p.name
    LIMIT $perPage OFFSET $offset
")->fetchAll();
$totalStmt = $pdo->query("SELECT COUNT(*) FROM patients");
$totalRows = (int)$totalStmt->fetchColumn();
$totalPages = (int)ceil($totalRows / $perPage);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Summary Report</h2>

    <div class="btn-group">
        <a href="summary.php"
           class="btn btn-sm <?= basename($_SERVER['PHP_SELF']) === 'summary.php' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Summary
        </a>
        <a href="followups.php"
           class="btn btn-sm <?= basename($_SERVER['PHP_SELF']) === 'followups.php' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Follow-ups
        </a>
        <a href="monthly.php"
           class="btn btn-sm <?= basename($_SERVER['PHP_SELF']) === 'monthly.php' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Monthly
        </a>
        <a href="birthdays.php"
           class="btn btn-sm <?= basename($_SERVER['PHP_SELF']) === 'birthdays.php' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Birthdays
        </a>
    </div>
</div>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Age</th>
            <th>Total Visits</th>
            <th>Last Visit</th>
            <th>Days Since Last Visit</th>
            <th>Next Follow-up</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($summary as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['age_years'] ?></td>
                <td><?= $row['total_visits'] ?></td>
                <td><?= $row['last_visit'] ?? 'N/A' ?></td>
                <td><?= $row['days_since_last_visit'] ?? 'N/A' ?></td>
                <td><?= $row['next_follow_up'] ?? 'N/A' ?></td>
                <td><?= $row['follow_up_status'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
