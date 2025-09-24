<?php
$page_title = "Modifier une Classe";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$class_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($class_id <= 0) {
    redirect('classes.php');
}

// Get class data
$query = "SELECT * FROM classe WHERE codecl = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $class_id);
$stmt->execute();
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    redirect('classes.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = sanitizeInput($_POST['nom']);
    $promotion = sanitizeInput($_POST['promotion']);
    $numprofcoord = sanitizeInput($_POST['numprofcoord']);
    
    if (!empty($nom) && !empty($promotion)) {
        try {
            $query = "UPDATE classe SET nom = :nom, promotion = :promotion, numprofcoord = :numprofcoord WHERE codecl = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':promotion', $promotion);
            $stmt->bindParam(':numprofcoord', $numprofcoord);
            $stmt->bindParam(':id', $class_id);
            
            if ($stmt->execute()) {
                $message = "Classe modifiée avec succès!";
                // Refresh class data
                $stmt = $db->prepare("SELECT * FROM classe WHERE codecl = :id");
                $stmt->bindParam(':id', $class_id);
                $stmt->execute();
                $class = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Erreur lors de la modification de la classe.";
            }
        } catch (PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir au moins le nom et la promotion.";
    }
}

// Get teachers for dropdown
$teacher_query = "SELECT * FROM prof ORDER BY nom, prenom";
$teacher_stmt = $db->prepare($teacher_query);
$teacher_stmt->execute();
$teachers = $teacher_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Modifier la Classe</h1>
            <p>Modifier les informations de la classe <?php echo htmlspecialchars($class['nom']); ?></p>
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
                    <label class="form-label" for="nom">Nom de la classe *</label>
                    <input type="text" id="nom" name="nom" class="form-control" 
                           value="<?php echo htmlspecialchars($class['nom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="promotion">Promotion *</label>
                    <input type="number" id="promotion" name="promotion" class="form-control" 
                           value="<?php echo htmlspecialchars($class['promotion']); ?>" 
                           min="2000" max="2050" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="numprofcoord">Professeur coordinateur</label>
                    <select id="numprofcoord" name="numprofcoord" class="form-control">
                        <option value="">Sélectionner un coordinateur</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?php echo $teacher['numprof']; ?>" 
                                    <?php echo ($class['numprofcoord'] == $teacher['numprof']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($teacher['prenom'] . ' ' . $teacher['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Sauvegarder les modifications</button>
                <a href="classes.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
