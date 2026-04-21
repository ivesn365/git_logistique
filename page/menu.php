<?php
require_once "../header.php";
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion Logistique</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f1f5f9;
}

/* SIDEBAR */
.sidebar {
    width: 250px;
    min-height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: linear-gradient(180deg, #1e293b, #0f172a);
    color: white;
    padding-top: 1rem;
    transition: all 0.3s;
}
.sidebar a {
    color: #cbd5e1;
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 4px;
    transition: 0.2s;
}
.sidebar a:hover, .sidebar a.active {
    background-color: #2563eb;
    color: white;
}

/* MAIN CONTENT */
.main-content {
    margin-left: 250px;
    padding: 20px;
    transition: all 0.3s;
}

/* TOPBAR */
.topbar {
    background: white;
    padding: 12px 20px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

/* DARK MODE */
body.dark-mode {
    background-color: #121212;
    color: white;
}
body.dark-mode .sidebar {
    background: #0f172a;
}
body.dark-mode .topbar {
    background: #1e293b;
    color: white;
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar d-none d-md-block">
    <div class="text-center mb-4">
        <h4>LOGISTIQUE</h4>
        <small>👋 <?= htmlspecialchars($username) ?></small>
    </div>
    <a href="index.php?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="index.php?page=pieces" class="<?= $page=='pieces'?'active':'' ?>"><i class="bi bi-box-seam"></i> Pièces</a>
    <a href="index.php?page=entrees" class="<?= $page=='entrees'?'active':'' ?>"><i class="bi bi-arrow-down-circle"></i> Entrées</a>
    <a href="index.php?page=sorties" class="<?= $page=='sorties'?'active':'' ?>"><i class="bi bi-arrow-up-circle"></i> Sorties</a>

    <?php if(in_array($role,['admin','direction'])): ?>
        <hr>
        <?php if($role=='admin'): ?>
            <a href="index.php?page=utilisateurs" class="<?= $page=='utilisateurs'?'active':'' ?>"><i class="bi bi-people"></i> Utilisateurs</a>
            <a href="index.php?page=rapports" class="<?= $page=='rapports'?'active':'' ?>"><i class="bi bi-bar-chart"></i> Rapports</a>
        <?php endif; ?>
        <?php if($role=='direction'): ?>
            <a href="index.php?page=rapports_globaux" class="<?= $page=='rapports_globaux'?'active':'' ?>"><i class="bi bi-graph-up"></i> Rapports globaux</a>
        <?php endif; ?>
    <?php endif; ?>

    <hr>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-list fs-4" onclick="toggleSidebar()"></i>
            <span class="ms-3 fw-semibold"><?= ucfirst($page) ?></span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-secondary" onclick="toggleDarkMode()">🌙 Mode sombre</button>
        </div>
    </div>

    <!-- PAGE DYNAMIQUE -->
    <div>
        <?php
        $file = "pages/" . $page . ".php";
        if(file_exists($file)){
            include $file;
        } else {
            echo "<div class='alert alert-danger'>Page introuvable</div>";
        }
        ?>
    </div>
</div>

<script>
function toggleDarkMode(){
    document.body.classList.toggle("dark-mode");
}
function toggleSidebar(){
    const sidebar = document.querySelector(".sidebar");
    const main = document.querySelector(".main-content");
    sidebar.classList.toggle("d-none");
    main.style.marginLeft = sidebar.classList.contains("d-none") ? "0" : "250px";
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>