<?php
$fournisseurs = Fournisseur::affichers("SELECT * FROM fournisseurs ORDER BY id DESC");
?>

<h2 class="mb-4">🚚 Gestion des Fournisseurs</h2>

<div class="card shadow border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <i class="bi bi-truck"></i> Gestion des Fournisseurs
        </span>
        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalAjouter">
            <i class="bi bi-plus-circle"></i> Ajouter
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Adresse</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php $i=1; foreach($fournisseurs as $f){ ?>
                    <tr>
                        <td><?= $i++; ?></td>

                        <td class="fw-semibold">
                            <?= $f->getNom(); ?>
                        </td>

                        <td><?= $f->getTelephone(); ?></td>

                        <td>
                            <span class="text-muted">
                                <?= $f->getEmail(); ?>
                            </span>
                        </td>

                        <td><?= $f->getAdresse(); ?></td>

                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#edit<?= $f->getId(); ?>">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <a href="index.php?page=fournisseur&supprimer_fournisseur=<?= $f->getId(); ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Supprimer ce fournisseur ?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    </table>
                  </div>
                     </div>
                        </div>

<!-- Modal Ajouter -->
<div class="modal fade" id="modalAjouter">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="action_fournisseur" value="ajouter">

                <div class="modal-header">
                    <h5 class="modal-title">Ajouter Fournisseur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input name="nom" class="form-control mb-2" placeholder="Nom" required>
                    <input name="telephone" class="form-control mb-2" placeholder="Téléphone">
                    <input name="email" class="form-control mb-2" placeholder="Email">
                    <textarea name="adresse" class="form-control" placeholder="Adresse"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php

if(isset($_POST['action_fournisseur'])){

    if($_POST['action_fournisseur'] === 'ajouter'){
        $f = new Fournisseur(
            null,
            Query::securisation($_POST['nom']),
            Query::securisation($_POST['telephone']),
            Query::securisation($_POST['email']),
            Query::securisation($_POST['adresse'])
        );
        $f->create();
    }

    if($_POST['action_fournisseur'] === 'modifier'){
        $f = new Fournisseur(
            $_POST['id'],
            $_POST['nom'],
            $_POST['telephone'],
            $_POST['email'],
            $_POST['adresse']
        );
        $f->modifier();
    }

    header("Location: index.php?page=fournisseur");
    exit;
}

if(isset($_GET['supprimer_fournisseur'])){
    $f = new Fournisseur(Query::securisation($_GET['supprimer_fournisseur']), '', '', '', '');
    $f->supprimer();
    header("Location: index.php?page=fournisseur");
    exit;
}



?>