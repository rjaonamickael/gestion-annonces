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

$noUtilisateur = 1; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categorie = $_POST['categorie'];
    $descriptionAbregee = $_POST['description_abregee'];
    $descriptionComplete = $_POST['description_complete'];
    $prix = $_POST['prix'] ?? 0; 
    $photo = $_FILES['photo']['name'] ?? '';

    if ($photo) {
        $targetDir = "../photos/";
        $targetFile = $targetDir . basename($photo);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            echo "Erreur lors du téléchargement de la photo.";
            exit();
        }
    }

    $query = "INSERT INTO annonces (NoUtilisateur, Parution, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Etat) 
              VALUES (?, NOW(), ?, ?, ?, ?, ?, 1)";
    $stmt = $mysql->cBD->prepare($query);
    $stmt->bind_param('isssds', $noUtilisateur, $categorie, $descriptionAbregee, $descriptionComplete, $prix, $photo);

    if ($stmt->execute()) {
        header("Location: gestion_annonce.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'annonce : " . $stmt->error;
    }

    $stmt->close();
    $mysql->deconnexion();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Annonce</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter une Annonce</h2>
        <form action="AjoutAnnonce.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="categorie">Catégorie :</label>
                <select name="categorie" id="categorie" class="form-control" required>
                    <option value="1">Location</option>
                    <option value="2">Recherche</option>
                    <option value="3">À vendre</option>
                    <option value="4">À donner</option>
                    <option value="5">Service offert</option>
                    <option value="6">Autre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description_abregee">Description abrégée :</label>
                <input type="text" name="description_abregee" id="description_abregee" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description_complete">Description complète :</label>
                <textarea name="description_complete" id="description_complete" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="prix">Prix :</label>
                <input type="number" name="prix" id="prix" step="0.01" class="form-control">
            </div>
            <div class="form-group">
                <label for="photo">Photo :</label>
                <input type="file" name="photo" id="photo" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Ajouter l'annonce</button>
            <a href="gestion_annonce.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
