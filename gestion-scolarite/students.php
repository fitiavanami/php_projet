<?php
$page_title = "Gestion des Étudiants";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle speciality filter
$specialite_filter = isset($_GET['specialite']) ? $_GET['specialite'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with filters
$query = "SELECT e.*, c.nom as classe_nom FROM eleve e 
          LEFT JOIN classe c ON e.codecl = c.codecl 
          WHERE 1=1";

$params = array();

if (!empty($specialite_filter)) {
    $query .= " AND c.nom = :specialite";
    $params[':specialite'] = $specialite_filter;
}

if (!empty($search)) {
    $query .= " AND (e.nomel LIKE :search OR e.prenomel LIKE :search OR e.telephone LIKE :search OR e.adresse LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY e.nomel, e.prenomel";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get classes for the add form
$class_query = "SELECT * FROM classe ORDER BY nom";
$class_stmt = $db->prepare($class_query);
$class_stmt->execute();
$classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">
                Gestion des Étudiants
                <?php if ($specialite_filter): ?>
                    - Spécialité <?php echo htmlspecialchars($specialite_filter); ?>
                <?php endif; ?>
            </h1>
            <p>Liste des étudiants inscrits</p>
        </div>
        
        <!-- Search and Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div class="search-container">
                <form method="GET" style="display: flex; gap: 10px;">
                    <?php if ($specialite_filter): ?>
                        <input type="hidden" name="specialite" value="<?php echo htmlspecialchars($specialite_filter); ?>">
                    <?php endif; ?>
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Rechercher un étudiant..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                    <?php if ($search || $specialite_filter): ?>
                        <a href="students.php" class="btn btn-secondary">Réinitialiser</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <a href="student_add.php" class="btn btn-success">+ Ajouter un étudiant</a>
        </div>
        
        <!-- Students Table -->
        <div style="overflow-x: auto;">
            <table class="table" id="studentsTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date de naissance</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Classe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['nomel']); ?></td>
                                <td><?php echo htmlspecialchars($student['prenomel']); ?></td>
                                <td><?php echo htmlspecialchars($student['date_naissance']); ?></td>
                                <td><?php echo htmlspecialchars($student['adresse']); ?></td>
                                <td><?php echo htmlspecialchars($student['telephone']); ?></td>
                                <td><?php echo htmlspecialchars($student['classe_nom']); ?></td>
                                <td>
                                    <a href="student_edit.php?id=<?php echo $student['numel']; ?>" class="btn btn-warning" style="margin: 2px;">Modifier</a>
                                    <a href="student_delete.php?id=<?php echo $student['numel']; ?>" 
                                       class="btn btn-danger" style="margin: 2px;"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                                Aucun étudiant trouvé
                                <?php if ($search): ?>
                                    pour "<?php echo htmlspecialchars($search); ?>"
                                <?php endif; ?>
                                <?php if ($specialite_filter): ?>
                                    dans la spécialité <?php echo htmlspecialchars($specialite_filter); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong>Total: <?php echo count($students); ?> étudiant(s)</strong>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const table = document.getElementById('studentsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        if (searchInput && table) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    // Skip the "no results" row
                    if (cells.length === 1 && cells[0].getAttribute('colspan')) {
                        continue;
                    }
                    
                    for (let j = 0; j < cells.length - 1; j++) { // -1 to skip actions column
                        if (cells[j].textContent.toLowerCase().includes(filter)) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                }
            });
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
