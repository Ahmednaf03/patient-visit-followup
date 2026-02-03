

<?php
require_once '../config/db.php';
require_once '../helpers/validation.php';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $dob      = $_POST['dob'] ?? '';
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $errors = validateUser($_POST);
    // field validations with helpers
    
    if(empty($errors)) {

        // Check if patient exists by if the name and dob match a record in patients table
        $stmt = $pdo->prepare(
            "SELECT patient_id, name
             FROM patients
             WHERE name = :name AND dob = :dob 
             LIMIT 1"
        );
        $stmt->execute([
            'name' => $name,
            'dob'  => $dob
        ]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patient) {
            $errors [] = 'No patient record found. Please contact admin.';
        } else {

            //  Check if user not already registered
            $stmt = $pdo->prepare(
                "SELECT 1 FROM users WHERE patient_id = :pid"
            );
            $stmt->execute(['pid' => $patient['patient_id']]);

            if ($stmt->fetch()) {
                $errors [] = 'Account already exists. Please login.';
            } else {

                // Create user if above checks pass
                $stmt = $pdo->prepare(
                    "INSERT INTO users (patient_id, name, email, password, role)
                     VALUES (:pid, :name, :email, :password, 'user')"
                );

                $stmt->execute([
                    'pid'      => $patient['patient_id'],
                    'name'     => $patient['name'],
                    'email'    => $email,
                    'password' => $password 
                ]);

                $success = 'Registration successful. You can now login.';
            }
        }
    }
}


?>

<?php require_once '../includes/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height:70vh;">
    <form method="post" class="card p-4" style="width:400px;">
        <?php if ($errors): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?><br>
        </div>
        <?php endforeach; ?>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

        <h4 class="text-center mb-3">Register</h4>

        <input class="form-control mb-2" name="name" placeholder="Full Name" required>
        <input class="form-control mb-2" type="date" name="dob" required>
        <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>

        <button class="btn btn-dark w-100">Register</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
