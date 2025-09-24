<?php
$page_title = "Ajouter un Enseignant";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = sanitizeInput($_POST['nom']);
    $prenom = sanitizeInput($_POST['prenom']);
    $adresse = sanitizeInput($_POST['adresse']);
    $telephone = sanitizeInput($_POST['telephone']);
    
    if (!empty($nom) && !empty($prenom)) {
        try {
            $query = "INSERT INTO prof (nom, prenom, adresse, telephone) VALUES (:nom, :prenom, :adresse, :telephone)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':telephone', $telephone);
            
            if ($stmt->execute()) {
                $message = "Enseignant ajouté avec succès!";
                $nom = $prenom = $adresse = $telephone = '';
            } else {
                $error = "Erreur lors de l'ajout de l'enseignant.";
            }
        } catch (PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir au moins le nom et le prénom.";
    }
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Ajouter un Enseignant</h1>
            <p>Saisir les informations du nouvel enseignant</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" class="form-control" 
                           value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" 
                           value="<?php echo isset($prenom) ? htmlspecialchars($prenom) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-control" 
                           value="<?php echo isset($telephone) ? htmlspecialchars($telephone) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" class="form-control" rows="3"><?php echo isset($adresse) ? htmlspecialchars($adresse) : ''; ?></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Ajouter l'enseignant</button>
                <a href="teachers.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
