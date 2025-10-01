<?php
$page_title = "Ajouter une Matière";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nommat = sanitizeInput($_POST['nommat']);
    $codecl = sanitizeInput($_POST['codecl']);
    
    if (!empty($nommat) && !empty($codecl)) {
        try {
            $query = "INSERT INTO matiere (nommat, codecl) VALUES (:nommat, :codecl)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nommat', $nommat);
            $stmt->bindParam(':codecl', $codecl);
            
            if ($stmt->execute()) {
                $message = "Matière ajoutée avec succès!";
                $nommat = $codecl = '';
            } else {
                $error = "Erreur lors de l'ajout de la matière.";
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
            <h1 class="card-title">Ajouter une Matière</h1>
            <p>Créer une nouvelle matière d'enseignement</p>
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
                           value="<?php echo isset($nommat) ? htmlspecialchars($nommat) : ''; ?>" 
                           placeholder="Ex: Mathématiques, Programmation, etc." required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="codecl">Classe *</label>
                    <select id="codecl" name="codecl" class="form-control" required>
                        <option value="">Sélectionner une classe</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['codecl']; ?>" 
                                    <?php echo (isset($codecl) && $codecl == $class['codecl']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($class['nom']) . ' - Promotion ' . $class['promotion']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Ajouter la matière</button>
                <a href="subjects.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
