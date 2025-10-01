<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$diploma_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($diploma_id <= 0) {
    redirect('diplomas.php');
}

// Get diploma info
$query = "SELECT titre_dip FROM diplome WHERE numdip = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $diploma_id);
$stmt->execute();
$diploma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$diploma) {
    redirect('diplomas.php');
}

try {
    // Check if there are students with this diploma
    $query = "SELECT COUNT(*) as count FROM eleve_diplome WHERE numdip = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $diploma_id);
    $stmt->execute();
    $student_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($student_count > 0) {
        $_SESSION['message'] = "Impossible de supprimer le diplôme " . $diploma['titre_dip'] . " car " . $student_count . " étudiant(s) l'ont obtenu.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Delete the diploma
        $query = "DELETE FROM diplome WHERE numdip = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $diploma_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Le diplôme " . $diploma['titre_dip'] . " a été supprimé avec succès.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression du diplôme.";
            $_SESSION['message_type'] = 'error';
        }
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur: " . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

redirect('diplomas.php');
?>
