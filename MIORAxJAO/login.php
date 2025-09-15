<?php
$host = 'localhost';
$dbname = 'gestion';
$username = 'root';
$password = '';

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $mail = $_POST['mail'];
    $mdp = $_POST['mdp'];

  
    $stmt = $pdo->prepare("SELECT * FROM login_users WHERE mail = :mail AND mdp = :mdp");
    $stmt->execute([
        ':mail' => $mail,
        ':mdp' => $mdp
    ]);

    if ($stmt->rowCount() === 1) {
        
        header("Location: index.html");
        exit();
    } else {
        echo "<span style='color:red;'>Diso mail na mot de passe!</span>";
    }

} catch (PDOException $e) {
    echo "<span style='color:red;'>Erreur: " . $e->getMessage() . "</span>";
}
?>