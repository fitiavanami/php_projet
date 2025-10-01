<?php
$page_title = "Gestion des Matières";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with search and join with classe
$query = "SELECT m.*, c.nom as classe_nom, c.promotion, COUNT(e.id) as nb_enseignements 
          FROM matiere m 
          LEFT JOIN classe c ON m.codecl = c.codecl 
          LEFT JOIN enseignement e ON m.codemat = e.codemat 
          WHERE 1=1";

$params = array();

if (!empty($search)) {
    $query .= " AND (m.nommat LIKE :search OR c.nom LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " GROUP BY m.codemat ORDER BY m.nommat";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Gestion des Matières</h1>
            <p>Liste des matières enseignées dans l'établissement</p>
        </div>
        
        <!-- Search and Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div class="search-container">
                <form method="GET" style="display: flex; gap: 10px;">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Rechercher une matière..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    <?php if ($search): ?>
                        <a href="subjects.php" class="btn btn-secondary">Réinitialiser</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <a href="subject_add.php" class="btn btn-success">+ Ajouter une matière</a>
        </div>
        
        <!-- Subjects Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php if (count($subjects) > 0): ?>
                <?php foreach ($subjects as $subject): ?>
                    <div style="background: white; border: 2px solid #e1e5e9; border-radius: 12px; padding: 25px; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.05);" 
                         onmouseover="this.style.borderColor='#43e97b'; this.style.transform='translateY(-2px)'" 
                         onmouseout="this.style.borderColor='#e1e5e9'; this.style.transform='translateY(0)'">
                        
                        <div style="margin-bottom: 15px;">
                            <h3 style="color: #333; margin: 0 0 10px 0; font-size: 20px;"><?php echo htmlspecialchars($subject['nommat']); ?></h3>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span style="background: #43e97b; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <?php echo htmlspecialchars($subject['classe_nom']); ?>
                                </span>
                                <span style="color: #666; font-size: 14px;">
                                    Promotion <?php echo $subject['promotion']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <p style="margin: 8px 0; color: #666;">
                                <strong>Enseignements:</strong> 
                                <span style="background: #667eea; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                    <?php echo $subject['nb_enseignements']; ?>
                                </span>
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a href="subject_edit.php?id=<?php echo $subject['codemat']; ?>" class="btn btn-warning" style="flex: 1; text-align: center;">Modifier</a>
                            <a href="subject_delete.php?id=<?php echo $subject['codemat']; ?>" 
                               class="btn btn-danger" style="flex: 1; text-align: center;"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')">Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
                    <h3>Aucune matière trouvée</h3>
                    <?php if ($search): ?>
                        <p>pour "<?php echo htmlspecialchars($search); ?>"</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong>Total: <?php echo count($subjects); ?> matière(s)</strong>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
