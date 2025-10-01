<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    redirect('students.php');
}

// Get student info for confirmation
$query = "SELECT nomel, prenomel FROM eleve WHERE numel = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    redirect('students.php');
}

try {
    // Delete related records first (if any)
    // Delete from bulletin
    $query = "DELETE FROM bulletin WHERE numel = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $student_id);
    $stmt->execute();
    
    // Delete from evaluation
    $query = "DELETE FROM evaluation WHERE numel = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $student_id);
    $stmt->execute();
    
    // Delete from eleve_diplome
    $query = "DELETE FROM eleve_diplome WHERE numel = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $student_id);
    $stmt->execute();
    
    // Delete from stage
    $query = "DELETE FROM stage WHERE numel = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $student_id);
    $stmt->execute();
    
    // Finally delete the student
    $query = "DELETE FROM eleve WHERE numel = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $student_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "L'étudiant " . $student['prenomel'] . " " . $student['nomel'] . " a été supprimé avec succès.";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression de l'étudiant.";
        $_SESSION['message_type'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur: " . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

redirect('students.php');
?>
