<?php
session_start();
include '../outils/DBConnexion.php'; 


try {
    $mysql = new MySQL('projet2', str_replace(".", "-", $_SERVER["SERVER_NAME"]) . ".php");
    $mysql->connexion();
    $mysql->selectionneBD();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


if (!isset($_GET['NoAnnonce'])) {
    die("Numéro d'annonce manquant.");
}

$noAnnonce = $_GET['NoAnnonce'];


$query = "SELECT a.*, u.Nom, u.Prenom, u.NoTelTravail FROM annonces a 
          JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur 
          WHERE a.NoAnnonce = ?";
$stmt = $mysql->cBD->prepare($query);
$stmt->bind_param('i', $noAnnonce);
$stmt->execute();
$result = $stmt->get_result();
$annonce = $result->fetch_assoc();

if (!$annonce) {
    die("Annonce introuvable.");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'oui') {
    $deleteQuery = "DELETE FROM annonces WHERE NoAnnonce = ?";
    $deleteStmt = $mysql->cBD->prepare($deleteQuery);
    $deleteStmt->bind_param('i', $noAnnonce);

    if ($deleteStmt->execute()) {
        header("Location: gestion_annonce.php");
        exit();
    } else {
        echo "Erreur lors de la suppression de l'annonce : " . $deleteStmt->error;
    }

    $deleteStmt->close();
}

$stmt->close();
$mysql->deconnexion();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression d'une annonce</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Voulez-vous vraiment supprimer cette annonce : <strong><?php echo htmlspecialchars($annonce['DescriptionAbregee']); ?></strong> ?</h2>
    <form method="post">
        <button type="submit" name="confirm" value="oui" class="btn btn-danger">Oui</button>
        <a href="gestion_annonce.php" class="btn btn-primary">Non</a>
    </form>

    <div class="mt-4">
        <img src="../photos/<?php echo htmlspecialchars($annonce['Photo']); ?>" alt="Image de l'annonce" class="img-fluid">
        <p><?php echo htmlspecialchars($annonce['DescriptionComplete']); ?></p>
        <ul>
            <li><strong>Numéro :</strong> <?php echo $annonce['NoAnnonce']; ?></li>
            <li><strong>Auteur :</strong> <?php echo htmlspecialchars($annonce['Nom']) . ', ' . htmlspecialchars($annonce['Prenom']); ?></li>
            <li><strong>Prix :</strong> <?php echo number_format($annonce['Prix'], 2, ',', ' ') . " $"; ?></li>
            <li><strong>Date parution :</strong> <?php echo date('Y-m-d H:i:s', strtotime($annonce['Parution'])); ?></li>
            <li><strong>Date modification :</strong> <?php echo date('Y-m-d H:i:s', strtotime($annonce['MiseAJour'])); ?></li>
        </ul>
        <h3>Contact :</h3>
        <p><strong>Téléphone travail :</strong> <?php echo htmlspecialchars($annonce['NoTelTravail']); ?></p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
