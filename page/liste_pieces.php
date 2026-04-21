<?php

$total_pieces = Query::CRUD("SELECT COUNT(*) total FROM pieces")->fetch(PDO::FETCH_ASSOC)['total'];

$total_stock = Query::CRUD("SELECT SUM(stock) total FROM pieces")->fetch(PDO::FETCH_ASSOC)['total'];

$stock_faible = Query::CRUD("SELECT COUNT(*) total FROM pieces WHERE stock <= seuil AND stock > 0")->fetch(PDO::FETCH_ASSOC)['total'];

$rupture = Query::CRUD("SELECT COUNT(*) total FROM pieces WHERE stock = 0")->fetch(PDO::FETCH_ASSOC)['total'];

/* ============================
   GENERATION CODE AUTOMATIQUE
============================ */

if(isset($_POST['ajouter'])){
    $nom = Piece::keys()->encrypt(trim(Query::securisation($_POST['nom'])));
    $exist = Query::CRUD("SELECT * FROM `pieces` WHERE `nom`='$nom'")->rowCount();
    
    if($exist) {
       header("Location:index.php?page=pieces&log=true");
        exit(); 
    } else {
        $piece = new Piece(
            null,
            genererCodePiece(Query::securisation($_POST['sous_categorie'])),
            Query::securisation($_POST['nom']),
            Query::securisation($_POST['type']),
            Query::securisation($_POST['categorie']),
            Query::securisation($_POST['sous_categorie']),
            Query::securisation($_POST['unite']),
            Query::securisation($_POST['reference']),
            Query::securisation($_POST['compatibilite']),
            Query::securisation($_POST['emplacement']),
            0,
            Query::securisation($_POST['stock_min']),
            Query::securisation($_POST['stock_max']),
            Query::securisation($_POST['seuil']),
            Query::securisation($_POST['prix']),
            Query::securisation($_POST['criticite']),
            Query::securisation($_POST['classe_abc']),
            Query::securisation($_POST['numero_lot'])
            
        );
    
        $piece->ajouter();
    
        header("Location:index.php?page=pieces");
        exit();
    }
}

function genererCodePiece($type){

    $prefix = strtoupper(substr($type,0,3));

    $count = Query::CRUD("SELECT COUNT(*) as total FROM pieces")
             ->fetch(PDO::FETCH_ASSOC)['total'] + 1;

    $numero = str_pad($count,4,"0",STR_PAD_LEFT);

    return $prefix."-".$numero;
}



/* ============================
   MODIFICATION
============================ */

if(isset($_POST['action']) && $_POST['action']=="modifier"){

     $piece = new Piece(
        Query::securisation($_POST['id']),
        genererCodePiece(Query::securisation($_POST['sous_categorie'])),
        Query::securisation($_POST['nom']),
        Query::securisation($_POST['type']),
        Query::securisation($_POST['categorie']),
        Query::securisation($_POST['sous_categorie']),
        Query::securisation($_POST['unite']),
        Query::securisation($_POST['reference']),
        Query::securisation($_POST['compatibilite']),
        Query::securisation($_POST['emplacement']),
        0,
        Query::securisation($_POST['stock_min']),
        Query::securisation($_POST['stock_max']),
        Query::securisation($_POST['seuil']),
        Query::securisation($_POST['prix']),
        Query::securisation($_POST['criticite']),
        Query::securisation($_POST['classe_abc']),
        Query::securisation($_POST['numero_lot'])
        
    );


    $piece->modifier();

    header("Location:index.php?page=pieces");
    exit();
}


/* ============================
   SUPPRESSION
============================ */

if(isset($_GET['supprimer'])){

    $id = (int)$_GET['supprimer'];

    $piece = Piece::supprimer($id);

    header("Location:index.php?page=pieces");
    exit();
}


/* ============================
   RECUPERATION
============================ */

$pieces = Piece::affichers("SELECT * FROM pieces ORDER BY id DESC");

?>



<div class="d-flex justify-content-between align-items-center mb-3">

<h4>
<i class="bi bi-box-seam"></i>
Gestion des pièces
</h4>
<?php
    if (isset($_GET['log'])) {
        echo '<div class="alert alert-danger">La pièce existe déjà. Merci</div>';
    }

?>
<button class="btn btn-success"
data-bs-toggle="modal"
data-bs-target="#modalAjouter">

<i class="bi bi-plus-circle"></i>
Ajouter une pièce

