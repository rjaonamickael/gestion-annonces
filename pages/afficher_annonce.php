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
    die("Annonce non spécifiée.");
}

$noAnnonce = (int)$_GET['NoAnnonce'];

// Récupérer les détails de l'annonce
$query = "SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Parution, a.Etat, 
                 u.Nom, u.Prenom, c.Description as Categorie 
          FROM annonces a 
          JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur
          JOIN categories c ON a.Categorie = c.NoCategorie
          WHERE a.NoAnnonce = ?";
$stmt = $mysql->cBD->prepare($query);
$stmt->bind_param('i', $noAnnonce);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Annonce non trouvée.");
}

$annonce = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'annonce</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center"><?php echo htmlspecialchars($annonce['DescriptionAbregee']); ?></h1>
    <div class="text-center">
        <img src="../photos/<?php echo htmlspecialchars($annonce['Photo']); ?>" alt="Image de l'annonce" class="img-fluid">
    </div>
    <div class="mt-3">
        <p><strong>Numéro :</strong> <?php echo htmlspecialchars($annonce['NoAnnonce']); ?></p>
        <p><strong>Auteur :</strong> <?php echo htmlspecialchars($annonce['Prenom'] . ' ' . $annonce['Nom']); ?></p>
        <p><strong>Prix :</strong> <?php echo number_format($annonce['Prix'], 2, ',', ' ') . " $"; ?></p>
        <p><strong>Date de parution :</strong> <?php echo date('Y-m-d H:i:s', strtotime($annonce['Parution'])); ?></p>
        <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($annonce['Categorie']); ?></p>
        <p><strong>État :</strong> <?php echo $annonce['Etat'] == 1 ? 'Actif' : 'Inactif'; ?></p>
    </div>
    <a href="afficher_annonces.php" class="btn btn-primary mt-3">Retour aux annonces</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
