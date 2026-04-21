<?php

/* ============================
   KPI PRINCIPAUX
============================ */

$total_stock = Query::CRUD("SELECT SUM(stock) as total FROM pieces")
->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$total_pieces = Query::CRUD("SELECT COUNT(*) as total FROM pieces")
->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$stock_critique = Query::CRUD("SELECT COUNT(*) as total FROM pieces WHERE stock <= stock_min")
->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$valeur_stock = Query::CRUD("SELECT SUM(stock * prix) as total FROM pieces")
->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;


/* ============================
   GRAPH ENTRÉES / SORTIES
============================ */

$mois = [];
$entrees = [];
$sorties = [];

$data = Query::CRUD("
SELECT 
MONTH(date_mouvement) as mois,
SUM(CASE WHEN type_mouvement='entree' THEN quantite ELSE 0 END) as total_entrees,
SUM(CASE WHEN type_mouvement='sortie' THEN quantite ELSE 0 END) as total_sorties
FROM mouvements_stock
GROUP BY MONTH(date_mouvement)
");

while($row=$data->fetch(PDO::FETCH_ASSOC)){

$mois[]=$row['mois'];
$entrees[]=$row['total_entrees'];
$sorties[]=$row['total_sorties'];

}

?>

<h2 class="mb-4">🏢 Dashboard Direction</h2>


<!-- KPI -->

<div class="row g-3 mb-4">

<div class="col-md-3">
<div class="card shadow bg-primary text-white">
<div class="card-body">
<h6>Stock Total</h6>
<h3><?= $total_stock ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow bg-success text-white">
<div class="card-body">
<h6>Produits</h6>
<h3><?= $total_pieces ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow bg-warning text-dark">
<div class="card-body">
<h6>Stock Critique</h6>
<h3><?= $stock_critique ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow bg-dark text-white">
<div class="card-body">
<h6>Valeur Stock</h6>
<h3>$ <?= number_format($valeur_stock,2) ?></h3>
</div>
</div>
</div>

</div>



<!-- GRAPHIQUE -->

<div class="card shadow mb-4">

<div class="card-header">

📈 Entrées vs Sorties mensuelles

</div>

<div class="card-body">

<canvas id="mouvementChart"></canvas>

</div>

</div>



<!-- TOP PRODUITS -->

<div class="card shadow mb-4">

<div class="card-header">

🏆 Top produits consommés

</div>

<div class="card-body">

<table class="table table-striped">

<thead>
<tr>
<th>Produit</th>
<th>Total sorties</th>
</tr>
</thead>

<tbody>

<?php

$top = Query::CRUD("
SELECT p.nom, SUM(m.quantite) as total
FROM mouvements_stock m
JOIN pieces p ON p.id=m.piece_id
WHERE m.type_mouvement='sortie'
GROUP BY p.id
ORDER BY total DESC
LIMIT 10
");

while($t=$top->fetch(PDO::FETCH_ASSOC)){

echo "<tr>
<td>".$t['nom']."</td>
<td>".$t['total']."</td>
</tr>";

}

?>

</tbody>

</table>

</div>

</div>



<!-- STOCK CRITIQUE -->

<div class="card shadow">

<div class="card-header">

⚠ Produits en stock critique

</div>

<div class="card-body">

<table class="table table-hover">

<thead>

<tr>
<th>Code</th>
<th>Produit</th>
<th>Stock</th>
<th>Minimum</th>
</tr>

</thead>

<tbody>

<?php

$critique = Query::CRUD("
SELECT code, nom, stock, stock_min
FROM pieces
WHERE stock <= stock_min
LIMIT 10
");

while($c=$critique->fetch(PDO::FETCH_ASSOC)){

echo "<tr>
<td>".Piece::keys()->decrypt($c['code'])."</td>
<td>".Piece::keys()->decrypt($c['nom'])."</td>
<td>".$c['stock']."</td>
<td>".$c['stock_min']."</td>
</tr>";

}

?>

</tbody>

</table>

</div>

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(document.getElementById('mouvementChart'),{

type:'bar',

data:{

labels:<?= json_encode($mois) ?>,

datasets:[

{
label:'Entrées',
data:<?= json_encode($entrees) ?>,
backgroundColor:"#198754"
},

{
label:'Sorties',
data:<?= json_encode($sorties) ?>,
backgroundColor:"#dc3545"
}

]

}

});

</script>