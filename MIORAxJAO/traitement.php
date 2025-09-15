<?php
 
$host = 'localhost';
$dbname = 'gestion';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
    $nom = $_POST['nom'];
    $prenoms = $_POST['prenoms'];
    $matricule = $_POST['matricule'];
    $class = $_POST['class'];
    $adress = $_POST['adress'];
 
    $sql = "INSERT INTO gest_db (nom, prenoms, matricule, class, adress)
            VALUES (:nom, :prenoms, :matricule, :class, :adress)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':prenoms' => $prenoms,
        ':matricule' => $matricule,
        ':class' => $class,
        ':adress' => $adress
    ]);

    echo "<span style='color: green;'>✅ Inscription réusi !</span>";
} catch (PDOException $e) {
    echo "<span style='color: red;'>❌ Adresse email ou mot de pass invalide : " . $e->getMessage() . "</span>";
}
?>
