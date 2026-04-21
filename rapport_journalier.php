<?php
require_once "header.php";

/* =============================
PARAMETRES
============================= */

$periode = $_POST['periode'] ?? 'journalier';
$date = $_POST['date'] ?? date("Y-m-d");

$mois  = (new DateTime($date))->format('Y-m');
$annee = (new DateTime($date))->format('Y');


/* =============================
CONDITIONS
============================= */

if($periode == "journalier"){

$condition_entree = "DATE(e.date_entree) = '$date'";
$condition_sortie = "DATE(s.date_sortie) = '$date'";

$titre = "Rapport journalier du ".date("d/m/Y",strtotime($date));

}

elseif($periode == "mensuel"){

$condition_entree = "DATE_FORMAT(e.date_entree,'%Y-%m') = '$mois'";
$condition_sortie = "DATE_FORMAT(s.date_sortie,'%Y-%m') = '$mois'";

$titre = "Rapport mensuel : ".$mois;

}

else{

$condition_entree = "YEAR(e.date_entree) = '$annee'";
$condition_sortie = "YEAR(s.date_sortie) = '$annee'";

$titre = "Rapport annuel : ".$annee;

}


/* =============================
STOCK INITIAL AVANT PERIODE
============================= */

$stocks = [];

$stockInitial = Query::CRUD("

SELECT

p.id,

(
SELECT IFNULL(SUM(e.quantite),0)
FROM entrees e
WHERE e.piece_id=p.id
AND DATE(e.date_entree) < '$date'
)

-

(
SELECT IFNULL(SUM(s.quantite),0)
FROM sorties s
WHERE s.piece_id=p.id
AND DATE(s.date_sortie) < '$date'
)

AS stock_initial

FROM pieces p

");

while($st = $stockInitial->fetch(PDO::FETCH_ASSOC)){

$stocks[$st['id']] = $st['stock_initial'];

}


/* =============================
REQUETE ENTREES
============================= */

$entrees = Query::CRUD("

SELECT
e.id,
e.piece_id,
e.quantite,
e.date_entree,
e.idusers,
p.nom,
p.code,
f.nom AS fournisseur

FROM entrees e

JOIN pieces p ON p.id = e.piece_id
LEFT JOIN fournisseurs f ON f.id = e.id_four

WHERE $condition_entree

ORDER BY e.date_entree ASC

");


/* =============================
REQUETE SORTIES
============================= */

$sorties = Query::CRUD("

SELECT
s.id,
s.piece_id,
s.quantite,
s.motif,
s.date_sortie,
p.nom,
p.code

FROM sorties s

JOIN pieces p ON p.id = s.piece_id

WHERE $condition_sortie

ORDER BY s.date_sortie ASC

");


$total_entrees = 0;
$total_sorties = 0;

$stockCourant = $stocks;

?>
<!DOCTYPE html>
<html>

<head>

<title>Rapport Stock</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

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

</head>


<body class="bg-light">

<div class="container mt-4">

<h3 class="text-center mb-4">

📊 <?= $titre ?>

</h3>


<!-- =============================
TABLEAU ENTREES
============================= -->

<div class="card shadow mb-4">

<div class="card-header bg-success text-white">
📥 Mouvements d'Entrées
</div>

<div class="card-body">

<table id="tableEntrees" class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Date</th>
<th>Code</th>
<th>Produit</th>
<th>Stock initial</th>
<th>Entrée</th>
<th>Stock final</th>
<th>Fournisseur</th>
<th>Encodeur</th>

</tr>

</thead>

<tbody>

<?php

while($e=$entrees->fetch(PDO::FETCH_ASSOC)){

$nom = Piece::keys()->decrypt($e['nom']);
$code = Piece::keys()->decrypt($e['code']);

$piece_id = $e['piece_id'];

if(!isset($stockCourant[$piece_id])){
$stockCourant[$piece_id] = 0;
}

$stock_initial = $stockCourant[$piece_id];

$stock_final = $stock_initial + $e['quantite'];

$stockCourant[$piece_id] = $stock_final;

$total_entrees += $e['quantite'];

$user = "-";

if($e['idusers']){

$user = Connexions::keys()->decrypt(
Query::CRUD("SELECT username FROM connexion WHERE id=".$e['idusers'])
->fetch(PDO::FETCH_OBJ)->username
);

}

?>

<tr>

<td><?= date("d/m/Y",strtotime($e['date_entree'])) ?></td>

<td><?= $code ?></td>

<td><?= $nom ?></td>

<td><?= $stock_initial ?></td>

<td class="text-success fw-bold">
+ <?= $e['quantite'] ?>
</td>

<td class="fw-bold text-primary">
<?= $stock_final ?>
</td>

<td><?= $e['fournisseur'] ? Fournisseur::keys()->decrypt($e['fournisseur']) : '-' ?></td>

<td><?= $user ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>


<!-- =============================
TABLEAU SORTIES
============================= -->

<div class="card shadow">

<div class="card-header bg-danger text-white">
📤 Mouvements de Sorties
</div>

<div class="card-body">

<table id="tableSorties" class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Date</th>
<th>Code</th>
<th>Produit</th>
<th>Stock initial</th>
<th>Sortie</th>
<th>Stock final</th>
<th>Motif</th>

</tr>

</thead>

<tbody>

<?php

while($s=$sorties->fetch(PDO::FETCH_ASSOC)){

$nom = Piece::keys()->decrypt($s['nom']);
$code = Piece::keys()->decrypt($s['code']);

$piece_id = $s['piece_id'];

if(!isset($stockCourant[$piece_id])){
$stockCourant[$piece_id] = 0;
}

$stock_initial = $stockCourant[$piece_id];

$stock_final = $stock_initial - $s['quantite'];

$stockCourant[$piece_id] = $stock_final;

$total_sorties += $s['quantite'];

?>

<tr>

<td><?= date("d/m/Y",strtotime($s['date_sortie'])) ?></td>

<td><?= $code ?></td>

<td><?= $nom ?></td>

<td><?= $stock_initial ?></td>

<td class="text-danger fw-bold">
- <?= $s['quantite'] ?>
</td>

<td class="fw-bold text-primary">
<?= $stock_final ?>
</td>

<td>
<span class="badge bg-secondary">
<?= $s['motif'] ?>
</span>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>


<!-- =============================
TOTAL
============================= -->

<div class="row mt-4">

<div class="col-md-6">

<div class="card border-success shadow">

<div class="card-body text-center">

<h5>Total Entrées</h5>

<h3 class="text-success">

<?= $total_entrees ?>

</h3>

</div>

</div>

</div>


<div class="col-md-6">

<div class="card border-danger shadow">

<div class="card-body text-center">

<h5>Total Sorties</h5>

<h3 class="text-danger">

<?= $total_sorties ?>

</h3>

</div>

</div>

</div>

</div>


</div>


<script>

$(document).ready(function(){

$('#tableEntrees').DataTable({

dom:'Bfrtip',

buttons:['copy','excel','pdf','print'],

pageLength:50,

language:{
url:"https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
}

});


$('#tableSorties').DataTable({

dom:'Bfrtip',

buttons:['copy','excel','pdf','print'],

pageLength:50,

language:{
url:"https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
}

});

});

</script>

</body>
</html>