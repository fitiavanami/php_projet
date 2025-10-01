<?php
$page_title = "Gestion des Stages";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with search and join with eleve
$query = "SELECT s.*, e.nomel, e.prenomel, c.nom as classe_nom 
          FROM stage s 
          LEFT JOIN eleve e ON s.numel = e.numel 
          LEFT JOIN classe c ON e.codecl = c.codecl 
          WHERE 1=1";

$params = array();

if (!empty($search)) {
    $query .= " AND (s.lieu_stage LIKE :search OR e.nomel LIKE :search OR e.prenomel LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY s.date_debut DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$stages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Gestion des Stages</h1>
            <p>Liste des stages effectués par les étudiants</p>
        </div>
        
        <!-- Search -->
        <div style="margin-bottom: 20px;">
            <form method="GET" style="display: flex; gap: 10px; max-width: 500px;">
                <div class="search-box" style="flex: 1;">
                    <input type="text" name="search" placeholder="Rechercher un stage..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Rechercher</button>
                <?php if ($search): ?>
                    <a href="stages.php" class="btn btn-secondary">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Stages Table -->
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Classe</th>
                        <th>Lieu de stage</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($stages) > 0): ?>
                        <?php foreach ($stages as $stage): ?>
                            <?php
                            $date_debut = new DateTime($stage['date_debut']);
                            $date_fin = new DateTime($stage['date_fin']);
                            $duree = $date_debut->diff($date_fin)->days;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($stage['prenomel'] . ' ' . $stage['nomel']); ?></strong>
                                </td>
                                <td>
                                    <span style="background: #667eea; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                        <?php echo htmlspecialchars($stage['classe_nom']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($stage['lieu_stage']); ?></td>
                                <td><?php echo htmlspecialchars($stage['date_debut']); ?></td>
                                <td><?php echo htmlspecialchars($stage['date_fin']); ?></td>
                                <td>
                                    <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                        <?php echo $duree; ?> jours
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                Aucun stage trouvé
                                <?php if ($search): ?>
                                    pour "<?php echo htmlspecialchars($search); ?>"
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong>Total: <?php echo count($stages); ?> stage(s)</strong>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
