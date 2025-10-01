<?php
$page_title = "Ajouter un Diplôme";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre_dip = sanitizeInput($_POST['titre_dip']);
    
    if (!empty($titre_dip)) {
        try {
            $query = "INSERT INTO diplome (titre_dip) VALUES (:titre_dip)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':titre_dip', $titre_dip);
            
            if ($stmt->execute()) {
                $message = "Diplôme ajouté avec succès!";
                $titre_dip = '';
            } else {
                $error = "Erreur lors de l'ajout du diplôme.";
            }
        } catch (PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir le titre du diplôme.";
    }
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Ajouter un Diplôme</h1>
            <p>Créer un nouveau diplôme</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="titre_dip">Titre du diplôme *</label>
                <input type="text" id="titre_dip" name="titre_dip" class="form-control" 
                       value="<?php echo isset($titre_dip) ? htmlspecialchars($titre_dip) : ''; ?>" 
                       placeholder="Ex: DUT_GI, DUT_TM, DUT_GRH, Master, Licence, etc." required>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Ajouter le diplôme</button>
                <a href="diplomas.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
