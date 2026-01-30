<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../helpers/auth.php';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchParam = "%$search%";
$limit = 5; // records per page

$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0
    ? (int) $_GET['page']
    : 1;

$offset = ($page - 1) * $limit;

$whereSql = '';
$params = [];

if ($search !== '') {
    $whereSql = 'WHERE p.name LIKE :search';
    $params['search'] = "%$search%";
}

$countSql = "
    SELECT COUNT(*) AS total
    FROM patients p
    $whereSql
";

$countStmt = $pdo->prepare($countSql);

foreach ($params as $key => $value) {
    $countStmt->bindValue(":$key", $value);
}

$countStmt->execute();


$totalPatients = (int) $countStmt->fetchColumn();
$totalPages = ceil($totalPatients / $limit);
$sql = "
SELECT
    p.patient_id,
    p.name,
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age_years,
    CONCAT(
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()),
        ' years ',
        MOD(TIMESTAMPDIFF(MONTH, p.dob, CURDATE()), 12),
        ' months'
    ) AS age_years_months,
    YEAR(p.join_date)  AS join_year,
    MONTH(p.join_date) AS join_month,
    DAY(p.join_date)   AS join_day,
    COUNT(v.visit_id) AS total_visits
FROM patients p
LEFT JOIN visits v ON v.patient_id = p.patient_id
$whereSql
GROUP BY p.patient_id, p.name, p.dob, p.join_date
ORDER BY p.name

LIMIT :limit OFFSET :offset;";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value); // learn me better
} 
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$patients = $stmt->fetchAll();
?>
<form method="get" class="mb-3">
    <div class="input-group" style="max-width: 300px;">
        <input
            type="text"
            name="q"
            class="form-control"
            placeholder="Search patient"
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
        >
        <button class="btn btn-dark" type="submit">Search</button>
    </div>
</form>

<h2>Patient List</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Full Age</th>
            <th>Join Date (Y-M-D)</th>
            <th>Total Visits</th>
            <?php if (isAdmin()): ?>
            <th>Actions</th>
            <?php endif; ?>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($patients as $p): ?>
        <tr>
            <td><a href="view.php?id=<?= $p['patient_id'] ?>" class="patient-link">
                <?= htmlspecialchars($p['name']) ?>
                </a></td>
            <td><?= $p['age_years'] ?></td>
            <td><?= $p['age_years_months'] ?></td>
            <td><?= $p['join_year'] ?>-<?= $p['join_month'] ?>-<?= $p['join_day'] ?></td>
            <td><?= $p['total_visits'] ?></td>
           
           <?php if (isAdmin()): ?>
                        <td> 
             <a href="edit.php?id=<?= $p['patient_id'] ?>"class="text-decoration-none">
              <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"><g fill="green" stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="20" stroke-dashoffset="20" d="M3 21h18"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.2s" values="20;0"/></path><path stroke-dasharray="48" stroke-dashoffset="48" d="M7 17v-4l10 -10l4 4l-10 10h-4"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.2s" dur="0.6s" values="48;0"/></path><path stroke-dasharray="8" stroke-dashoffset="8" d="M14 6l4 4"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.8s" dur="0.2s" values="8;0"/></path></g></svg>
             </a>
    |
            <a href="delete.php?id=<?= $p['patient_id'] ?>"
            onclick="return confirm('Delete this patient?')" class="text-decoration-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="red" d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zM17 6H7v13h10zM9 17h2V8H9zm4 0h2V8h-2zM7 6v13z"/></svg></a>
            </a>
            </td>
            <?php endif; ?>



        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                <a class="page-link"
                   href="?page=<?= $i ?>&q=<?= urlencode($search) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>

</nav>


<?php require_once '../includes/footer.php'; ?>