</button>

</div>




<div class="card shadow border-0">

<div class="card-header bg-dark text-white">

Liste des pièces
<span class="badge bg-light text-dark">
<?= count($pieces) ?>
</span>

</div>



<div class="card-body p-0">

<div class="table-responsive">
<div class="row mb-3">

<div class="col-md-3">

<select id="filtreStock" class="form-select">

<option value="all">Toutes les pièces</option>

<option value="stock">Pièces en stock</option>

<option value="faible">Stock faible (≤ seuil)</option>

<option value="rupture">Rupture de stock</option>

</select>

</div>

</div>
<div class="row mb-4">

<div class="col-md-3">

<div class="card shadow border-0 bg-primary text-white">

<div class="card-body text-center">

<h6>Total pièces</h6>

<h3><?= $total_pieces ?></h3>

</div>

</div>

</div>


<div class="col-md-3">

<div class="card shadow border-0 bg-success text-white">

<div class="card-body text-center">

<h6>Stock total</h6>

<h3><?= $total_stock ?></h3>

</div>

</div>

</div>


<div class="col-md-3">

<div class="card shadow border-0 bg-warning text-dark">

<div class="card-body text-center">

<h6>Stock faible</h6>

<h3><?= $stock_faible ?></h3>

</div>

</div>

</div>


<div class="col-md-3">

<div class="card shadow border-0 bg-danger text-white">

<div class="card-body text-center">

<h6>Rupture de stock</h6>

<h3><?= $rupture ?></h3>

</div>

</div>

</div>

</div>
<table id="tablePieces" class="table table-hover align-middle">

<thead class="table-dark">
<tr>

<th>#</th>
<th>Code</th>
<th>Nom</th>
<th>Catégorie</th>
<th>Sous-catégorie</th>
<th>Unité</th>
<th>Stock</th>
<th>Stock Seuil</th>
<th>Stock Min</th>
<th>Stock Max</th>
<th>Prix</th>
<th>Criticité</th>
<th>Emplacement</th>
<th>Actions</th>

</tr>
</thead>

<tbody>

