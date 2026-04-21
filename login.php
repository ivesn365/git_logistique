<?php
session_start();
require_once "header.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = Query::securisation($_POST['email']) ?? '';
    $password = Query::securisation($_POST['password']) ?? '';

    $user = Connexions::login($email, $password);

    if ($user) {
        $_SESSION['role'] = $user->getRole();
        $_SESSION['id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        header("Location: index.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion - Logistique Pro</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #1e293b, #2563eb);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', sans-serif;
}

.login-card {
    width: 100%;
    max-width: 400px;
    background: white;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.login-title {
    font-weight: 600;
    margin-bottom: 25px;
    text-align: center;
}

.btn-primary {
    background: #2563eb;
    border: none;
}

.btn-primary:hover {
    background: #1d4ed8;
}
</style>
</head>

<body>

<div class="login-card">

    <div class="text-center mb-3">
        <i class="bi bi-box-seam fs-1 text-primary"></i>
    </div>

    <h4 class="login-title">Connexion CCET Logistique</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label>Nom d'utilisateur</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="text" name="email" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>

        <div class="d-grid">
            <button class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
        </div>

    </form>

    <div class="text-center mt-4 text-muted small">
        © <?= date("Y") ?> CCET Logistique 
    </div>

</div>

</body>
</html>
