<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = sanitizeInput($_POST['pseudo']);
    $password = sanitizeInput($_POST['password']);
    
    if (!empty($pseudo) && !empty($password)) {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM login WHERE pseudo = :pseudo AND passe = :password";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $user['Num'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['username'] = $user['pseudo'];
            
            redirect('index.php');
        } else {
            $error = 'Pseudo ou mot de passe incorrect';
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion de Scolarité</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">Gestion de Scolarité</h1>
                <p class="login-subtitle">Connectez-vous à votre compte</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="pseudo">Pseudo :</label>
                    <input type="text" id="pseudo" name="pseudo" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
