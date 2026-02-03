<?php
require_once '../config/db.php';
require_once '../helpers/auth.php';
require_once '../includes/header.php';
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare(
        "SELECT user_id, patient_id, name, role, password 
         FROM users 
         WHERE email = ?"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {
        $_SESSION['auth'] = [
            'user_id'    => $user['user_id'],
            'patient_id' => $user['patient_id'],
            'name'       => $user['name'],
            'role'       => $user['role'],
        ];
    
        header('Location: /patient-visit-followup/index.php');
        exit;
    }

    $error = 'Invalid email or password';
}
?>
<?php require_once '../includes/header.php'; ?>


<div class="container d-flex justify-content-center align-items-center" style="min-height:70vh;">
    <form method="post" class="card p-4" style="width:350px;">
        <h4 class="mb-3 text-center">Login</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>

        <button class="btn btn-dark w-100">Login</button>
        <div class="d-flex justify-content-end mt-2">
          <a href="register.php" class="text-dark fw-medium">Register</a>
        </div>

    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
