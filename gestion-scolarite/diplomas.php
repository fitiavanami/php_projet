<?php
$page_title = "Gestion des Diplômes";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with search
$query = "SELECT d.*, COUNT(ed.id) as nb_etudiants 
          FROM diplome d 
          LEFT JOIN eleve_diplome ed ON d.numdip = ed.numdip 
          WHERE 1=1";

$params = array();

if (!empty($search)) {
    $query .= " AND d.titre_dip LIKE :search";
    $params[':search'] = "%$search%";
}

$query .= " GROUP BY d.numdip ORDER BY d.titre_dip";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$diplomas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Gestion des Diplômes</h1>
            <p>Liste des diplômes délivrés par l'établissement</p>
        </div>
        
        <!-- Search and Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div class="search-container">
                <form method="GET" style="display: flex; gap: 10px;">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Rechercher un diplôme..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    <?php if ($search): ?>
                        <a href="diplomas.php" class="btn btn-secondary">Réinitialiser</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <a href="diploma_add.php" class="btn btn-success">+ Ajouter un diplôme</a>
        </div>
        
        <!-- Diplomas Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php if (count($diplomas) > 0): ?>
                <?php foreach ($diplomas as $diploma): ?>
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 12px; padding: 25px; transition: all 0.3s ease; box-shadow: 0 5px 20px rgba(240, 147, 251, 0.3);" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(240, 147, 251, 0.4)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(240, 147, 251, 0.3)'">
                        
                        <div style="margin-bottom: 20px;">
                            <h3 style="color: white; margin: 0 0 15px 0; font-size: 22px; font-weight: 600;">
                                <?php echo htmlspecialchars($diploma['titre_dip']); ?>
                            </h3>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 20px; font-size: 14px;">
                                    🎓 Diplôme
                                </span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 25px;">
                            <p style="margin: 0; color: rgba(255,255,255,0.9);">
                                <strong>Étudiants diplômés:</strong> 
                                <span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 12px; font-size: 14px; margin-left: 8px;">
                                    <?php echo $diploma['nb_etudiants']; ?>
                                </span>
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a href="diploma_edit.php?id=<?php echo $diploma['numdip']; ?>" 
                               style="flex: 1; text-align: center; background: rgba(255,255,255,0.2); color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s ease;"
                               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                Modifier
                            </a>
                            <a href="diploma_delete.php?id=<?php echo $diploma['numdip']; ?>" 
                               style="flex: 1; text-align: center; background: rgba(220, 53, 69, 0.8); color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s ease;"
                               onmouseover="this.style.background='rgba(220, 53, 69, 1)'"
                               onmouseout="this.style.background='rgba(220, 53, 69, 0.8)'"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce diplôme ?')">
                                Supprimer
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
                    <h3>Aucun diplôme trouvé</h3>
                    <?php if ($search): ?>
                        <p>pour "<?php echo htmlspecialchars($search); ?>"</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong>Total: <?php echo count($diplomas); ?> diplôme(s)</strong>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
