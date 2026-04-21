<div class="card shadow border-0 mb-4">

<div class="card-header bg-dark text-white fw-semibold">
📊 Générer un rapport de stock
</div>

<div class="card-body">

<form method="POST" action="rapport_journalier.php">

<div class="row g-3">

<!-- Type de rapport -->
<div class="col-md-3">

<label class="form-label">Type de rapport</label>

<select name="periode" id="periode" class="form-select">

<option value="journalier">Journalier</option>
<option value="mensuel">Mensuel</option>
<option value="annuel">Annuel</option>

</select>

</div>

<!-- Date -->
<div class="col-md-3" id="blocDate">

<label class="form-label">Date</label>

<input type="date"
name="date"
class="form-control"
value="<?= date('Y-m-d') ?>">

</div>

<!-- Mois -->
<div class="col-md-3 d-none" id="blocMois">

<label class="form-label">Mois</label>

<input type="month"
name="mois"
class="form-control">

</div>

<!-- Année -->
<div class="col-md-3 d-none" id="blocAnnee">

<label class="form-label">Année</label>

<select name="annee" class="form-select">

<?php
for($i=date("Y"); $i>=2020; $i--){
echo "<option value='$i'>$i</option>";
}
?>

</select>

</div>


<!-- Produit -->
<div class="col-md-3">

<label class="form-label">Produit</label>

<select name="piece_id" class="form-select">

<option value="">Tous les produits</option>

<?php
$pieces = Piece::affichers("SELECT * FROM pieces");

foreach($pieces as $p){
?>

<option value="<?= $p->getId(); ?>">
<?= $p->getNom(); ?>
</option>

<?php } ?>

</select>

</div>


<!-- Type mouvement -->
<div class="col-md-3">

<label class="form-label">Type de mouvement</label>

<select name="type" class="form-select">

<option value="">Tous</option>
<option value="entree">Entrées</option>
<option value="sortie">Sorties</option>

</select>

</div>


<!-- Bouton -->
<div class="col-md-3 d-flex align-items-end">

<button class="btn btn-primary w-100">

<i class="bi bi-file-earmark-text"></i>
Générer le rapport

</button>

</div>

</div>

</form>

</div>

</div>
<script>

document.getElementById("periode").addEventListener("change", function(){

let type = this.value;

let blocDate = document.getElementById("blocDate");
let blocMois = document.getElementById("blocMois");
let blocAnnee = document.getElementById("blocAnnee");

/* cacher tous les champs */

blocDate.classList.add("d-none");
blocMois.classList.add("d-none");
blocAnnee.classList.add("d-none");

/* afficher selon le choix */

if(type === "journalier"){
blocDate.classList.remove("d-none");
}

if(type === "mensuel"){
blocMois.classList.remove("d-none");
}

if(type === "annuel"){
blocAnnee.classList.remove("d-none");
}

});

</script>