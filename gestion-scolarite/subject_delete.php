<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$subject_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($subject_id <= 0) {
    redirect('subjects.php');
}

// Get subject info
$query = "SELECT nommat FROM matiere WHERE codemat = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $subject_id);
$stmt->execute();
$subject = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subject) {
    redirect('subjects.php');
}

try {
    // Check if there are teachings for this subject
    $query = "SELECT COUNT(*) as count FROM enseignement WHERE codemat = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $subject_id);
    $stmt->execute();
    $teaching_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($teaching_count > 0) {
        $_SESSION['message'] = "Impossible de supprimer la matière " . $subject['nommat'] . " car elle est utilisée dans " . $teaching_count . " enseignement(s).";
        $_SESSION['message_type'] = 'error';
    } else {
        // Delete related records first
        $query = "DELETE FROM devoir WHERE codemat = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $subject_id);
        $stmt->execute();
        
        $query = "DELETE FROM bulletin WHERE codemat = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $subject_id);
        $stmt->execute();
        
        // Delete the subject
        $query = "DELETE FROM matiere WHERE codemat = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $subject_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "La matière " . $subject['nommat'] . " a été supprimée avec succès.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression de la matière.";
            $_SESSION['message_type'] = 'error';
        }
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur: " . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

redirect('subjects.php');
?>
