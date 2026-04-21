<?php
require_once "header.php";
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


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
      <a href="index.php?page=fournisseur" class="<?= $page=='fournisseur'?'active':'' ?>"><i class="bi bi-box-seam"></i> Fournisseur</a>
    <a href="index.php?page=pieces" class="<?= $page=='pieces'?'active':'' ?>"><i class="bi bi-box-seam"></i> Gestion d'article</a>
    <a href="index.php?page=entrees" class="<?= $page=='entrees'?'active':'' ?>"><i class="bi bi-arrow-down-circle"></i> Entrées</a>
    <a href="index.php?page=sorties" class="<?= $page=='sorties'?'active':'' ?>"><i class="bi bi-arrow-up-circle"></i> Sorties</a>

    <?php if(in_array($role,['ADMIN','direction'])): ?>
        <hr>
        <?php if($role=='ADMIN'): ?>
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
<?php
    if(isset($_GET['page'])){
        $page = $_GET['page'];
        if($page === 'pieces') {
            include "page/liste_pieces.php";
        } elseif($page === 'utilisateurs') {
            include "page/utilisateurs.php";
        } elseif($page === 'entrees') {
            include "page/entrees.php";
        } elseif($page === 'sorties') {
            include "page/sorties.php";
        } elseif($page === 'rapports') {
            include "page/rapports.php";
        } elseif($page === 'rapports_globaux') {
            include "page/rapports_globaux.php";
        }
        elseif($page === 'fournisseur') {
            include "page/fournisseurs.php";
        }else {
            echo "<div class='alert alert-danger'>Page introuvable</div>";
        }
    } else {
        if($role == 'ADMIN') {
            include "page/dashboard_admin.php";
        } elseif($role == 'direction') {
            include "page/dashboard_direction.php";
        } elseif($role == 'magasinier') {
            include "page/dashboard_magasinier.php";
        } elseif($role == 'responsable') {
            include "page/dashboard_responsable.php";
        } else {
         
        }
    }
?>
    
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