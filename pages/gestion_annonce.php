<?php
session_start();
include '../outils/DBConnexion.php'; 

// Create MySQL connection object
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
    $prix = $_POST['prix'] ?? 0; // Default price to 0 if not set
    $photo = $_FILES['photo']['name'] ?? '';

   
    if ($photo) {
        $targetDir = "../photos/"; // Ensure the directory exists and is writable
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


$query = "SELECT a.NoAnnonce, a.Parution, a.Categorie, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat 
          FROM annonces a 
          WHERE a.Etat = 1 
          ORDER BY a.Parution DESC 
          LIMIT 10";
$result = $mysql->cBD->query($query);

if ($result === false) {
    die("Erreur lors de l'exécution de la requête : " . $mysql->cBD->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des Annonces</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 20px; }
        table { width: 100%; }
        img { max-width: 100px; height: auto; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
<header class="mb-4">
    <h1 class="text-center my-3">Annonces</h1>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a href="gestion_annonce.php" class="nav-link">Annonces</a></li>
            <li class="nav-item"><a href="afficher_annonces.php" class="nav-link">Gestion de vos annonces</a></li>
            <li class="nav-item"><a href="modification_profil.php" class="nav-link">Modification du profil</a></li>
            <li class="nav-item"><a href="deconnexion.php" class="nav-link">Déconnexion (test@test.test)</a></li>
        </ul>
    </nav>
</header>

<main class="container">
    <section id="annonces-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Date de parution</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['NoAnnonce']); ?></td>
                            <td><img src="../photos/<?php echo htmlspecialchars($row['Photo']); ?>" alt="Image de l'annonce"></td>
                            <td><a href="#"><?php echo htmlspecialchars($row['DescriptionAbregee']); ?></a></td>
                            <td><?php echo htmlspecialchars($row['Categorie']); ?></td>
                            <td><?php echo $row['Prix'] !== null ? number_format($row['Prix'], 2, ',', ' ') . " $" : 'N/A'; ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($row['Parution'])); ?></td>
                            <td><?php echo $row['Etat'] == 1 ? 'Actif' : 'Inactif'; ?></td>
                            <td>
                                <a href="modifier_annonce.php?NoAnnonce=<?php echo $row['NoAnnonce']; ?>" class="btn btn-success">Modification</a>
                                <a href="supprimer_annonce.php?NoAnnonce=<?php echo $row['NoAnnonce']; ?>" class="btn btn-danger">Retrait</a>
                                <button class="btn btn-secondary">Désactiver</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucune annonce trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <h2>Ajouter une Annonce</h2>
    <form action="gestion_annonce.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="categorie">Catégorie :</label>
            <select name="categorie" id="categorie" class="form-control" required>
                <option value="Location">Location</option>
                <option value="Recherche">Recherche</option>
                <option value="À vendre">À vendre</option>
                <option value="À donner">À donner</option>
                <option value="Service offert">Service offert</option>
                <option value="Autre">Autre</option>
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
    </form>
</main>

<footer class="text-center mt-4">
    <p>&copy; 2024 Les petites annonces GG</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
