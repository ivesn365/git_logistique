<?php

$pieces = Piece::affichers("SELECT * FROM pieces");

if(isset($_POST['ajouter'])){

$date = Query::securisation($_POST['date_sortie']);

/* creation bon */

Query::CRUD("
INSERT INTO bon_sortie(date_sortie)
VALUES('$date')
");

/* recuperer ID */

$bon_id = Query::CRUD("SELECT LAST_INSERT_ID() as id")
->fetch(PDO::FETCH_ASSOC)['id'];


/* insertion des lignes */

foreach($_POST['piece_id'] as $i => $piece_id){

$piece_id = Query::securisation($piece_id);
$quantite = Query::securisation($_POST['quantite'][$i]);
$motif = Query::securisation($_POST['motif'][$i]);

Query::CRUD("
INSERT INTO bon_sortie_lignes
(bon_id,piece_id,quantite,motif)
VALUES('$bon_id','$piece_id','$quantite','$motif')
");
$sortie = new Sortie(null,$piece_id,$quantite,$date,$motif);
$sortie->ajouter();

/* mise à jour stock */

Query::CRUD("
UPDATE pieces
SET stock = stock - '$quantite'
WHERE id='$piece_id'
");

}

/* redirection vers bon */

header("Location:bon_sortie.php?id=".$bon_id);
exit();

}

/* ============================
   MODIFIER SORTIE
============================ */
if(isset($_POST['action']) && $_POST['action']=="modifier"){

$id = (int)$_POST['id'];
$qte = (int)$_POST['quantite'];
$date = Query::securisation($_POST['date_sortie']);
$motif = Query::securisation($_POST['motif']);

// ancienne valeur
$old = Query::CRUD("SELECT quantite, piece_id FROM sorties WHERE id=$id")
->fetch(PDO::FETCH_ASSOC);

// recalcul stock
$diff = $qte - $old['quantite'];

Query::CRUD("
UPDATE pieces 
SET stock = stock - ($diff) 
WHERE id=".$old['piece_id']
);

// update sortie
Query::CRUD("
UPDATE sorties 
SET quantite='$qte',
motif='$motif',
date_sortie='$date'
WHERE id='$id'
");

header("Location:index.php?page=sorties");
exit();
}


/* ============================
   SUPPRIMER SORTIE
============================ */
if(isset($_GET['supprimer'])){

$id = (int)$_GET['supprimer'];

// récupérer données
$data = Query::CRUD("SELECT quantite, piece_id FROM sorties WHERE id=$id")
->fetch(PDO::FETCH_ASSOC);

// remettre le stock
Query::CRUD("
UPDATE pieces 
SET stock = stock + ".$data['quantite']." 
WHERE id=".$data['piece_id']
);

// supprimer
Query::CRUD("DELETE FROM sorties WHERE id=$id");

header("Location:index.php?page=sorties");
exit();
}
?>

<h2 class="mb-4">⬆ Gestion des Sorties</h2>

<div class="card shadow border-0 mb-4">
    <div class="card-header bg-danger text-white fw-semibold">
        <i class="bi bi-arrow-up-circle"></i>
        Bon de sortie de stock
    </div>

    <div class="card-body">
     <form method="post">

<div class="card shadow border-0 mb-4">

<div class="card-header bg-danger text-white">

Nouvelle sortie de stock

</div>

<div class="card-body">

<div id="ligneArticles">

<div class="row g-3 article-ligne mb-2">

<div class="col-md-4">

<select name="piece_id[]" class="form-select select-piece" required>

<option value="">Rechercher un article...</option>

<?php foreach($pieces as $p): ?>

<option value="<?= $p->getId(); ?>">

<?= $p->getNom(); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-md-2">

<input type="number"
name="quantite[]"
class="form-control"
placeholder="Quantité"
required>

</div>

<div class="col-md-4">

<input type="text"
name="motif[]"
class="form-control"
placeholder="Motif">

</div>

<div class="col-md-2">

<button type="button"
class="btn btn-danger supprimer-ligne">

Supprimer

</button>

</div>

</div>

</div>


<div class="row mt-3">

<div class="col-md-3">

<label>Date</label>

<input type="date"
name="date_sortie"
class="form-control"
required>

</div>
</div>


<div class="row mt-3">
    <div class="col-md-3">
    <button type="button"
class="btn btn-primary"
id="ajouterLigne">

+ Ajouter article

</button>
</div>
<div class="col-md-3">

<button type="submit"
name="ajouter"
class="btn btn-danger">

Enregistrer le bon

</button>

</div>

</div>

</div>

</div>

</form>
    </div>
</div>
<div class="card shadow border-0">
    <div class="card-header bg-danger text-white fw-semibold d-flex justify-content-between">
        <span><i class="bi bi-arrow-up-circle"></i> Historique des Sorties</span>
        <span class="badge bg-light text-dark">
            <?php
            $count = Query::CRUD("SELECT COUNT(*) as total FROM sorties")
                     ->fetch(PDO::FETCH_ASSOC)['total'];
            echo $count . " sorties";
            ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tableSortie" class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Pièce</th>
                        <th class="text-center">Quantité</th>
                        <th>Motif</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $j = 1;
                    $sorties = Query::CRUD("
                        SELECT s.id, s.quantite, s.motif, s.date_sortie,
                               p.nom AS piece_nom
                        FROM sorties s
                        JOIN pieces p ON p.id = s.piece_id
                        ORDER BY s.date_sortie DESC
                    ");

                    while($s = $sorties->fetch(PDO::FETCH_ASSOC)):
                    ?>

                    <tr>
                        <td><?= $j++ ?></td>

                        <td class="fw-semibold">
                            <?= Piece::keys()->decrypt($s['piece_nom']); ?>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-danger">
                                -<?= $s['quantite']; ?>
                            </span>
                        </td>

                        <td>
                            <span class="text-muted">
                                <?= $s['motif']; ?>
                            </span>
                        </td>

                        <td>
                            <?= date("d/m/Y", strtotime($s['date_sortie'])); ?>
                        </td>
                        <td class="text-end">

                            <!-- Modifier -->
                            <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#edit<?= $s['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                        
                            <!-- Supprimer -->
                            <a href="index.php?page=sorties&supprimer=<?= $s['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Supprimer cette sortie ?')">
                                <i class="bi bi-trash"></i>
                            </a>

                        </td>
                    </tr>
                    <div class="modal fade" id="edit<?= $s['id'] ?>">
                    <div class="modal-dialog">
                    <div class="modal-content">
                    
                    <form method="post">
                    
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    
                    <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Modifier sortie</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                    
                    <div class="mb-2">
                    <label>Quantité</label>
                    <input type="number" name="quantite"
                    class="form-control"
                    value="<?= $s['quantite'] ?>" required>
                    </div>
                    
                    <div class="mb-2">
                    <label>Motif</label>
                    <input type="text" name="motif"
                    class="form-control"
                    value="<?= $s['motif'] ?>">
                    </div>
                    
                    <div class="mb-2">
                    <label>Date</label>
                    <input type="date" name="date_sortie"
                    class="form-control"
                    value="<?= $s['date_sortie'] ?>" required>
                    </div>
                    
                    </div>
                    
                    <div class="modal-footer">
                    <button class="btn btn-success">Enregistrer</button>
                    </div>
                    
                    </form>
                    
                    </div>
                    </div>
                    </div>

                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

</div>
<script>

$('#ajouterLigne').click(function(){

let ligne = $('.article-ligne:first').clone();

/* vider les champs */

ligne.find('input').val('');
ligne.find('select').val('');

/* supprimer select2 du clone */

ligne.find('.select2-container').remove();
ligne.find('select').removeClass('select2-hidden-accessible');
ligne.find('select').removeAttr('data-select2-id');

/* ajouter ligne */

$('#ligneArticles').append(ligne);

/* réactiver select2 */

$('.select-piece').select2({
placeholder:"Rechercher un article",
width:'100%'
});

});



$(document).ready(function(){

$('.select-piece').select2({

placeholder: "Rechercher un article",

allowClear: true,

width: '100%'

});

});

    $(document).ready(function(){
    
    $('#tableSortie').DataTable({
    
  dom:'Bfrtip',  
buttons:[
'copy',
'excel',
'pdf',
'print'
],
    pageLength:10,
    
    language:{
    url:"https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
    }
    
    });
    
    });
    
    $(document).ready(function(){

    $('.select-piece').select2({
    
    placeholder:"Rechercher une pièce",
    
    width:'100%'
    
    });

});

</script>
</body>
</html>
