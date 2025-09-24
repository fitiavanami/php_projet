<?php
$page_title = "Modifier une Matière";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$subject_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($subject_id <= 0) {
    redirect('subjects.php');
}

// Get subject data
$query = "SELECT * FROM matiere WHERE codemat = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $subject_id);
$stmt->execute();
$subject = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subject) {
    redirect('subjects.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nommat = sanitizeInput($_POST['nommat']);
    $codecl = sanitizeInput($_POST['codecl']);
    
    if (!empty($nommat) && !empty($codecl)) {
        try {
            $query = "UPDATE matiere SET nommat = :nommat, codecl = :codecl WHERE codemat = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nommat', $nommat);
            $stmt->bindParam(':codecl', $codecl);
            $stmt->bindParam(':id', $subject_id);
            
            if ($stmt->execute()) {
                $message = "Matière modifiée avec succès!";
                // Refresh subject data
                $stmt = $db->prepare("SELECT * FROM matiere WHERE codemat = :id");
                $stmt->bindParam(':id', $subject_id);
                $stmt->execute();
                $subject = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Erreur lors de la modification de la matière.";
            }
        } catch (PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Get classes for dropdown
$class_query = "SELECT * FROM classe ORDER BY nom, promotion";
$class_stmt = $db->prepare($class_query);
$class_stmt->execute();
$classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Modifier la Matière</h1>
            <p>Modifier les informations de <?php echo htmlspecialchars($subject['nommat']); ?></p>
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
                    <label class="form-label" for="nommat">Nom de la matière *</label>
                    <input type="text" id="nommat" name="nommat" class="form-control" 
                           value="<?php echo htmlspecialchars($subject['nommat']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="codecl">Classe *</label>
                    <select id="codecl" name="codecl" class="form-control" required>
                        <option value="">Sélectionner une classe</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['codecl']; ?>" 
                                    <?php echo ($subject['codecl'] == $class['codecl']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($class['nom']) . ' - Promotion ' . $class['promotion']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Sauvegarder les modifications</button>
                <a href="subjects.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
