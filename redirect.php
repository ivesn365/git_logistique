<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

switch($_SESSION['role']) {
    case 'ADMIN':
        header("Location: page/dashboard_admin.php");
        break;

    case 'magasinier':
        header("Location: page/dashboard_magasinier.php");
        break;

    case 'direction':
        header("Location: page/dashboard_direction.php");
        break;

    case 'responsable':
        header("Location: page/dashboard_responsable.php");
        break;

    default:
        header("Location: login.php");
}

exit();