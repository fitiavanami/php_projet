<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($teacher_id <= 0) {
    redirect('teachers.php');
}

// Get teacher info
$query = "SELECT nom, prenom FROM prof WHERE numprof = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $teacher_id);
$stmt->execute();
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    redirect('teachers.php');
}

try {
    // Delete related records first
    $query = "DELETE FROM enseignement WHERE numprof = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $teacher_id);
    $stmt->execute();
    
    // Delete the teacher
    $query = "DELETE FROM prof WHERE numprof = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $teacher_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "L'enseignant " . $teacher['prenom'] . " " . $teacher['nom'] . " a été supprimé avec succès.";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression de l'enseignant.";
        $_SESSION['message_type'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur: " . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

redirect('teachers.php');
?>
