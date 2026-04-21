<?php

// Alertes
$rupture = Query::CRUD("SELECT COUNT(*) as total FROM pieces WHERE stock = 0")->fetch(PDO::FETCH_ASSOC)['total'];
$faible = Query::CRUD("SELECT COUNT(*) as total FROM pieces WHERE stock <= seuil AND stock > 0")->fetch(PDO::FETCH_ASSOC)['total'];

// Mouvements récents
$mouvements = Query::CRUD("
    SELECT p.nom, e.quantite, e.date_entree 
    FROM entrees e
    JOIN pieces p ON p.id = e.piece_id
    ORDER BY e.date_entree DESC LIMIT 5
");
?>

<h2 class="mb-4">📦 Dashboard Magasinier</h2>

<div class="row">

    <div class="col-md-6">
        <div class="card bg-danger text-white shadow">
            <div class="card-body">
                <h5>Produits en rupture</h5>
                <h2><?php echo $rupture; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-warning shadow">
            <div class="card-body">
                <h5>Stock faible</h5>
                <h2><?php echo $faible; ?></h2>
            </div>
        </div>
    </div>

</div>

<div class="card mt-4 shadow">
    <div class="card-header">🕒 Derniers mouvements</div>
    <div class="card-body">
        <ul>
            <?php while($m = $mouvements->fetch(PDO::FETCH_ASSOC)): ?>
                <li><?php echo Piece::keys()->decrypt($m['nom']); ?> - <?php echo $m['quantite']; ?> pièces (<?php echo $m['date_entree']; ?>)</li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

</div>
</body>
</html>