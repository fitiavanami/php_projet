<?php
$page_title = "Gestion des Classes";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with search and join with prof for coordinator name
$query = "SELECT c.*, p.nom as prof_nom, p.prenom as prof_prenom, COUNT(e.numel) as nb_etudiants 
          FROM classe c 
          LEFT JOIN prof p ON c.numprofcoord = p.numprof 
          LEFT JOIN eleve e ON c.codecl = e.codecl 
          WHERE 1=1";

$params = array();

if (!empty($search)) {
    $query .= " AND (c.nom LIKE :search OR c.promotion LIKE :search OR p.nom LIKE :search OR p.prenom LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " GROUP BY c.codecl ORDER BY c.nom, c.promotion";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Gestion des Classes</h1>
            <p>Liste des classes de l'établissement</p>
        </div>
        
        <!-- Search and Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div class="search-container">
                <form method="GET" style="display: flex; gap: 10px;">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Rechercher une classe..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    <?php if ($search): ?>
                        <a href="classes.php" class="btn btn-secondary">Réinitialiser</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <a href="class_add.php" class="btn btn-success">+ Ajouter une classe</a>
        </div>
        
        <!-- Classes Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php if (count($classes) > 0): ?>
                <?php foreach ($classes as $class): ?>
                    <div style="background: white; border: 2px solid #e1e5e9; border-radius: 12px; padding: 25px; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.05);" 
                         onmouseover="this.style.borderColor='#667eea'; this.style.transform='translateY(-2px)'" 
                         onmouseout="this.style.borderColor='#e1e5e9'; this.style.transform='translateY(0)'">
                        
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <h3 style="color: #333; margin: 0; font-size: 24px;"><?php echo htmlspecialchars($class['nom']); ?></h3>
                            <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 500;">
                                <?php echo $class['promotion']; ?>
                            </span>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <p style="margin: 8px 0; color: #666;">
                                <strong>Coordinateur:</strong> 
                                <?php if ($class['prof_nom']): ?>
                                    <?php echo htmlspecialchars($class['prof_prenom'] . ' ' . $class['prof_nom']); ?>
                                <?php else: ?>
                                    <span style="color: #dc3545;">Non assigné</span>
                                <?php endif; ?>
                            </p>
                            <p style="margin: 8px 0; color: #666;">
                                <strong>Étudiants:</strong> 
                                <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                    <?php echo $class['nb_etudiants']; ?>
                                </span>
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a href="class_edit.php?id=<?php echo $class['codecl']; ?>" class="btn btn-warning" style="flex: 1; text-align: center;">Modifier</a>
                            <a href="class_delete.php?id=<?php echo $class['codecl']; ?>" 
                               class="btn btn-danger" style="flex: 1; text-align: center;"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')">Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
                    <h3>Aucune classe trouvée</h3>
                    <?php if ($search): ?>
                        <p>pour "<?php echo htmlspecialchars($search); ?>"</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong>Total: <?php echo count($classes); ?> classe(s)</strong>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
