<?php
require_once "header.php";

$id = (int)$_GET['id'];

/* BON */

$bon = Query::CRUD("
SELECT *
FROM bon_sortie
WHERE id=$id
")->fetch(PDO::FETCH_ASSOC);

/* LIGNES */

$lignes = Query::CRUD("
SELECT l.*, p.*
FROM bon_sortie_lignes l
JOIN pieces p ON p.id=l.piece_id
WHERE l.bon_id=$id
");

$numero = "BS-".date("Y")."-".str_pad($bon['id'],4,"0",STR_PAD_LEFT);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Bon de sortie</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
padding:30px;
font-family:Arial;
}

.document{
background:white;
padding:40px;
border:1px solid #ddd;
}

.header{
border-bottom:2px solid #000;
padding-bottom:15px;
margin-bottom:25px;
}

.logo{
font-size:22px;
font-weight:bold;
}

.title{
font-size:22px;
font-weight:bold;
text-align:center;
margin-top:10px;
}

.signature{
margin-top:70px;
}

.signature-line{
border-top:1px solid #000;
width:220px;
margin:auto;
margin-top:40px;
}

@media print{

body{
background:white;
padding:0;
}

.prints{
display:none;
}

.document{
border:none;
}

}

</style>

</head>

<body>

<div class="container">

<div class="document">

<!-- ENTETE -->

<div class="row header">

<div class="col-md-6">

<div class="logo">

CCET

</div>

Service Logistique

</div>

<div class="col-md-6 text-end">

<strong>Bon de sortie</strong><br>

N° : <?= $numero ?><br>

Date : <?= date("d/m/Y",strtotime($bon['date_sortie'])) ?>

</div>

</div>


<!-- TITRE -->

<div class="title">

BON DE SORTIE DE STOCK

</div>


<!-- TABLEAU -->

<table class="table table-bordered mt-4">

<thead class="table-light">

<tr>

<th width="40">#</th>
<th>Code produit</th>
<th>Désignation</th>
<th width="120">Quantité</th>
<th width="120">Stock initial</th>
<th width="120">Stock après</th>
<th>Motif</th>

</tr>

</thead>

<tbody>

<?php
$i = 1;

while($l=$lignes->fetch(PDO::FETCH_ASSOC)){

$nom_piece = Piece::keys()->decrypt($l['nom']);
$code = Piece::keys()->decrypt($l['code']);

$stock_apres = $l['stock'];
$stock_avant = $l['stock'] + $l['quantite'];

?>

<tr>

<td><?= $i++ ?></td>

<td><?= $code ?></td>

<td><?= $nom_piece ?></td>

<td class="text-center">

<?= $l['quantite'] ?>

</td>

<td class="text-center">

<?= $stock_avant ?>

</td>

<td class="text-center">

<?= $stock_apres ?>

</td>

<td>

<?= $l['motif'] ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>


<!-- SIGNATURES -->

<div class="row signature text-center">

<div class="col-md-4">

Magasinier

<div class="signature-line"></div>

</div>

<div class="col-md-4">

Responsable logistique

<div class="signature-line"></div>

</div>

<div class="col-md-4">

Demandeur

<div class="signature-line"></div>

</div>

</div>

</div>


<div class="text-center mt-4">

<button onclick="window.print()"  class="btn btn-primary prints">

🖨 Imprimer le bon

</button>

<a href="index.php?page=sorties" class="btn btn-secondary prints">

Retour

</a>

</div>

</div>

</body>
</html>
