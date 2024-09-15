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

$noAnnonce = (int)$_GET['NoAnnonce'];


$query = "SELECT * FROM annonces WHERE NoAnnonce = ?";
$stmt = $mysql->cBD->prepare($query);
$stmt->bind_param('i', $noAnnonce);
$stmt->execute();
$result = $stmt->get_result();
$annonce = $result->fetch_assoc();

if (!$annonce) {
    die("Annonce introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categorie = $_POST['categorie'];
    $descriptionAbregee = $_POST['description_abregee'];
    $descriptionComplete = $_POST['description_complete'];
    $prix = $_POST['prix'] ?? 0;
    $etat = $_POST['etat'];
    $photo = $annonce['Photo'];

    
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo']['name'];
        $targetDir = "../photos/";
        $targetFile = $targetDir . basename($photo);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            echo "Erreur lors du téléchargement de la photo.";
            exit();
        }
    }

    
    $updateQuery = "UPDATE annonces SET Categorie = ?, DescriptionAbregee = ?, DescriptionComplete = ?, Prix = ?, Photo = ?, Etat = ?, MiseAJour = NOW() WHERE NoAnnonce = ?";
    $updateStmt = $mysql->cBD->prepare($updateQuery);
    $updateStmt->bind_param('sssdsii', $categorie, $descriptionAbregee, $descriptionComplete, $prix, $photo, $etat, $noAnnonce);

    if ($updateStmt->execute()) {
        header("Location: gestion_annonce.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour de l'annonce : " . $updateStmt->error;
    }

    $updateStmt->close();
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
    <title>Modification d'une annonce</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Modification d'une annonce</h2>
    <form action="modifier_annonce.php?NoAnnonce=<?php echo $noAnnonce; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="categorie">Catégorie :</label>
            <select name="categorie" id="categorie" class="form-control" required>
                <option value="1" <?php echo $annonce['Categorie'] == 1 ? 'selected' : ''; ?>>Location</option>
                <option value="2" <?php echo $annonce['Categorie'] == 2 ? 'selected' : ''; ?>>Recherche</option>
                <option value="3" <?php echo $annonce['Categorie'] == 3 ? 'selected' : ''; ?>>À vendre</option>
                <option value="4" <?php echo $annonce['Categorie'] == 4 ? 'selected' : ''; ?>>À donner</option>
                <option value="5" <?php echo $annonce['Categorie'] == 5 ? 'selected' : ''; ?>>Service offert</option>
                <option value="6" <?php echo $annonce['Categorie'] == 6 ? 'selected' : ''; ?>>Autre</option>
            </select>
        </div>
        <div class="form-group">
            <label for="description_abregee">Petite description :</label>
            <input type="text" name="description_abregee" id="description_abregee" class="form-control" value="<?php echo htmlspecialchars($annonce['DescriptionAbregee']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description_complete">Description longue :</label>
            <textarea name="description_complete" id="description_complete" class="form-control" required><?php echo htmlspecialchars($annonce['DescriptionComplete']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="prix">Prix :</label>
            <input type="number" name="prix" id="prix" step="0.01" class="form-control" value="<?php echo htmlspecialchars($annonce['Prix']); ?>">
        </div>
        <div class="form-group">
            <label for="photo">Sélectionner l'image de l'annonce :</label>
            <input type="file" name="photo" id="photo" class="form-control">
            <?php if ($annonce['Photo']): ?>
                <img src="../photos/<?php echo htmlspecialchars($annonce['Photo']); ?>" alt="Image actuelle" style="max-width: 150px; margin-top: 10px;">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="etat">Actif ?</label>
            <select name="etat" id="etat" class="form-control">
                <option value="1" <?php echo $annonce['Etat'] == 1 ? 'selected' : ''; ?>>Actif</option>
                <option value="0" <?php echo $annonce['Etat'] == 0 ? 'selected' : ''; ?>>Inactif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="gestion_annonce.php" class="btn btn-link">Annuler la modification de l'annonce</a>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
