<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$class_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($class_id <= 0) {
    redirect('classes.php');
}

$query = "SELECT nom, promotion FROM classe WHERE codecl = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $class_id);
$stmt->execute();
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    redirect('classes.php');
}

try {
    $query = "SELECT COUNT(*) as count FROM eleve WHERE codecl = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $class_id);
    $stmt->execute();
    $student_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($student_count > 0) {
        $_SESSION['message'] = "Impossible de supprimer la classe " . $class['nom'] . " car elle contient " . $student_count . " étudiant(s).";
        $_SESSION['message_type'] = 'error';
    } else {
    
        $query = "DELETE FROM enseignement WHERE codecl = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $class_id);
        $stmt->execute();
        
        $query = "DELETE FROM matiere WHERE codecl = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $class_id);
        $stmt->execute();
        
        $query = "DELETE FROM classe WHERE codecl = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $class_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "La classe " . $class['nom'] . " a été supprimée avec succès.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression de la classe.";
            $_SESSION['message_type'] = 'error';
        }
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur: " . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

redirect('classes.php');
?>
