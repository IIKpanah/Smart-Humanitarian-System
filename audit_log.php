<?php require_once 'includes/auth.php'; Auth::checkAuth(['ADMIN','GOV']); 
$logs = file_get_contents(LOG_PATH . 'access.log'); 
?>
<!DOCTYPE html><html><head><title>Audit</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><div class="container mt-5"><h4>📜 Audit Trail</h4><pre class="bg-light p-3" style="max-height:600px; overflow-y:scroll;"><?php echo htmlspecialchars($logs); ?></pre></div></body></html>