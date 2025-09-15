<?php
$host = 'localhost';
$dbname = 'gestion';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['matricule'])) {
        $matricule = $_POST['matricule'];

        $sql = "DELETE FROM gest_db WHERE matricule = :matricule";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['matricule' => $matricule]);

        
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        echo "Matricule non défini.";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
