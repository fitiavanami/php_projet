<?php
$page_title = "Modifier un Enseignant";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($teacher_id <= 0) {
    redirect('teachers.php');
}

// Get teacher data
$query = "SELECT * FROM prof WHERE numprof = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $teacher_id);
$stmt->execute();
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    redirect('teachers.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = sanitizeInput($_POST['nom']);
    $prenom = sanitizeInput($_POST['prenom']);
    $adresse = sanitizeInput($_POST['adresse']);
    $telephone = sanitizeInput($_POST['telephone']);
    
    if (!empty($nom) && !empty($prenom)) {
        try {
            $query = "UPDATE prof SET nom = :nom, prenom = :prenom, adresse = :adresse, telephone = :telephone WHERE numprof = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':id', $teacher_id);
            
            if ($stmt->execute()) {
                $message = "Enseignant modifié avec succès!";
                // Refresh teacher data
                $stmt = $db->prepare("SELECT * FROM prof WHERE numprof = :id");
                $stmt->bindParam(':id', $teacher_id);
                $stmt->execute();
                $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Erreur lors de la modification de l'enseignant.";
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
            <h1 class="card-title">Modifier l'Enseignant</h1>
            <p>Modifier les informations de <?php echo htmlspecialchars($teacher['prenom'] . ' ' . $teacher['nom']); ?></p>
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
                           value="<?php echo htmlspecialchars($teacher['nom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" 
                           value="<?php echo htmlspecialchars($teacher['prenom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-control" 
                           value="<?php echo htmlspecialchars($teacher['telephone']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" class="form-control" rows="3"><?php echo htmlspecialchars($teacher['adresse']); ?></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Sauvegarder les modifications</button>
                <a href="teachers.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
