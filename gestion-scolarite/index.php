<?php
$page_title = "Accueil - Gestion de Scolarité";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT COUNT(*) as total FROM eleve";
$stmt = $db->prepare($query);
$stmt->execute();
$total_students = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM prof";
$stmt = $db->prepare($query);
$stmt->execute();
$total_teachers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM classe";
$stmt = $db->prepare($query);
$stmt->execute();
$total_classes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM matiere";
$stmt = $db->prepare($query);
$stmt->execute();
$total_subjects = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<div class="container" style="max-width: 1000px; margin: 40px auto;">
    <div class="card shadow" style="border-radius: 18px; background: #f7f8fc;">
        <div class="card-header" style="background: linear-gradient(90deg, #4e54c8 0%, #8f94fb 100%); color: #fff; padding: 36px 28px; border-radius: 18px 18px 0 0;">
            <h1 style="font-size: 2.2rem; font-weight: 700; margin-bottom: 8px;">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h1>
            <p style="font-size: 1.1rem; opacity: 0.92;">Tableau de bord - Gestion de Scolarité</p>
        </div>
        <div style="display: flex; gap: 24px; justify-content: space-between; flex-wrap: wrap; padding: 32px 28px 18px 28px;">
            <div style="flex: 1; min-width: 180px; background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(78,84,200,0.08); text-align: center; padding: 28px 10px;">
                <div style="font-size: 2.3rem; font-weight: 700; color: #4e54c8;"><?php echo $total_students; ?></div>
                <div style="font-size: 1rem; color: #888;">Étudiants</div>
            </div>
            <div style="flex: 1; min-width: 180px; background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(143,148,251,0.08); text-align: center; padding: 28px 10px;">
                <div style="font-size: 2.3rem; font-weight: 700; color: #8f94fb;"><?php echo $total_teachers; ?></div>
                <div style="font-size: 1rem; color: #888;">Enseignants</div>
            </div>
            <div style="flex: 1; min-width: 180px; background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(67,233,123,0.08); text-align: center; padding: 28px 10px;">
                <div style="font-size: 2.3rem; font-weight: 700; color: #43e97b;"><?php echo $total_classes; ?></div>
                <div style="font-size: 1rem; color: #888;">Classes</div>
            </div>
            <div style="flex: 1; min-width: 180px; background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(38,249,215,0.08); text-align: center; padding: 28px 10px;">
                <div style="font-size: 2.3rem; font-weight: 700; color: #38f9d7;"><?php echo $total_subjects; ?></div>
                <div style="font-size: 1rem; color: #888;">Matières</div>
            </div>
        </div>
        <div style="display: flex; gap: 24px; flex-wrap: wrap; padding: 0 28px 32px 28px;">
            <div style="flex: 1; min-width: 300px; background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 24px 18px;">
                <h3 style="color: #4e54c8; margin-bottom: 16px; font-weight: 600;">Actions Rapides</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="students.php" class="btn btn-primary" style="font-weight: 500; border-radius: 7px; background: linear-gradient(90deg,#4e54c8,#8f94fb); border: none;">Gérer les Étudiants</a>
                    <a href="teachers.php" class="btn btn-success" style="font-weight: 500; border-radius: 7px; background: linear-gradient(90deg,#43e97b,#38f9d7); border: none;">Gérer les Enseignants</a>
                    <a href="classes.php" class="btn btn-warning" style="font-weight: 500; border-radius: 7px; background: linear-gradient(90deg,#f093fb,#f5576c); border: none;">Gérer les Classes</a>
                </div>
            </div>
            <div style="flex: 1; min-width: 300px; background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 24px 18px;">
                <h3 style="color: #4e54c8; margin-bottom: 16px; font-weight: 600;">Informations Système</h3>
                <p style="margin-bottom: 8px;"><strong>Type d'utilisateur:</strong> <?php echo ucfirst(htmlspecialchars($_SESSION['user_type'])); ?></p>
                <p style="margin-bottom: 8px;"><strong>Dernière connexion:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                <p><strong>Statut:</strong> <span style="color: #28a745; font-weight: 600;">Connecté</span></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
