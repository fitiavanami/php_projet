<?php
$page_title = "Gestion des Enseignants";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with search
$query = "SELECT p.*, COUNT(e.id) as nb_enseignements FROM prof p 
          LEFT JOIN enseignement e ON p.numprof = e.numprof 
          WHERE 1=1";

$params = array();

if (!empty($search)) {
    $query .= " AND (p.nom LIKE :search OR p.prenom LIKE :search OR p.adresse LIKE :search OR p.telephone LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " GROUP BY p.numprof ORDER BY p.nom, p.prenom";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Gestion des Enseignants</h1>
            <p>Liste des enseignants de l'établissement</p>
        </div>
        
        <!-- Search and Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div class="search-container">
                <form method="GET" style="display: flex; gap: 10px;">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Rechercher un enseignant..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    <?php if ($search): ?>
                        <a href="teachers.php" class="btn btn-secondary">Réinitialiser</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <a href="teacher_add.php" class="btn btn-success">+ Ajouter un enseignant</a>
        </div>
        
        <!-- Teachers Table -->
        <div style="overflow-x: auto;">
            <table class="table" id="teachersTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Nb. Enseignements</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($teachers) > 0): ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teacher['nom']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['adresse']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['telephone']); ?></td>
                                <td><span class="badge" style="background: #667eea; color: white; padding: 4px 8px; border-radius: 12px;"><?php echo $teacher['nb_enseignements']; ?></span></td>
                                <td>
                                    <a href="teacher_edit.php?id=<?php echo $teacher['numprof']; ?>" class="btn btn-warning" style="margin: 2px;">Modifier</a>
                                    <a href="teacher_delete.php?id=<?php echo $teacher['numprof']; ?>" 
                                       class="btn btn-danger" style="margin: 2px;"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                Aucun enseignant trouvé
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
            <strong>Total: <?php echo count($teachers); ?> enseignant(s)</strong>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
