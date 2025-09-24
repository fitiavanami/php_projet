<?php
$page_title = "Modifier un Étudiant";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    redirect('students.php');
}

// Get student data
$query = "SELECT * FROM eleve WHERE numel = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    redirect('students.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = sanitizeInput($_POST['nom']);
    $prenom = sanitizeInput($_POST['prenom']);
    $date_naissance = sanitizeInput($_POST['date_naissance']);
    $adresse = sanitizeInput($_POST['adresse']);
    $telephone = sanitizeInput($_POST['telephone']);
    $codecl = sanitizeInput($_POST['codecl']);
    
    if (!empty($nom) && !empty($prenom) && !empty($date_naissance) && !empty($codecl)) {
        try {
            $query = "UPDATE eleve SET nomel = :nom, prenomel = :prenom, date_naissance = :date_naissance, 
                      adresse = :adresse, telephone = :telephone, codecl = :codecl WHERE numel = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':date_naissance', $date_naissance);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':codecl', $codecl);
            $stmt->bindParam(':id', $student_id);
            
            if ($stmt->execute()) {
                $message = "Étudiant modifié avec succès!";
                // Refresh student data
                $stmt = $db->prepare("SELECT * FROM eleve WHERE numel = :id");
                $stmt->bindParam(':id', $student_id);
                $stmt->execute();
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Erreur lors de la modification de l'étudiant.";
            }
        } catch (PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Get classes for dropdown
$class_query = "SELECT * FROM classe ORDER BY nom";
$class_stmt = $db->prepare($class_query);
$class_stmt->execute();
$classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Modifier l'Étudiant</h1>
            <p>Modifier les informations de <?php echo htmlspecialchars($student['prenomel'] . ' ' . $student['nomel']); ?></p>
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
                           value="<?php echo htmlspecialchars($student['nomel']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" 
                           value="<?php echo htmlspecialchars($student['prenomel']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="date_naissance">Date de naissance *</label>
                    <input type="date" id="date_naissance" name="date_naissance" class="form-control" 
                           value="<?php echo htmlspecialchars($student['date_naissance']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-control" 
                           value="<?php echo htmlspecialchars($student['telephone']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" class="form-control" rows="3"><?php echo htmlspecialchars($student['adresse']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="codecl">Classe *</label>
                <select id="codecl" name="codecl" class="form-control" required>
                    <option value="">Sélectionner une classe</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['codecl']; ?>" 
                                <?php echo ($student['codecl'] == $class['codecl']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['nom']) . ' - Promotion ' . $class['promotion']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Sauvegarder les modifications</button>
                <a href="students.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
