<?php
$role = $_SESSION['user']['role'];
?>

<?php if($role == 'ADMIN'): ?>

    <!-- Dashboard ADMIN -->
    <h5>Vue Administrateur</h5>
    <?php include 'dashboard_admin.php'; ?>

<?php elseif($role == 'MAGASINIER'): ?>

    <!-- Dashboard MAGASINIER -->
    <h5>Vue Magasinier</h5>
    <?php include 'dashboard_magasinier.php'; ?>

<?php elseif($role == 'RESPONSABLE'): ?>

    <!-- Dashboard RESPONSABLE -->
    <h5>Vue Responsable</h5>
    <?php include 'dashboard_responsable.php'; ?>

<?php endif; ?>