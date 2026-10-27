<?php require_once 'includes/auth.php'; Auth::checkAuth(['ADMIN']); ?>
<!DOCTYPE html>
<html><head><title>Manage System</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><div class="container mt-5"><h4>🔧 System Management</h4>
    <div class="alert alert-info">Click to trigger AI Retraining (Pings Python Microservice)</div>
    <form action="http://localhost:5000/retrain" method="POST"><button class="btn btn-warning">Retrain AI Model</button></form>
    <hr>
    <div class="alert alert-secondary">RSA Keys are stored in config/keys/. Upload new ones here (Advanced).</div>
</div></body></html>