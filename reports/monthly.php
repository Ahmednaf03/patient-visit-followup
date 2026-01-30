<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$perPage = 5;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

/* Patients joined per month */
$patientsMonthly = $pdo->query("
    SELECT
        YEAR(join_date) AS year,
        MONTH(join_date) AS month,
        COUNT(*) AS total_patients
    FROM patients
    GROUP BY YEAR(join_date), MONTH(join_date)
    ORDER BY year DESC, month DESC
    LIMIT $perPage OFFSET $offset
")->fetchAll();

/* Visits per month */
$visitsMonthly = $pdo->query("
    SELECT
        YEAR(visit_date) AS year,
        MONTH(visit_date) AS month,
        COUNT(*) AS total_visits
    FROM visits
    GROUP BY YEAR(visit_date), MONTH(visit_date)
    ORDER BY year DESC, month DESC
")->fetchAll();

$totalStmt = $pdo->query("SELECT COUNT(*) FROM patients");
$totalRows = (int)$totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);
?>



<!-- Patients Joined -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Monthly report</h4>

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
            <th>Year</th>
            <th>Month</th>
            <th>Total Patients</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($patientsMonthly): ?>
            <?php foreach ($patientsMonthly as $row): ?>
                <tr>
                    <td><?= $row['year'] ?></td>
                    <td><?= $row['month'] ?></td>
                    <td><?= $row['total_patients'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No records</td>
            </tr>
        <?php endif; ?>
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

<!-- Visits -->
<h4 class="mt-4">Visits Per Month</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Year</th>
            <th>Month</th>
            <th>Total Visits</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($visitsMonthly): ?>
            <?php foreach ($visitsMonthly as $row): ?>
                <tr>
                    <td><?= $row['year'] ?></td>
                    <td><?= $row['month'] ?></td>
                    <td><?= $row['total_visits'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No records</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
