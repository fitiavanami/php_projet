<?php
$page_title = "Ajouter une Classe";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = sanitizeInput($_POST['nom']);
    $promotion = sanitizeInput($_POST['promotion']);
    $numprofcoord = sanitizeInput($_POST['numprofcoord']);
    
    if (!empty($nom) && !empty($promotion)) {
        try {
            $query = "INSERT INTO classe (nom, promotion, numprofcoord) VALUES (:nom, :promotion, :numprofcoord)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':promotion', $promotion);
            $stmt->bindParam(':numprofcoord', $numprofcoord);
            
            if ($stmt->execute()) {
                $message = "Classe ajoutée avec succès!";
                $nom = $promotion = $numprofcoord = '';
            } else {
                $error = "Erreur lors de l'ajout de la classe.";
            }
        } catch (PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir au moins le nom et la promotion.";
    }
}

$teacher_query = "SELECT * FROM prof ORDER BY nom, prenom";
$teacher_stmt = $db->prepare($teacher_query);
$teacher_stmt->execute();
$teachers = $teacher_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Ajouter une Classe</h1>
            <p>Créer une nouvelle classe</p>
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
                           value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>" 
                           placeholder="Ex: GI, TM, GRH" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="promotion">Promotion *</label>
                    <input type="number" id="promotion" name="promotion" class="form-control" 
                           value="<?php echo isset($promotion) ? htmlspecialchars($promotion) : ''; ?>" 
                           placeholder="Ex: 2024" min="2000" max="2050" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="numprofcoord">Professeur coordinateur</label>
                    <select id="numprofcoord" name="numprofcoord" class="form-control">
                        <option value="">Sélectionner un coordinateur</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?php echo $teacher['numprof']; ?>" 
                                    <?php echo (isset($numprofcoord) && $numprofcoord == $teacher['numprof']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($teacher['prenom'] . ' ' . $teacher['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-success">Ajouter la classe</button>
                <a href="classes.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