<?php $i=1;
foreach($pieces as $p){ ?>

<tr data-stock="<?= $p->getStock(); ?>" data-seuil="<?= $p->getSeuil(); ?>">
<td><?= $i++; ?></td>

<td>
<span class="badge bg-secondary">
<?= $p->getCode(); ?>
</span>
</td>

<td><?= $p->getNom(); ?></td>

<td><?= $p->getCategorie(); ?></td>
<td><?= $p->getSousCategorie(); ?></td>

<td><?= $p->getUnite(); ?></td>

<td>
<span class="badge bg-success">
<?= $p->getStock(); ?>
</span>
</td>
<td><?= $p->getSeuil(); ?></td>
<td><?= $p->getStockMin(); ?></td>

<td><?= $p->getStockMax(); ?></td>

<td><?= $p->getPrix(); ?> $</td>

<td>
<span class="badge bg-warning">
<?= $p->getCriticite(); ?>
</span>
</td>

<td><?= $p->getEmplacement(); ?></td>

<td>

<button class="btn btn-sm btn-primary"
data-bs-toggle="modal"
data-bs-target="#modalModifier<?= $p->getId(); ?>">

<i class="bi bi-pencil"></i>

</button>

<a href="index.php?page=pieces&supprimer=<?= $p->getId(); ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Supprimer cette pièce ?')">

<i class="bi bi-trash"></i>

</a>

</td>

</tr>
<div class="modal fade" id="modalModifier<?= $p->getId(); ?>">
<div class="modal-dialog modal-xl">
<div class="modal-content">

<form method="post">

<input type="hidden" name="action" value="modifier">
<input type="hidden" name="id" value="<?= $p->getId(); ?>">

<div class="modal-header bg-primary text-white">

<h5 class="modal-title">
<i class="bi bi-pencil-square"></i>
Modifier la pièce
</h5>

<button type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal"></button>

</div>


<div class="modal-body">

<!-- ======================== -->
<!-- INFORMATIONS GENERALES -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mb-3">Informations générales</h6>

<div class="row g-3">

<div class="col-md-3">
<label>Code</label>
<input type="text"
name="code"
class="form-control"
value="<?= $p->getCode(); ?>">
</div>

<div class="col-md-3">
<label>Nom</label>
<input type="text"
name="nom"
class="form-control"
value="<?= $p->getNom(); ?>">
</div>

<div class="col-md-3">
<label>Catégorie</label>
<input type="text"
name="categorie"
class="form-control"
value="<?= $p->getCategorie(); ?>">
</div>

<div class="col-md-3">
<label>Sous catégorie</label>
<input type="text"
name="sous_categorie"
class="form-control"
value="<?= $p->getSousCategorie(); ?>">
</div>

<div class="col-md-3">
<label>Unité</label>
<input type="text"
name="unite"
class="form-control"
value="<?= $p->getUnite(); ?>">
</div>

<div class="col-md-6">
<label>Spécifications techniques</label>
<input type="text"
name="type"
class="form-control"
value="<?= $p->getType(); ?>">
</div>

</div>


<!-- ======================== -->
<!-- INFORMATIONS TECHNIQUES -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mt-4 mb-3">Informations techniques</h6>

<div class="row g-3">

<div class="col-md-4">
<label>Référence fabricant</label>
<input type="text"
name="reference"
class="form-control"
value="<?= $p->getReference(); ?>">
</div>

<div class="col-md-4">
<label>Compatibilité machine</label>
<input type="text"
name="compatibilite"
class="form-control"
value="<?= $p->getCompatibilite(); ?>">
</div>

<div class="col-md-4">
<label>Emplacement</label>
<input type="text"
name="emplacement"
class="form-control"
value="<?= $p->getEmplacement(); ?>">
</div>

<div class="col-md-4">
<label>Numéro de lot</label>
<input type="text"
name="numero_lot"
class="form-control"
value="<?= $p->getNumeroLot(); ?>">
</div>

</div>


<!-- ======================== -->
<!-- PARAMETRES STOCK -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mt-4 mb-3">Paramètres de stock</h6>

<div class="row g-3">

<div class="col-md-3">
<label>Stock</label>
<input type="number"
name="stock"
class="form-control"
value="<?= $p->getStock(); ?>">
</div>

<div class="col-md-3">
<label>Stock minimum</label>
<input type="number"
name="stock_min"
class="form-control"
value="<?= $p->getStockMin(); ?>">
</div>

<div class="col-md-3">
<label>Stock maximum</label>
<input type="number"
name="stock_max"
class="form-control"
value="<?= $p->getStockMax(); ?>">
</div>

<div class="col-md-3">
<label>Seuil d'alerte</label>
<input type="number"
name="seuil"
class="form-control"
value="<?= $p->getSeuil(); ?>">
</div>

</div>


<!-- ======================== -->
<!-- PARAMETRES FINANCIERS -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mt-4 mb-3">Informations financières</h6>

<div class="row g-3">

<div class="col-md-3">
<label>Prix</label>
<input type="number"
step="0.01"
name="prix"
class="form-control"
value="<?= $p->getPrix(); ?>">
</div>

<div class="col-md-3">
<label>Criticité</label>
<input type="text"
name="criticite"
class="form-control"
value="<?= $p->getCriticite(); ?>">
</div>

<div class="col-md-3">
<label>Classe ABC</label>
<input type="text"
name="classe_abc"
class="form-control"
value="<?= $p->getClasseABC(); ?>">
</div>

</div>

</div>


<div class="modal-footer">

<button class="btn btn-success">

<i class="bi bi-check-circle"></i>
Enregistrer

</button>

</div>

</form>

</div>
</div>
</div>

<?php } ?>

</tbody>
</table>

</div>
</div>
</div>



<!-- MODAL AJOUT -->

<div class="modal fade" id="modalAjouter" tabindex="-1">
<div class="modal-dialog modal-xl">
<div class="modal-content">

<form method="post" action="#">

<!-- HEADER -->
<div class="modal-header bg-dark text-white">
<h5 class="modal-title">
<i class="bi bi-box-seam"></i> Ajouter une pièce
</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>


<div class="modal-body">

<!-- ======================== -->
<!-- INFORMATIONS GENERALES -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mb-3">Informations générales</h6>

<div class="row g-3">


<div class="col-md-4">
<label class="form-label">Nom</label>
<input type="text" name="nom" class="form-control">
</div>


<div class="col-md-3">
<label class="form-label">Catégorie</label>
<select name="categorie" class="form-select">
<option value="Consommable">Consommable</option>
<option value="Pièce critique">Pièce critique</option>
<option value="EPI">EPI</option>
<option value="Carburant">Carburant</option>
<option value="Explosif">Explosif</option>
<option value="Outil">Outil</option>
<option value="Equipement lourd">Equipement lourd</option>
</select>
</div>

