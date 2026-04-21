<?php


// Ajouter utilisateur
if(isset($_POST['ajouter'])){
    $nom = Query::securisation($_POST['nom']);
    $role = Query::securisation($_POST['role']);

    (new Utilisateur(null, $nom, $role))->ajouter(); 
    header("Location:index.php?page=utilisateurs");
}

// Liste utilisateurs
$users = Utilisateur::affichers("SELECT * FROM utilisateurs");
?>

<h2 class="mb-4">👤 Gestion des Utilisateurs</h2>

<form method="post" class="mb-4 row g-2">
    <div class="col-md-6">
        <input type="text" name="nom" class="form-control" placeholder="Nom" required>
    </div>
    <div class="col-md-4">
        <select name="role" class="form-select" required>
            <option value="">Rôle</option>
            <option value="magasinier">Magasinier</option>
            <option value="direction">Direction</option>
            <option value="responsable">Responsable</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" name="ajouter" class="btn btn-success w-100">Ajouter</button>
    </div>
</form>

<table class="table table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $j = 1;
         foreach($users as $u): ?>
        <tr>
            <td><?php echo $j++; ?></td>
            <td><?php echo $u->getNom(); ?></td>
            <td><?php echo $u->getRole(); ?></td>
            <td>
                <a href="modifier_utilisateur.php?id=<?php echo $u->getId(); ?>" class="btn btn-primary btn-sm">✏️ Modifier</a>
                <a href="supprimer_utilisateur.php?id=<?php echo $u->getId(); ?>" class="btn btn-danger btn-sm">🗑 Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>
</body>
</html>