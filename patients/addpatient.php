<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../helpers/validation.php';
require_once '../helpers/auth.php';
requireAdmin(); // ensure only logged-in admins can access 

$errors = [];
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$patient = null;


if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->execute([$_GET['id']]);
    $patient = $stmt->fetch();

    if (!$patient) {
        die('Patient not found');
    }

    
}
// runs after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $dob      = $_POST['dob'] ?? '';
    $joinDate = $_POST['join_date'] ?? '';
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    // field validations with helpers
    $errors = validatePatient($_POST);

    // if no errors then update or insert
    if (empty($errors)) {
        // this ensures it is an update with invisible patient_id field
        if (isset($_POST['patient_id']) && is_numeric($_POST['patient_id'])) {
            // simple UPDATE query 
            $sql = "
                UPDATE patients
                SET
                    name = :name,
                    dob = :dob,
                    join_date = :join_date,
                    phone = :phone,
                    address = :address
                WHERE patient_id = :patient_id
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name'       => $name,
                'dob'        => $dob,
                'join_date'  => $joinDate,
                'phone'      => $phone ?: null,
                'address'    => $address ?: null,
                'patient_id' => $_POST['patient_id'],
            ]);

        } else {
            // else insert new patient
            $sql = "
                INSERT INTO patients (name, dob, join_date, phone, address)
                VALUES (:name, :dob, :join_date, :phone, :address)
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name'       => $name,
                'dob'        => $dob,
                'join_date'  => $joinDate,
                'phone'      => $phone ?: null,
                'address'    => $address ?: null,
            ]);
        }

        header('Location: list.php');
        exit;
    }
}


?>


<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h4 class="mb-4 text-center"><?= $isEdit ? 'Update Patient' : 'Add Patient' ?></h4>

                <form method="post">
                        <?php if ($isEdit): ?>
                    <input type="hidden" name="patient_id"
                      value="<?= $patient['patient_id'] ?>">
                         <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" required
                            value="<?= $patient['name'] ?? '' ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                            value="<?= $patient['phone'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control" required
                            value="<?= $patient['dob'] ?? '' ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Join Date *</label>
                            <input type="date" name="join_date" class="form-control" required
                            value="<?= $patient['join_date'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"
                        ><?= $patient['address'] ?? '' ?></textarea>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-dark px-4">
                            <?= $isEdit ? 'Update Patient' : 'Add Patient' ?>
                        </button>
                        <a href="list.php" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


<?php require_once '../includes/footer.php'; ?>
