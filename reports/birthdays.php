<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$birthdays = $pdo->query("
    SELECT
        p.name,
        p.dob,
        DATE_ADD(
            p.dob,
            INTERVAL (YEAR(CURDATE()) - YEAR(p.dob)) YEAR
        ) AS birthday_this_year,
        DATEDIFF(
            DATE_ADD(
                p.dob,
                INTERVAL (YEAR(CURDATE()) - YEAR(p.dob)) YEAR
            ),
            CURDATE()
        ) AS days_remaining
    FROM patients p
    WHERE
        DATEDIFF(
            DATE_ADD(
                p.dob,
                INTERVAL (YEAR(CURDATE()) - YEAR(p.dob)) YEAR
            ),
            CURDATE()
        ) BETWEEN 0 AND 30
    ORDER BY days_remaining
")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Birthday Report</h4>

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
            <th>Date of Birth</th>
            <th>Birthday This Year</th>
            <th>Days Remaining</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($birthdays): ?>
            <?php foreach ($birthdays as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['dob'] ?></td>
                    <td><?= $row['birthday_this_year'] ?></td>
                    <td><?= $row['days_remaining'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No upcoming birthdays</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
