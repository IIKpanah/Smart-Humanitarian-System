<?php
require_once 'includes/auth.php';
Auth::checkAuth(['ADMIN', 'NGO', 'VOLUNTEER']);
require_once 'includes/file_handler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $result = FileHandler::secureUpload($_FILES['file']);
    $msg = $result ? "File uploaded & processed successfully!" : "Upload failed.";
}
?>
<!DOCTYPE html>
<html><head><title>Upload</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-5"><div class="card"><div class="card-body">
    <h4>📤 Upload Beneficiary CSV / GeoJSON / Incident Photo</h4>
    <?php if(isset($msg)) echo "<div class='alert alert-info'>$msg</div>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" class="form-control mb-3" accept=".csv,.json,.geojson,.jpg,.png" required>
        <button class="btn btn-primary">Upload & Process</button>
        <p class="text-muted mt-2">CSV format: Community, FullName, Age, Gender, Disability(Yes/No), Household, Livelihood, Poverty</p>
    </form>
</div></div></div>
</body>
</html>