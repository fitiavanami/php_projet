<?php
$page_title = "Gestion des Enseignements";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with search and joins
$query = "SELECT e.*, c.nom as classe_nom, c.promotion, m.nommat, p.nom as prof_nom, p.prenom as prof_prenom, s.date_debut, s.date_fin 
          FROM enseignement e 
          LEFT JOIN classe c ON e.codecl = c.codecl 
          LEFT JOIN matiere m ON e.codemat = m.codemat 
          LEFT JOIN prof p ON e.numprof = p.numprof 
          LEFT JOIN semestre s ON e.numsem = s.numsem 
          WHERE 1=1";

$params = array();

if (!empty($search)) {
    $query .= " AND (m.nommat LIKE :search OR c.nom LIKE :search OR p.nom LIKE :search OR p.prenom LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY c.nom, m.nommat";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$teachings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Gestion des Enseignements</h1>
            <p>Liste des enseignements par classe et matière</p>
        </div>
        
        <!-- Search -->
        <div style="margin-bottom: 20px;">
            <form method="GET" style="display: flex; gap: 10px; max-width: 500px;">
                <div class="search-box" style="flex: 1;">
                    <input type="text" name="search" placeholder="Rechercher un enseignement..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Rechercher</button>
                <?php if ($search): ?>
                    <a href="teaching.php" class="btn btn-secondary">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Teaching Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px;">
            <?php if (count($teachings) > 0): ?>
                <?php foreach ($teachings as $teaching): ?>
                    <div style="background: white; border: 2px solid #e1e5e9; border-radius: 12px; padding: 25px; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.05);" 
                         onmouseover="this.style.borderColor='#4facfe'; this.style.transform='translateY(-2px)'" 
                         onmouseout="this.style.borderColor='#e1e5e9'; this.style.transform='translateY(0)'">
                        
                        <div style="margin-bottom: 15px;">
                            <h3 style="color: #333; margin: 0 0 8px 0; font-size: 20px;"><?php echo htmlspecialchars($teaching['nommat']); ?></h3>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span style="background: #4facfe; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    <?php echo htmlspecialchars($teaching['classe_nom']); ?>
                                </span>
                                <span style="color: #666; font-size: 14px;">
                                    Promotion <?php echo $teaching['promotion']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <p style="margin: 5px 0; color: #666;">
                                <strong>Enseignant:</strong> 
                                <?php echo htmlspecialchars($teaching['prof_prenom'] . ' ' . $teaching['prof_nom']); ?>
                            </p>
                            <p style="margin: 5px 0; color: #666;">
                                <strong>Semestre:</strong> 
                                <span style="background: #ffc107; color: #333; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                    S<?php echo $teaching['numsem']; ?>
                                </span>
                            </p>
                            <?php if ($teaching['date_debut'] && $teaching['date_fin']): ?>
                                <p style="margin: 5px 0; color: #666; font-size: 14px;">
                                    <strong>Période:</strong> <?php echo htmlspecialchars($teaching['date_debut']); ?> - <?php echo htmlspecialchars($teaching['date_fin']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #666;">
                    <h3>Aucun enseignement trouvé</h3>
                    <?php if ($search): ?>
                        <p>pour "<?php echo htmlspecialchars($search); ?>"</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong>Total: <?php echo count($teachings); ?> enseignement(s)</strong>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
