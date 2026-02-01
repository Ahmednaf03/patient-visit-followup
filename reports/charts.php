<?php
require_once '../config/db.php';
require_once '../includes/header.php';
// main query to get total visits, upcoming, overdue, missed follow ups
$stmt = $pdo->query("SELECT
    COUNT(*) AS total_visits,

    SUM(
        CASE
            WHEN follow_up_due >= CURDATE() THEN 1
            ELSE 0
        END
    ) AS upcoming,

    SUM(
        CASE
            WHEN follow_up_due < CURDATE()
                 AND follow_up_due >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 AND NOT EXISTS (
                     SELECT 1
                     FROM visits v2
                     WHERE v2.patient_id = v.patient_id
                       AND v2.visit_date > v.follow_up_due
                 )
            THEN 1 ELSE 0
        END
    ) AS overdue,

    SUM(
        CASE
            WHEN follow_up_due < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 AND NOT EXISTS (
                     SELECT 1
                     FROM visits v2
                     WHERE v2.patient_id = v.patient_id
                       AND v2.visit_date > v.follow_up_due
                 )
            THEN 1 ELSE 0
        END
    ) AS missed
FROM visits v;
");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Charts Overview</h4>

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
          <a href="charts.php"
           class="btn btn-sm <?= basename($_SERVER['PHP_SELF']) === 'charts.php' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Charts
        </a>
    </div>
</div>
<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <h4 class="text-center mb-4">Visits Overview</h4>
            <canvas id="visitsChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('visitsChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Total Visits', 'Upcoming', 'Overdue', 'Missed'],
        datasets: [{
            label: 'Count',
            data: [
                <?= (int)$stats['total_visits'] ?>,
                <?= (int)$stats['upcoming'] ?>,
                <?= (int)$stats['overdue'] ?>,
                <?= (int)$stats['missed'] ?>
            ],
            backgroundColor: [
    '#111111', // Total Visits
    '#0d6efd', // Upcoming (blue)
    '#ffc107', // Overdue (yellow)
    '#dc3545'  // Missed (red)
        ], 
        borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
<?php require_once '../includes/footer.php'; ?>