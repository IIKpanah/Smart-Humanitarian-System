<?php
require_once 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = DB::getInstance();
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $mfa_secret = Auth::generateMFASecret();
    $db->insert('users', [
        'username' => $_POST['username'],
        'password' => $hashed,
        'role' => $_POST['role'],
        'organization' => $_POST['organization'],
        'phone' => $_POST['phone'],
        'mfa_secret' => $mfa_secret,
        'is_mfa_enabled' => 1,
        'is_approved' => 0 // Requires admin approval
    ]);
    echo "<div class='alert alert-success'>Registered! Await Admin approval. MFA Secret: $mfa_secret (Save this)</div>";
}
?>
<!DOCTYPE html>
<html><head><title>Register</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-5" style="max-width:500px;">
    <h3>Register</h3>
    <form method="POST">
        <input class="form-control mb-2" name="username" placeholder="Username" required>
        <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
        <select class="form-control mb-2" name="role">
            <option value="NGO">NGO</option><option value="GOV">Government</option><option value="LEADER">Community Leader</option><option value="VOLUNTEER">Volunteer</option>
        </select>
        <input class="form-control mb-2" name="organization" placeholder="Organization Name">
        <input class="form-control mb-2" name="phone" placeholder="Phone">
        <button class="btn btn-primary w-100">Register</button>
    </form>
</div>
</body>
</html>