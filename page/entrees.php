<?php 
$pieces = Piece::affichers("SELECT * FROM pieces");
$fournisseur = Fournisseur::affichers("SELECT * FROM fournisseurs");

if(isset($_POST['ajouter'])){
    $piece_id = Query::securisation($_POST['piece_id']);
    $quantite = Query::securisation($_POST['quantite']);
    $date = Query::securisation($_POST['date_entree']);
    $fournisseur_id = Query::securisation($_POST['fournisseur_id']);

    (new Entree(null, $piece_id, $quantite, $date, $fournisseur_id, $_SESSION['id'], Query::securisation($_POST['date_exp'])))->ajouter(); 
    //Query::CRUD("UPDATE pieces SET stock = stock + '$quantite' WHERE id='$piece_id'");
    
    header("Location:index.php?page=entrees");
}

    /* ============================
       MODIFIER
    ============================ */
    if(isset($_POST['action']) && $_POST['action']=="modifier"){
        $id = (int)$_POST['id'];
        $qte = (int)$_POST['quantite'];
        $date = Query::securisation($_POST['date_entree']);
        $date_exp = Query::securisation($_POST['date_exp']);
        
        // récupérer ancienne quantité
        $old = Query::CRUD("SELECT quantite, piece_id FROM entrees WHERE id=$id")
        ->fetch(PDO::FETCH_ASSOC);
        
        // ajuster stock
        $diff = $qte - $old['quantite'];
        
        Query::CRUD("UPDATE pieces SET stock = stock + ($diff) WHERE id=".$old['piece_id']);
        
        // update entrée
        Query::CRUD("
            UPDATE entrees 
            SET quantite='$qte',
            date_entree='$date',
            date_exp='$date_exp'
            WHERE id='$id'
        ");
        
        header("Location:index.php?page=entrees");
        exit();
    }
    
    
    /* ============================
       SUPPRIMER
    ============================ */
    if(isset($_GET['supprimer'])){
    
    $id = (int)$_GET['supprimer'];
    
    // récupérer infos
    $data = Query::CRUD("SELECT quantite, piece_id FROM entrees WHERE id=$id")
    ->fetch(PDO::FETCH_ASSOC);
    
    // remettre le stock
    Query::CRUD("UPDATE pieces SET stock = stock - ".$data['quantite']." WHERE id=".$data['piece_id']);
    
    // supprimer
    Query::CRUD("DELETE FROM entrees WHERE id=$id");
    
    header("Location:index.php?page=entrees");
    exit();
}
?>

<h2 class="mb-4">⬇ Gestion des Entrées</h2>

<div class="card shadow border-0 mb-4">
    <div class="card-header bg-success text-white fw-semibold">
        ➕  Approvisionnement    stock</div>
  
    <div class="card-body">
        <form method="post">
            <div class="row g-3 align-items-end">

                <!-- Pièce -->
                <div class="col-md-3">
                    <label class="form-label">Pièce</label>
                    <select name="piece_id" class="form-control select-piece" required>
                        <option value="">Sélectionnez une pièce</option>
                        <?php foreach($pieces as $p): ?>
                            <option value="<?= $p->getId(); ?>">
                                <?= $p->getNom(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Quantité -->
                <div class="col-md-2">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite"
                           class="form-control"
                           placeholder="0"
                           min="1"
                           required>
                </div>

                <!-- Fournisseur -->
                <div class="col-md-3">
                    <label class="form-label">Fournisseur</label>
                    <select name="fournisseur_id" class="form-select" required>
                        <option value="">Sélectionnez un fournisseur</option>
                        <?php foreach($fournisseur as $f): ?>
                            <option value="<?= $f->getId(); ?>">
                                <?= $f->getNom(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date -->
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date_entree"
                           class="form-control"
                           required>
                </div>
                  <div class="col-md-2">
                    <label class="form-label">Date d'expiration</label>
                    <input type="date" name="date_exp
                           class="form-control"
                           >
                </div>

                <!-- Bouton -->
                <div class="col-md-2">
                    <button type="submit"
                            name="ajouter"
                            class="btn btn-success w-100">
                        <i class="bi bi-check-circle"></i>
                        Ajouter
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="card shadow border-0">
    <div class="card-header bg-success text-white fw-semibold d-flex justify-content-between">
        <span><i class="bi bi-arrow-down-circle"></i> Historique des Entrées</span>
        <span class="badge bg-light text-dark">
            <?php
            $count = Query::CRUD("SELECT COUNT(*) as total FROM entrees")
                     ->fetch(PDO::FETCH_ASSOC)['total'];
            echo $count . " entrées";
            ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tableEntrees" class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Pièce</th>
                        <th class="text-center">Quantité</th>
                        <th>Fournisseur</th>
                        <th>Date</th>
                        <th>Date d'expiration</th>
                         <th>Signature</th>
                         <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $j = 1;

                    $entrees = Query::CRUD("
                        SELECT 
                            e.id,
                            e.quantite,
                            e.date_entree,
                            e.date_exp,
                            e.idusers,
                            p.nom AS piece_nom,
                            f.nom AS fournisseur_nom
                        FROM entrees e
                        JOIN pieces p ON p.id = e.piece_id
                        JOIN fournisseurs f ON f.id = e.id_four
                        ORDER BY e.date_entree DESC
                    ");

                    while($e = $entrees->fetch(PDO::FETCH_ASSOC)):
                        $idusers = intval($e['idusers']);
                        $users = $idusers ? Connexions::keys()->decrypt(Query::CRUD("SELECT * FROM `connexion` WHERE `id`='$idusers'")->fetch(PDO::FETCH_OBJ)->username): '-';
                    ?>

                    <tr>
                        <td><?= $j++; ?></td>

                        <td class="fw-semibold">
                            <?= Piece::keys()->decrypt($e['piece_nom']); ?>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-success">
                                +<?= $e['quantite']; ?>
                            </span>
                        </td>

                        <td>
                            <span class="text-muted">
                                <?= Fournisseur::keys()->decrypt($e['fournisseur_nom']); ?>
                            </span>
                        </td>

                        <td>
                            <?= date("d/m/Y", strtotime($e['date_entree'])); ?>
                        </td>
                        <td>
                            <?= $e['date_exp'] ?>
                        </td>
                        <td>
                            <?= $users ?>
                        </td>
                        <td class="text-end">

                        <!-- Modifier -->
                        <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#edit<?= $e['id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                    
                        <!-- Supprimer -->
                        <a href="index.php?page=entrees&supprimer=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Supprimer cette entrée ?')">
                            <i class="bi bi-trash"></i>
                        </a>

                        </td>
                    </tr>
                    
                    <div class="modal fade" id="edit<?= $e['id'] ?>">
                        <div class="modal-dialog">
                        <div class="modal-content">
                        
                        <form method="post">
                        
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                        
                        <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Modifier entrée</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        
                        <div class="modal-body">
                        
                        <div class="mb-2">
                        <label>Quantité</label>
                        <input type="number" name="quantite"
                        class="form-control"
                        value="<?= $e['quantite'] ?>" required>
                        </div>
                        
                        <div class="mb-2">
                        <label>Date</label>
                        <input type="date" name="date_entree"
                        class="form-control"
                        value="<?= $e['date_entree'] ?>" required>
                        </div>
                        
                        <div class="mb-2">
                        <label>Date expiration</label>
                        <input type="date" name="date_exp"
                        class="form-control"
                        value="<?= $e['date_exp'] ?>">
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

    $(document).ready(function(){
    
    $('#tableEntrees').DataTable({
    
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