<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$upcomingquery = " SELECT
    p.name,
    v.visit_date,
    v.follow_up_due
FROM visits v
JOIN patients p ON p.patient_id = v.patient_id
WHERE v.follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY v.follow_up_due;";

$overduequery = "SELECT
    p.name,
    v.visit_date,
    v.follow_up_due
FROM visits v
JOIN patients p ON p.patient_id = v.patient_id
WHERE v.follow_up_due < CURDATE()
AND NOT EXISTS (
    SELECT 1
    FROM visits v2
    WHERE v2.patient_id = v.patient_id
      AND v2.visit_date > v.follow_up_due
)
ORDER BY v.follow_up_due;";

$missedquery = " SELECT
    p.name,
    v.visit_date,
    v.follow_up_due
FROM visits v
JOIN patients p ON p.patient_id = v.patient_id
WHERE v.follow_up_due < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
AND NOT EXISTS (
    SELECT 1
    FROM visits v2
    WHERE v2.patient_id = v.patient_id
      AND v2.visit_date > v.follow_up_due
)
ORDER BY v.follow_up_due;";

$upcoming = $pdo->query($upcomingquery)->fetchAll();
$overdue  = $pdo->query($overduequery)->fetchAll();
$missed   = $pdo->query($missedquery)->fetchAll();

?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Follow-up Report</h4>

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
            <th>Last Visit</th>
            <th>Follow-up Due</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($upcoming): ?>
            <?php foreach ($upcoming as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['visit_date'] ?></td>
                    <td><?= $row['follow_up_due'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No records</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h4 class="mt-4">Overdue follow-ups</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Last Visit</th>
            <th>Follow-up Due</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($overdue): ?>
            <?php foreach ($overdue as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['visit_date'] ?></td>
                    <td><?= $row['follow_up_due'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No overdue follow-ups</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<h4 class="mt-4">Missed follow-ups (no visit after due date)</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Last Visit</th>
            <th>Follow-up Due</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($missed): ?>
            <?php foreach ($missed as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['visit_date'] ?></td>
                    <td><?= $row['follow_up_due'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No missed follow-ups</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