<div class="col-md-3">
<label class="form-label">Sous-catégorie</label>
<select name="sous_categorie" class="form-select">
<option value="Mécanique">Mécanique</option>
<option value="Electrique">Electrique</option>
<option value="Hydraulique">Hydraulique</option>
<option value="Sécurité">Sécurité</option>
<option value="Maintenance">Maintenance</option>
<option value="Genie-Civil">Genie-Civil</option>
<option value="Soudure">Soudure</option>
<option value="Electrique">Electrique</option>
<option value="EPI">EPI</option>
<option value="Service Crew">Service Crew</option>
</select>
</div>

<div class="col-md-2">
<label class="form-label">Unité de mesure</label>
<select name="unite" class="form-select">
<option value="Pièce">Pièce</option>
<option value="Litre">Litre</option>
<option value="Mètre">Mètre</option>
<option value="Kg">Kg</option>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Spécifications techniques</label>
<select name="type" class="form-select">
<option value="Pression">Pression</option>
<option value="Diamètre">Diamètre</option>
<option value="Voltage">Voltage</option>
<option value="Norme ISO">Norme ISO</option>
</select>
</div>

</div>


<!-- ======================== -->
<!-- INFORMATIONS TECHNIQUES -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mt-4 mb-3">Informations techniques</h6>

<div class="row g-3">

<div class="col-md-4">
<label class="form-label">Référence fabricant</label>
<input type="text" name="reference" class="form-control">
</div>

<div class="col-md-4">
<label class="form-label">Modèle / Compatibilité machine</label>
<input type="text" name="compatibilite" class="form-control">
</div>

<div class="col-md-4">
<label class="form-label">Numéro de serie</label>
<input type="text" name="emplacement" class="form-control">
</div>

<div class="col-md-4">
<label class="form-label">Numéro de lot</label>
<input type="text" name="numero_lot" class="form-control">
</div>

</div>


<!-- ======================== -->
<!-- PARAMETRES STOCK -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mt-4 mb-3">Paramètres de stock</h6>

<div class="row g-3">

<div class="col-md-3">
<label class="form-label">Stock minimum</label>
<input type="number" name="stock_min" class="form-control">
</div>

<div class="col-md-3">
<label class="form-label">Stock maximum</label>
<input type="number" name="stock_max" class="form-control">
</div>

<div class="col-md-3">
<label class="form-label">Seuil d'alerte</label>
<input type="number" name="seuil" class="form-control">
</div>

</div>


<!-- ======================== -->
<!-- PARAMETRES FINANCIERS -->
<!-- ======================== -->

<h6 class="border-bottom pb-2 mt-4 mb-3">Informations financières</h6>

<div class="row g-3">

<div class="col-md-3">
<label class="form-label">Prix unitaire</label>
<input type="number" step="0.01" name="prix" class="form-control">
</div>

<div class="col-md-3">
<label class="form-label">Criticité</label>
<select name="criticite" class="form-select">
<option>Standard</option>
<option>Stratégique</option>
<option>Critique</option>
</select>
</div>

<div class="col-md-3">
<label class="form-label">Classe ABC</label>
<select name="classe_abc" class="form-select">
<option>A</option>
<option>B</option>
<option>C</option>
</select>
</div>

</div>

</div>


<!-- FOOTER -->

<div class="modal-footer">

<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Annuler
</button>

<button type="submit" class="btn btn-success" name="ajouter">
<i class="bi bi-check-circle"></i> Enregistrer
</button>

</div>

</form>

</div>
</div>
</div>



<script>

$(document).ready(function(){

$('#tablePieces').DataTable({

dom:'Bfrtip',

buttons:[
'copy',
'excel',
'pdf',
'print'
],

pageLength:100,

order:[[0,'desc']],

language:{
url:"https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
}

});

});
$('#filtreStock').on('change', function(){

let type = $(this).val();

$('#tablePieces tbody tr').each(function(){

let stock = parseInt($(this).data('stock'));
let seuil = parseInt($(this).data('seuil'));

let afficher = true;

if(type === "stock"){
afficher = stock > 0;
}

if(type === "faible"){
afficher = stock <= seuil;
}

if(type === "rupture"){
afficher = stock === 0;
}

$(this).toggle(afficher);

});

});
</script>