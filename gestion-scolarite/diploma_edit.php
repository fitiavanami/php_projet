<?php
$page_title = "Modifier un Diplôme";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$diploma_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($diploma_id <= 0) {
    redirect('diplomas.php');
}

// Get diploma data
$query = "SELECT * FROM diplome WHERE numdip = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $diploma_id);
$stmt->execute();
$diploma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$diploma) {
    redirect('diplomas.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre_dip = sanitizeInput($_POST['titre_dip']);
    
    if (!empty($titre_dip)) {
        try {
            $query = "UPDATE diplome SET titre_dip = :titre_dip WHERE numdip = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':titre_dip', $titre_dip);
            $stmt->bindParam(':id', $diploma_id);
            
            if ($stmt->execute()) {
                $message = "Diplôme modifié avec succès!";
                // Refresh diploma data
                $stmt = $db->prepare("SELECT * FROM diplome WHERE numdip = :id");
                $stmt->bindParam(':id', $diploma_id);
                $stmt->execute();
                $diploma = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Erreur lors de la modification du diplôme.";
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
            <h1 class="card-title">Modifier le Diplôme</h1>
            <p>Modifier les informations du diplôme <?php echo htmlspecialchars($diploma['titre_dip']); ?></p>
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
                       value="<?php echo htmlspecialchars($diploma['titre_dip']); ?>" required>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Sauvegarder les modifications</button>
                <a href="diplomas.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
