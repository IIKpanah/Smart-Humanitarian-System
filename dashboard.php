<?php
require_once 'includes/auth.php';
Auth::checkAuth(['ADMIN', 'NGO', 'GOV', 'LEADER', 'VOLUNTEER']);
require_once 'includes/db.php';
$db = DB::getInstance();

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    if ($_GET['action'] === 'getGeoJSON') {
        $communities = $db->getCommunitiesWithRisk();
        $features = [];
        foreach ($communities as $c) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => ['type' => 'Point', 'coordinates' => [(float)$c['longitude'], (float)$c['latitude']]],
                'properties' => [
                    'name' => $c['name'],
                    'risk_level' => $c['risk_level'] ?? 'MEDIUM',
                    'vul_index' => $c['vulnerability_index'] ?? 50
                ]
            ];
        }
        echo json_encode(['type' => 'FeatureCollection', 'features' => $features]);
        exit;
    }
    if ($_GET['action'] === 'getAnalytics') {
        $res = $db->query("SELECT risk_level, COUNT(*) as count FROM vulnerability_indicators GROUP BY risk_level");
        $data = ['critical'=>0,'high'=>0,'medium'=>0,'low'=>0];
        while($row = $res->fetch_assoc()) {
            $data[strtolower($row['risk_level'])] = (int)$row['count'];
        }
        echo json_encode($data);
        exit;
    }
}

// Render Page
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SHRDVMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 d-none d-lg-block bg-dark vh-100 p-3">
            <h5 class="text-white">📡 SHRDVMS</h5>
            <hr class="border-secondary">
            <a href="#" class="sidebar-link active">Dashboard</a>
            <a href="upload.php" class="sidebar-link">Upload Data</a>
            <a href="manage_keys.php" class="sidebar-link">System Settings</a>
            <a href="audit_log.php" class="sidebar-link">Audit Log</a>
            <a href="logout.php" class="sidebar-link text-danger">Logout</a>
        </div>
        <!-- Main -->
        <div class="col-lg-10 p-4">
            <h2>📊 Decision Intelligence Dashboard</h2>
            <!-- KPIs -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3"><div class="kpi-card"><span class="kpi-label">Critical</span><div class="kpi-number text-danger" id="criticalCount">12</div></div></div>
                <div class="col-6 col-md-3"><div class="kpi-card border-left-warning"><span class="kpi-label">IDPs</span><div class="kpi-number">1.2M</div></div></div>
                <div class="col-6 col-md-3"><div class="kpi-card border-left-info"><span class="kpi-label">Pending</span><div class="kpi-number">45</div></div></div>
                <div class="col-6 col-md-3"><div class="kpi-card border-left-success"><span class="kpi-label">Stock (T)</span><div class="kpi-number">340</div></div></div>
            </div>
            <!-- Map -->
            <div class="card mb-4"><div class="card-body"><div id="map" class="map-container"></div></div></div>
            <!-- Charts -->
            <div class="row g-3">
                <div class="col-md-6"><div class="card"><div class="card-body"><canvas id="riskChart" height="250"></canvas></div></div></div>
                <div class="col-md-6"><div class="card"><div class="card-body"><h6>Top Critical Communities</h6><ul class="list-group"><?php 
                $top = $db->query("SELECT c.name, v.risk_level FROM communities c JOIN vulnerability_indicators v ON c.id=v.community_id WHERE v.risk_level='CRITICAL' ORDER BY v.vulnerability_index DESC LIMIT 5");
                while($r = $top->fetch_assoc()) echo "<li class='list-group-item'><span class='badge bg-danger me-2'>CRITICAL</span> {$r['name']}</li>"; 
                ?></ul></div></div></div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>