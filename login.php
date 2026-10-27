<?php
require_once 'includes/auth.php';
require_once 'includes/audit.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = Auth::login($_POST['username'], $_POST['password'], $_POST['mfa_code'] ?? null);
    if ($res === true) { header('Location: dashboard.php'); exit; }
    elseif ($res === 'MFA_REQUIRED') { $mfa_required = true; }
    else { $error = 'Invalid credentials or MFA code.'; }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login | SHRDVMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-5" style="max-width:400px;">
    <h3>🔐 SHRDVMS Login</h3>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <input class="form-control mb-2" name="username" placeholder="Username" required>
        <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
        <?php if(isset($mfa_required)): ?>
            <input class="form-control mb-2" name="mfa_code" placeholder="6-digit MFA Code" required>
        <?php endif; ?>
        <button class="btn btn-primary w-100">Login</button>
    </form>
    <p class="mt-2"><a href="register.php">Register new account</a></p>
</div>
<script src="assets/js/encryption.js"></script>
</body>
</html>