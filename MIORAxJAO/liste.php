<?php
$host = 'localhost';
$dbname = 'gestion';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

     
    $classe = $_GET['class'] ?? 'L1';  

     
    $sql = "SELECT * FROM gest_db WHERE `class` = :classe";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['classe' => $classe]);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste Classe <?= htmlspecialchars($classe) ?></title>
    <link rel="stylesheet" href="liste.css">
</head>
<body>
    <h1>Liste des étudiants <?= htmlspecialchars($classe) ?></h1>

    <?php if (count($etudiants) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénoms</th>
                    <th>Matricule</th>
                    <th>Classe</th>
                    <th>Adresse</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etudiants as $etud): ?>
                    <tr>
                        <td><?= htmlspecialchars($etud['nom']) ?></td>
                        <td><?= htmlspecialchars($etud['prenoms']) ?></td>
                        <td><?= htmlspecialchars($etud['matricule']) ?></td>
                        <td><?= htmlspecialchars($etud['class']) ?></td>
                        <td><?= htmlspecialchars($etud['adress']) ?></td>

                        <td>
    <form method="POST" action="supprimer.php" onsubmit="return confirm('Es-tu sûr de vouloir supprimer cet étudiant ?');">
        <input type="hidden" name="matricule" value="<?= htmlspecialchars($etud['matricule']) ?>">
        <input type="submit" value="Supprimer" style="color:blue;border:none;padding:5px 10px;cursor:pointer;">
    </form>
</td>


                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">Aucun étudiant trouvé pour cette classe.</p>
    <?php endif; ?>
    <button><a href="index.html">retour</a></button>
</body>
</html>
